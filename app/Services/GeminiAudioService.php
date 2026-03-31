<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class GeminiAudioService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/models');
    }

    /**
     * Evaluate speaking audio using Gemini.
     */
    public function evaluateAudio(UploadedFile $audioFile, string $taskInstruction, string $articleContent): array
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is not configured.');
            return ['error' => 'AI evaluation service is temporarily unavailable.'];
        }

        try {
            // 1. Convert audio to base64
            $audioBase64 = base64_encode(file_get_contents($audioFile->getRealPath()));
            $mimeType = $audioFile->getMimeType();

            // 2. Prepare the prompt
            $prompt = <<<PROMPT
You are an expert English speaking examiner. Please evaluate the following spoken response based on the article content and task instruction provided.

Task Instruction: $taskInstruction
Article Context (Reference): $articleContent

Provide a JSON response with the following keys:
- score: An overall score out of 100.
- fluency: Score out of 10 and a brief comment.
- relevance: Score out of 10 and a brief comment (how well it addresses the task and article).
- pronunciation: Score out of 10 and a brief comment.
- feedback: A concise summary of strengths and areas for improvement in English.

Return ONLY the JSON object.
PROMPT;

            // 3. Call Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(90)->post("{$this->baseUrl}/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $audioBase64
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ]);

            if ($response->failed()) {
                $message = (string) ($response->json('error.message') ?? 'Failed to reach AI evaluation service.');
                Log::error('Gemini API request failed.', [
                    'status' => $response->status(),
                    'model' => $this->model,
                    'body' => $response->body(),
                ]);

                return [
                    'error' => $message,
                    'status_code' => $response->status(),
                ];
            }

            $result = $response->json();
            $textResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            return json_decode($textResponse, true) ?: ['error' => 'Invalid AI response format.'];

        } catch (\Exception $e) {
            Log::error('Error in Gemini evaluation: ' . $e->getMessage());
            return ['error' => 'An unexpected error occurred during AI evaluation.'];
        }
    }
}
