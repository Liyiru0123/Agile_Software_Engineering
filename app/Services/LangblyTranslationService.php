<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LangblyTranslationService
{
    public function translate(
        string $text,
        string $targetLanguage = 'zh-CN',
        ?string $sourceLanguage = 'en'
    ): array {
        $apiKey = (string) config('services.langbly.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('Langbly API key is not configured.');
        }

        $payload = [
            'q' => $text,
            'target' => $targetLanguage,
            'format' => 'text',
            'quality' => config('services.langbly.quality', 'standard'),
        ];

        if (filled($sourceLanguage)) {
            $payload['source'] = $sourceLanguage;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->post(config('services.langbly.base_url'), $payload)
                ->throw();
        } catch (RequestException $exception) {
            throw new RuntimeException('Langbly translation request failed.', 0, $exception);
        }

        $translation = data_get($response->json(), 'data.translations.0.translatedText');

        if (! is_string($translation) || trim($translation) === '') {
            throw new RuntimeException('Langbly returned an empty translation payload.');
        }

        return [
            'translated_text' => trim($translation),
            'source_language' => data_get($response->json(), 'data.translations.0.detectedSourceLanguage', $sourceLanguage),
            'target_language' => $targetLanguage,
            'provider' => 'langbly',
        ];
    }
}
