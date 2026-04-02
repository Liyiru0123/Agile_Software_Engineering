<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class GeminiAudioService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key', '');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $this->baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/models'), '/');
    }

    /**
     * Evaluate speaking audio using Gemini.
     */
    public function evaluateAudio(UploadedFile $audioFile, string|array $taskInstruction, string $articleContent): array
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is not configured.');
            return ['error' => 'AI evaluation service is temporarily unavailable.'];
        }

        try {
            $audioBase64 = base64_encode(file_get_contents($audioFile->getRealPath()));
            $mimeType = (string) ($audioFile->getMimeType() ?: 'audio/mpeg');
            $taskContext = $this->normalizeTaskContext($taskInstruction);
            $prompt = $this->buildEvaluationPrompt($taskContext, $articleContent);

            if ($this->usesCompatibleChatApi()) {
                return $this->evaluateWithCompatibleApi($audioBase64, $mimeType, $audioFile, $prompt, $taskContext);
            }

            return $this->evaluateWithNativeGeminiApi($audioBase64, $mimeType, $prompt, $taskContext);
        } catch (\Exception $e) {
            Log::error('Error in Gemini evaluation: ' . $e->getMessage());
            return ['error' => 'An unexpected error occurred during AI evaluation.'];
        }
    }

    protected function normalizeTaskContext(string|array $taskInstruction): array
    {
        if (is_array($taskInstruction)) {
            return [
                'mode' => (string) ($taskInstruction['mode'] ?? 'open_response'),
                'instruction' => (string) ($taskInstruction['instruction'] ?? 'Respond to the speaking task.'),
                'target_text' => isset($taskInstruction['target_text']) ? (string) $taskInstruction['target_text'] : null,
                'title' => (string) ($taskInstruction['title'] ?? 'Speaking Task'),
            ];
        }

        return [
            'mode' => 'open_response',
            'instruction' => $taskInstruction,
            'target_text' => null,
            'title' => 'Speaking Task',
        ];
    }

    protected function buildEvaluationPrompt(array $taskContext, string $articleContent): string
    {
        $taskInstruction = $taskContext['instruction'];

        if (($taskContext['mode'] ?? 'open_response') === 'shadowing' && filled($taskContext['target_text'] ?? null)) {
            $targetText = (string) $taskContext['target_text'];

            return <<<PROMPT
You are an expert English shadowing examiner. The learner listened to a short audio clip and tried to repeat it.

Task: {$taskInstruction}
Target excerpt: "{$targetText}"
Article Context (Reference): {$articleContent}

Please first infer a transcript of the learner's speech from the audio, then compare it with the target excerpt.

Provide a JSON response with the following keys:
- score: An overall score out of 100.
- fluency: Score out of 10 and a brief comment about smoothness and pacing.
- accuracy: Score out of 10 and a brief comment about how closely the learner matched the target wording and meaning.
- pronunciation: Score out of 10 and a brief comment about clarity and intelligibility.
- transcript: The learner's inferred transcript in plain English.
- feedback: A concise summary of strengths and the next revision priority in English.

Return ONLY the JSON object.
PROMPT;
        }

        return <<<PROMPT
You are an expert English speaking examiner. Please evaluate the following spoken response based on the article content and task instruction provided.

Task Instruction: $taskInstruction
Article Context (Reference): $articleContent

Provide a JSON response with the following keys:
- score: An overall score out of 100.
- fluency: Score out of 10 and a brief comment.
- relevance: Score out of 10 and a brief comment (how well it addresses the task and article).
- pronunciation: Score out of 10 and a brief comment.
- transcript: A short inferred transcript of the learner's response in plain English.
- feedback: A concise summary of strengths and areas for improvement in English.

Return ONLY the JSON object.
PROMPT;
    }

    protected function evaluateWithNativeGeminiApi(string $audioBase64, string $mimeType, string $prompt, array $taskContext): array
    {
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
                                'data' => $audioBase64,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json',
            ],
        ]);

        $this->storeRawAiResponse('gemini-native', $response->status(), $response->body());

        if ($response->failed()) {
            return $this->failedResponse('Gemini API request failed.', $response);
        }

        $textResponse = (string) ($response->json('candidates.0.content.parts.0.text') ?? '{}');

        return $this->decodeEvaluationPayload($textResponse, 'Gemini returned an invalid evaluation payload.', $taskContext);
    }

    protected function evaluateWithCompatibleApi(
        string $audioBase64,
        string $mimeType,
        UploadedFile $audioFile,
        string $prompt,
        array $taskContext
    ): array {
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
                                'format' => $this->resolveAudioFormat($mimeType, $audioFile->getClientOriginalExtension()),
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

        $this->storeRawAiResponse('gemini-compatible', $response->status(), $response->body());

        if ($response->failed()) {
            return $this->failedResponse('Gemini-compatible API request failed.', $response);
        }

        $content = $response->json('choices.0.message.content');
        $textResponse = $this->extractCompatibleContent($content);

        if (! is_string($textResponse) || trim($textResponse) === '') {
            Log::warning('Gemini-compatible API returned empty content.', [
                'model' => $this->model,
                'body' => $response->body(),
            ]);

            return ['error' => 'Gemini-compatible API returned an empty evaluation result.'];
        }

        return $this->decodeEvaluationPayload(
            $textResponse,
            'Gemini-compatible API returned an invalid evaluation payload.',
            $taskContext
        );
    }

    protected function failedResponse(string $logMessage, \Illuminate\Http\Client\Response $response): array
    {
        $message = (string) ($response->json('error.message') ?? 'Failed to reach AI evaluation service.');
        Log::error($logMessage, [
            'status' => $response->status(),
            'model' => $this->model,
            'body' => $response->body(),
        ]);

        return [
            'error' => $message,
            'status_code' => $response->status(),
        ];
    }

    protected function decodeEvaluationPayload(?string $payload, string $fallbackMessage, array $taskContext): array
    {
        if (! is_string($payload) || trim($payload) === '') {
            return ['error' => $fallbackMessage];
        }

        $trimmed = trim($payload);
        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```json|^```|```$/m', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        $decoded = json_decode($trimmed, true);

        if (! is_array($decoded)) {
            Log::warning('Gemini audio evaluation returned non-JSON content.', [
                'model' => $this->model,
                'payload' => Str::limit($trimmed, 1200),
            ]);

            return ['error' => $fallbackMessage];
        }

        return $this->normalizeEvaluation($decoded, $taskContext);
    }

    protected function normalizeEvaluation(array $decoded, array $taskContext): array
    {
        $isShadowing = ($taskContext['mode'] ?? 'open_response') === 'shadowing';
        $secondaryMetric = $isShadowing ? 'accuracy' : 'relevance';

        if ($isShadowing && ! isset($decoded['accuracy']) && isset($decoded['relevance'])) {
            $decoded['accuracy'] = $decoded['relevance'];
        }

        $decoded['score'] = is_numeric($decoded['score'] ?? null) ? (float) $decoded['score'] : 0;

        foreach (['fluency', $secondaryMetric, 'pronunciation'] as $metric) {
            if (! isset($decoded[$metric]) || ! is_array($decoded[$metric])) {
                $decoded[$metric] = [
                    'score' => 0,
                    'comment' => '',
                ];
            }

            $decoded[$metric]['score'] = is_numeric($decoded[$metric]['score'] ?? null)
                ? (float) $decoded[$metric]['score']
                : 0;
            $decoded[$metric]['comment'] = (string) ($decoded[$metric]['comment'] ?? '');
        }

        if ($isShadowing) {
            $decoded['accuracy'] = $decoded['accuracy'] ?? $decoded[$secondaryMetric];
            $decoded['relevance'] = $decoded['accuracy'];
        }

        if (! $isShadowing && ! isset($decoded['relevance'])) {
            $decoded['relevance'] = [
                'score' => 0,
                'comment' => '',
            ];
        }

        $decoded['transcript'] = (string) ($decoded['transcript'] ?? '');
        $decoded['feedback'] = (string) ($decoded['feedback'] ?? 'No feedback provided.');
        $decoded['mode'] = $taskContext['mode'] ?? 'open_response';
        $decoded['metric_labels'] = [
            'fluency' => 'Fluency',
            'relevance' => $isShadowing ? 'Accuracy' : 'Relevance',
            'pronunciation' => 'Pronunciation',
        ];

        return $decoded;
    }

    protected function extractCompatibleContent(mixed $content): ?string
    {
        if (is_string($content) && trim($content) !== '') {
            return $content;
        }

        if (! is_array($content)) {
            return null;
        }

        foreach ($content as $item) {
            $text = data_get($item, 'text') ?? data_get($item, 'output_text');

            if (is_string($text) && trim($text) !== '') {
                return $text;
            }
        }

        return null;
    }

    protected function usesCompatibleChatApi(): bool
    {
        return ! str_contains($this->baseUrl, 'generativelanguage.googleapis.com');
    }

    protected function storeRawAiResponse(string $provider, int $status, string $body): void
    {
        $path = storage_path('logs/speaking-ai-raw.log');
        $directory = dirname($path);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $entry = [
            'timestamp' => now()->toDateTimeString(),
            'provider' => $provider,
            'model' => $this->model,
            'base_url' => $this->baseUrl,
            'status' => $status,
            'body' => $body,
        ];

        File::append($path, json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL);
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
