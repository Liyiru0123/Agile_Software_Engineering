<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use App\Services\ArticleTextProcessor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);

        $content = <<<'TEXT'
Engineers design bridges to carry weight safely across rivers and roads.
They test each structure carefully before it opens to the public.

Good bridge design depends on materials, weather, and long-term maintenance.
Even a simple bridge needs regular inspection to remain safe.
TEXT;

        $processor = app(ArticleTextProcessor::class);

        $article = Article::query()->updateOrCreate([
            'slug' => 'how-bridges-stay-strong',
        ], [
            'subject' => 'Civil Engineering',
            'title' => 'How Bridges Stay Strong',
            'author' => 'Project Seeder',
            'source' => 'Internal Sample',
            'level' => 'Intermediate',
            'resource_type' => 'text',
            'accent' => 'US',
            'word_count' => $processor->countWords($content),
            'total_duration' => 0,
        ]);

        $article->segments()->delete();
        $article->segments()->createMany($processor->buildSegments($content));
    }
}
