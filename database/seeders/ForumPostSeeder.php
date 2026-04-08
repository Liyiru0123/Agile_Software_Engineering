<?php

namespace Database\Seeders;

use App\Models\ForumPost;
use App\Models\ForumPostAttachment;
use App\Models\ForumTag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumPostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@eaplus.local')->first();

        if (! $admin) {
            $this->command?->warn('Admin user not found. Skipping forum post seed.');

            return;
        }

        $publicTag = ForumTag::query()->firstOrCreate(
            ['slug' => 'public-forum'],
            [
                'user_id' => $admin->id,
                'name' => 'Public Forum',
                'description' => 'Open discussion for general learning reflections, questions, and study updates.',
            ]
        );

        $posts = $this->loadPosts();

        foreach ($posts as $index => $post) {
            if (! isset($post['title'], $post['source'], $post['sections']) || ! is_array($post['sections'])) {
                continue;
            }

            $forumPost = ForumPost::query()->updateOrCreate(
                [
                    'user_id' => $admin->id,
                    'forum_tag_id' => $publicTag->id,
                    'title' => $post['title'],
                ],
                [
                    'source_name' => $post['source'],
                    'body' => $this->buildBody($post['sections']),
                    'view_count' => 0,
                    'is_pinned' => $index === 0,
                    'pinned_at' => $index === 0 ? now() : null,
                ]
            );

            $this->syncImageAttachments($forumPost, $post['sections']);
        }

        $this->command?->info('Forum posts seeded: '.count($posts));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function loadPosts(): array
    {
        $path = database_path('seeders/data/forum_posts_seed_en.json');

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param array<int, array<string, mixed>> $sections
     */
    protected function buildBody(array $sections): string
    {
        $parts = [];
        $imageIndex = 0;

        foreach ($sections as $section) {
            $type = (string) ($section['type'] ?? '');
            $content = trim((string) ($section['content'] ?? ''));
            if ($type === 'heading' && $content !== '') {
                $parts[] = '## '.$content;
                continue;
            }

            if ($type === 'text' && $content !== '') {
                $parts[] = $content;
                continue;
            }

            if ($type === 'image' && filled($section['src'] ?? null)) {
                $parts[] = '[[forum-image:'.$imageIndex.']]';
                $imageIndex++;
            }
        }

        return implode("\n\n", $parts);
    }

    /**
     * @param array<int, array<string, mixed>> $sections
     */
    protected function syncImageAttachments(ForumPost $post, array $sections): void
    {
        $payloads = collect($sections)
            ->filter(fn (array $section) => ($section['type'] ?? null) === 'image' && filled($section['src'] ?? null))
            ->values()
            ->map(function (array $section, int $index) use ($post) {
                $url = trim((string) $section['src']);
                $alt = trim((string) ($section['alt'] ?? ''));
                $originalName = $alt !== ''
                    ? $alt
                    : trim((string) ($section['filename'] ?? ''));

                if ($originalName === '') {
                    $originalName = 'Forum photo';
                }

                return [
                    'forum_post_id' => $post->id,
                    'path' => $url,
                    'original_name' => $originalName,
                    'mime_type' => $this->guessMimeTypeFromUrl($url),
                    'size' => null,
                    'sort_order' => $index,
                ];
            })
            ->all();

        $post->attachments()->get()->each(function (ForumPostAttachment $attachment) {
            $attachment->delete();
        });

        if ($payloads !== []) {
            $post->attachments()->createMany($payloads);
        }
    }

    protected function guessMimeTypeFromUrl(string $url): ?string
    {
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => null,
        };
    }
}
