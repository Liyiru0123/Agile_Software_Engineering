<?php

namespace Tests\Feature;

use App\Services\GeminiAudioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiAudioServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_gemini_audio_service_supports_openai_compatible_gateway(): void
    {
        File::delete(storage_path('logs/speaking-ai-raw.log'));

        config([
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-2.5-flash',
            'services.gemini.base_url' => 'https://moyu.info/v1',
        ]);

        Http::fake([
            'https://moyu.info/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score' => 88,
                                'fluency' => [
                                    'score' => 8.5,
                                    'comment' => 'Fluent overall with minor hesitation.',
                                ],
                                'relevance' => [
                                    'score' => 8,
                                    'comment' => 'The response addresses the topic clearly.',
                                ],
                                'pronunciation' => [
                                    'score' => 8.5,
                                    'comment' => 'Pronunciation is clear and easy to understand.',
                                ],
                                'feedback' => 'A clear and understandable speaking response with good overall delivery.',
                            ], JSON_UNESCAPED_SLASHES),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $audio = UploadedFile::fake()->createWithContent('sample.mp3', 'fake-audio-content');

        $result = app(GeminiAudioService::class)->evaluateAudio(
            $audio,
            'Explain the main idea of the article.',
            'The article is about digital learning.',
        );

        $this->assertSame(88.0, $result['score']);
        $this->assertSame(8.5, $result['fluency']['score']);
        $this->assertSame('A clear and understandable speaking response with good overall delivery.', $result['feedback']);
        $this->assertTrue(File::exists(storage_path('logs/speaking-ai-raw.log')));
        $this->assertStringContainsString(
            '"provider":"gemini-compatible"',
            File::get(storage_path('logs/speaking-ai-raw.log'))
        );

        Http::assertSent(function ($request) {
            return $request->url() === 'https://moyu.info/v1/chat/completions'
                && $request['model'] === 'gemini-2.5-flash'
                && data_get($request->data(), 'messages.1.content.1.type') === 'input_audio';
        });
    }

    public function test_gemini_audio_service_supports_shadowing_mode_labels(): void
    {
        config([
            'services.gemini.api_key' => 'test-gemini-key',
            'services.gemini.model' => 'gemini-2.5-flash',
            'services.gemini.base_url' => 'https://moyu.info/v1',
        ]);

        Http::fake([
            'https://moyu.info/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score' => 84,
                                'fluency' => [
                                    'score' => 8,
                                    'comment' => 'The clip is delivered smoothly overall.',
                                ],
                                'accuracy' => [
                                    'score' => 7.5,
                                    'comment' => 'Most key words are repeated correctly with a few omissions.',
                                ],
                                'pronunciation' => [
                                    'score' => 8.2,
                                    'comment' => 'Pronunciation is mostly clear and intelligible.',
                                ],
                                'transcript' => 'Autonomous transit systems could reduce congestion in large cities.',
                                'feedback' => 'Good shadowing attempt. Keep the wording tighter and reduce skipped words.',
                            ], JSON_UNESCAPED_SLASHES),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $audio = UploadedFile::fake()->createWithContent('sample.webm', 'fake-audio-content');

        $result = app(GeminiAudioService::class)->evaluateAudio(
            $audio,
            [
                'mode' => 'shadowing',
                'instruction' => 'Repeat the short clip as accurately as possible.',
                'target_text' => 'Autonomous transit systems could reduce congestion in large cities.',
            ],
            'The article is about autonomous public transport.',
        );

        $this->assertSame('shadowing', $result['mode']);
        $this->assertSame('Accuracy', $result['metric_labels']['relevance']);
        $this->assertSame(7.5, $result['relevance']['score']);
        $this->assertSame('Autonomous transit systems could reduce congestion in large cities.', $result['transcript']);
    }
}
