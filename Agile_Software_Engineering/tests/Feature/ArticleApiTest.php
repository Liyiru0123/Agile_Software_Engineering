<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_article_and_segments_content(): void
    {
        Storage::fake('public');

        $response = $this->post('/api/articles', [
            'subject' => 'Civil Engineering',
            'title' => 'Bridge Safety Basics',
            'author' => 'Test Author',
            'level' => 'Easy',
            'content' => "Bridges must carry weight safely. Engineers inspect them often.\n\nMaintenance prevents major failures.",
            'accent' => 'US',
            'audio_file' => UploadedFile::fake()->create('bridge.mp3', 128, 'audio/mpeg'),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertCreated()
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.article.title', 'Bridge Safety Basics')
            ->assertJsonPath('data.article.subject', 'Civil Engineering')
            ->assertJsonPath('data.article.has_audio', true)
            ->assertJsonPath('data.reading.paragraphs.0.sentences.0.text', 'Bridges must carry weight safely.');

        $this->assertDatabaseHas('articles', [
            'title' => 'Bridge Safety Basics',
            'slug' => 'bridge-safety-basics',
            'subject' => 'Civil Engineering',
        ]);

        $this->assertDatabaseCount('article_segments', 3);
        Storage::disk('public')->assertExists(Article::query()->firstOrFail()->getRawOriginal('audio_url'));
    }

    public function test_it_returns_grouped_reading_payload(): void
    {
        $article = Article::query()->create([
            'subject' => 'Mechanical Engineering',
            'title' => 'Road Materials',
            'slug' => 'road-materials',
            'author' => 'Seeder',
            'source' => 'Internal',
            'level' => 'Intermediate',
            'resource_type' => 'text',
            'accent' => 'UK',
            'word_count' => 9,
            'total_duration' => 0,
        ]);

        $article->segments()->createMany([
            [
                'paragraph_index' => 0,
                'sentence_index' => 0,
                'content_en' => 'Concrete is strong.',
            ],
            [
                'paragraph_index' => 0,
                'sentence_index' => 1,
                'content_en' => 'Asphalt is flexible.',
            ],
            [
                'paragraph_index' => 1,
                'sentence_index' => 0,
                'content_en' => 'Both materials need maintenance.',
            ],
        ]);

        $response = $this->getJson('/api/articles/'.$article->id.'/reading');

        $response->assertOk()
            ->assertJsonPath('code', 0)
            ->assertJsonPath('data.article_id', $article->id)
            ->assertJsonPath('data.paragraphs.0.text', 'Concrete is strong. Asphalt is flexible.')
            ->assertJsonPath('data.paragraphs.1.sentences.0.text', 'Both materials need maintenance.');
    }
}
