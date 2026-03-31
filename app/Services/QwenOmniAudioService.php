<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QwenOmniAudioService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.qwen_omni.api_key', '');
        $this->model = (string) config('services.qwen_omni.model', 'qwen-omni-turbo');
        $this->baseUrl = rtrim((string) config('services.qwen_omni.base_url', 'https://dashscope.aliyuncs.com/compatible-mode/v1'), '/');
    }

    public function evaluateAudio(UploadedFile $audioFile, string $taskInstruction, string $articleContent): array
    {
        if ($this->apiKey === '') {
            return ['error' => 'Qwen Omni API key is not configured.'];
        }

        try {
            $audioBytes = file_get_contents($audioFile->getRealPath());
            if ($audioBytes === false) {
                return ['error' => 'Failed to read uploaded audio file.'];
            }

            $audioBase64 = base64_encode($audioBytes);
            $mimeType = (string) ($audioFile->getMimeType() ?? 'audio/webm');
            $format = $this->resolveAudioFormat($mimeType, $audioFile->getClientOriginalExtension());

            $prompt = <<<PROMPT
You are an expert English speaking examiner.

Task Instruction: {$taskInstruction}
Article Context: {$articleContent}

Evaluate the user's spoken response and return ONLY a JSON object with:
- score: number (0-100)
- fluency: {"score": number (0-10), "comment": string}
- relevance: {"score": number (0-10), "comment": string}
- pronunciation: {"score": number (0-10), "comment": string}
- feedback: string
PROMPT;

            $payload = [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a strict but fair English speaking examiner.',
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                            [
                                'type' => 'input_audio',
                                'input_audio' => [
                                    'data' => $audioBase64,
                                    'format' => $format,
                                ],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0.2,
            ];

            $response = Http::timeout(120)
                ->withToken($this->apiKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl.'/chat/completions', $payload);

            if ($response->failed()) {
                $message = (string) ($response->json('error.message') ?? 'Failed to reach Qwen Omni service.');
                Log::error('Qwen Omni API request failed.', [
                    'status' => $response->status(),
                    'model' => $this->model,
                    'body' => $response->body(),
                ]);

                return [
                    'error' => $message,
                    'status_code' => $response->status(),
                ];
            }

            $content = (string) ($response->json('choices.0.message.content') ?? '{}');
            $decoded = json_decode($content, true);

            if (! is_array($decoded)) {
                Log::warning('Qwen Omni returned non-JSON content.', [
                    'content' => $content,
                ]);

                return ['error' => 'Qwen Omni returned non-JSON evaluation result.'];
            }

            $decoded['score'] = is_numeric($decoded['score'] ?? null) ? (float) $decoded['score'] : 0;
            foreach (['fluency', 'relevance', 'pronunciation'] as $metric) {
                if (! isset($decoded[$metric]) || ! is_array($decoded[$metric])) {
                    $decoded[$metric] = [
                        'score' => 0,
                        'comment' => '',
                    ];
                }
            }
            $decoded['feedback'] = (string) ($decoded['feedback'] ?? 'No feedback provided.');

            return $decoded;
        } catch (\Throwable $exception) {
            Log::error('Qwen Omni evaluation exception.', [
                'message' => $exception->getMessage(),
            ]);

            return ['error' => 'An unexpected error occurred during Qwen Omni evaluation.'];
        }
    }

    protected function resolveAudioFormat(string $mimeType, ?string $extension): string
    {
        $ext = strtolower((string) $extension);
        if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'webm'], true)) {
            return $ext;
        }

        return match ($mimeType) {
            'audio/mpeg' => 'mp3',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/ogg' => 'ogg',
            'audio/mp4', 'audio/x-m4a' => 'm4a',
            default => 'webm',
        };
    }
}
