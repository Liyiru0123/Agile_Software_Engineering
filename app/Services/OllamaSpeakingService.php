<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\GroqSpeechService;

class OllamaSpeakingService
{
    protected string $baseUrl;
    protected string $model;
    protected GroqSpeechService $sttService;

    public function __construct(GroqSpeechService $sttService)
    {
        $this->baseUrl = env('OLLAMA_BASE_URL', 'http://localhost:11434');
        $this->model = env('OLLAMA_MODEL', 'qwen3.5:9b');
        $this->sttService = $sttService;
    }

    /**
     * Evaluate speaking using local Ollama (requires STT first).
     */
    public function evaluateSpeaking(UploadedFile $audioFile, string $taskInstruction, string $articleContent): array
    {
        try {
            // 1. Transcribe audio to text first using Groq (since Ollama/Qwen is LLM)
            $transcriptionResult = $this->sttService->transcribe($audioFile);
            
            if (empty($transcriptionResult['text'])) {
                Log::warning('Speaking STT failed before Ollama evaluation.', [
                    'provider' => $transcriptionResult['provider'] ?? 'unknown',
                    'configured' => $transcriptionResult['configured'] ?? null,
                    'message' => $transcriptionResult['message'] ?? null,
                ]);

                return ['error' => 'Speech recognition failed. Please try speaking more clearly.'];
            }

            $userSpeech = $transcriptionResult['text'];

            // 2. Prepare the prompt for Qwen
            $prompt = <<<PROMPT
You are an expert English speaking examiner. Please evaluate the following transcript of a spoken response based on the article content and task instruction provided.

Article Context: $articleContent
Task Instruction: $taskInstruction
User's Spoken Transcript: "$userSpeech"

Provide a JSON response with the following keys:
- score: An overall score out of 100.
- fluency: Score out of 10 and a brief comment.
- relevance: Score out of 10 and a brief comment.
- pronunciation: Score out of 10 and a brief comment.
- feedback: A concise summary of strengths and areas for improvement.

Return ONLY the JSON object.
PROMPT;

            // 3. Call local Ollama API (strict JSON first)
            $response = Http::timeout(120)->post("{$this->baseUrl}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json'
            ]);

            if ($response->failed()) {
                Log::error('Ollama API request failed: ' . $response->body());
                return ['error' => 'Local AI service (Ollama) is not responding.'];
            }

            $result = $response->json();
            $textResponse = trim((string) ($result['response'] ?? $result['message']['content'] ?? ''));

            // Some models return empty body in strict json mode. Retry once without format.
            if ($textResponse === '') {
                $retry = Http::timeout(120)->post("{$this->baseUrl}/api/generate", [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                ]);

                if ($retry->successful()) {
                    $retryResult = $retry->json();
                    $textResponse = trim((string) ($retryResult['response'] ?? $retryResult['message']['content'] ?? ''));
                }
            }

            $evaluation = json_decode($textResponse, true);

            if (! is_array($evaluation) && $textResponse !== '') {
                // Fallback: extract first JSON object from markdown/text output.
                if (preg_match('/\{[\s\S]*\}/', $textResponse, $matches) === 1) {
                    $evaluation = json_decode($matches[0], true);
                }
            }

            if (! is_array($evaluation)) {
                Log::warning('Ollama returned non-JSON evaluation payload.', [
                    'raw' => $textResponse,
                ]);

                return ['error' => 'Invalid evaluation format from Local AI.'];
            }
            
            // Add the transcript to the result for user visibility
            $evaluation['transcript'] = $userSpeech;

            // Normalize common missing fields so frontend rendering is stable.
            $evaluation['score'] = is_numeric($evaluation['score'] ?? null) ? (float) $evaluation['score'] : 0;
            $evaluation['feedback'] = (string) ($evaluation['feedback'] ?? 'No feedback provided.');

            foreach (['fluency', 'relevance', 'pronunciation'] as $metric) {
                if (! isset($evaluation[$metric])) {
                    $evaluation[$metric] = [
                        'score' => 0,
                        'comment' => '',
                    ];
                }
            }

            return $evaluation;

        } catch (\Exception $e) {
            Log::error('Error in Ollama evaluation: ' . $e->getMessage());
            return ['error' => 'An unexpected error occurred during local AI evaluation.'];
        }
    }
}
