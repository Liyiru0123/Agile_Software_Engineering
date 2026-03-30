<?php

namespace App;

use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class GroqSpeechService
{
    public function transcribe(UploadedFile $audioFile, ?Article $article = null): array
    {
        if (! filled(config('services.groq.api_key'))) {
            return [
                'provider' => 'groq',
                'configured' => false,
                'text' => '',
                'message' => 'Groq API key 未配置，当前无法执行语音识别。',
            ];
        }

        try {
            $response = Http::timeout(60)
                ->withToken(config('services.groq.api_key'))
                ->attach(
                    'file',
                    file_get_contents($audioFile->getRealPath()),
                    $audioFile->getClientOriginalName()
                )
                ->post(rtrim(config('services.groq.base_url'), '/').'/audio/transcriptions', [
                    'model' => config('services.groq.stt_model'),
                    'language' => 'en',
                    'temperature' => 0,
                    'response_format' => 'verbose_json',
                    'prompt' => 'This audio contains English listening answers and article-related academic vocabulary.',
                ])
                ->throw();

            return [
                'provider' => 'groq',
                'configured' => true,
                'text' => trim((string) ($response->json('text') ?? '')),
                'duration' => $response->json('duration'),
                'article_id' => $article?->id,
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return [
                'provider' => 'groq',
                'configured' => true,
                'text' => '',
                'message' => 'Groq 语音识别请求失败，请稍后重试。',
            ];
        }
    }
}
