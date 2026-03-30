<?php

namespace App;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\Submission;
use App\Services\ArticleTextProcessor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ListeningExerciseService
{
    private const LOGIC_CONNECTORS = [
        'however', 'therefore', 'moreover', 'furthermore', 'consequently', 'although',
        'because', 'while', 'whereas', 'thus', 'instead', 'meanwhile', 'overall',
        'for example', 'for instance', 'in contrast', 'as a result', 'in addition',
    ];

    public function __construct(
        protected ArticleTextProcessor $processor
    ) {
    }

    public function generateForArticle(Article $article): array
    {
        $fallback = $this->buildFallbackExercise($article);
        $exercisePayload = $this->generateWithGemini($article) ?? $fallback;

        $exercise = Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => $exercisePayload['instruction'],
                'passage' => $exercisePayload['passage'],
                'items' => $exercisePayload['items'],
                'provider' => $exercisePayload['provider'],
                'note' => $exercisePayload['note'] ?? null,
            ],
            'answer' => [
                'items' => collect($exercisePayload['items'])
                    ->mapWithKeys(fn (array $item) => [$item['id'] => $item['accepted_answers']])
                    ->all(),
            ],
        ]);

        return [
            'id' => $exercise->id,
            'instruction' => $exercisePayload['instruction'],
            'passage' => $exercisePayload['passage'],
            'items' => $exercisePayload['items'],
            'provider' => $exercisePayload['provider'],
            'note' => $exercisePayload['note'] ?? null,
        ];
    }

    public function evaluateSubmission(
        Article $article,
        Exercise $exercise,
        array $answers,
        ?int $userId = null,
        int $timeSpent = 0
    ): array {
        $questionData = $exercise->question_data ?? [];
        $items = collect($questionData['items'] ?? []);
        $userItemAnswers = collect($answers['items'] ?? []);

        $itemResults = $items->map(function (array $item) use ($userItemAnswers) {
            $userAnswer = trim((string) $userItemAnswers->get((string) $item['id'], ''));
            $acceptedAnswers = collect($item['accepted_answers'] ?? [])
                ->map(fn ($value) => $this->normalize((string) $value))
                ->filter()
                ->all();

            $normalizedAnswer = $this->normalize($userAnswer);
            $isCorrect = $normalizedAnswer !== '' && in_array($normalizedAnswer, $acceptedAnswers, true);

            return [
                'id' => $item['id'],
                'label' => $item['label'],
                'context' => $item['context'],
                'expected' => $item['answer'],
                'user_answer' => $userAnswer,
                'is_correct' => $isCorrect,
                'status' => $isCorrect ? 'Correct' : 'Incorrect',
            ];
        })->values();

        $questionCount = max(1, $itemResults->count());
        $correctCount = $itemResults->where('is_correct', true)->count();
        $score = round(($correctCount / $questionCount) * 100, 2);

        if ($userId) {
            Submission::query()->create([
                'user_id' => $userId,
                'exercise_id' => $exercise->id,
                'article_id' => $article->id,
                'user_answer' => [
                    'items' => $userItemAnswers->all(),
                ],
                'score' => $score,
                'time_spent' => $timeSpent,
                'attempt_count' => 1,
                'ai_advice' => [
                    'provider' => 'local-check',
                    'summary' => $correctCount === $questionCount
                        ? 'All blanks were completed correctly.'
                        : 'Review the incorrect blanks and compare them with the source audio.',
                ],
            ]);
        }

        return [
            'score' => $score,
            'correct_count' => $correctCount,
            'total_count' => $questionCount,
            'item_results' => $itemResults,
            'summary' => $correctCount === $questionCount
                ? 'All blanks were completed correctly.'
                : 'Some answers were incorrect. Check the marked blanks and compare them with the correct answers.',
        ];
    }

    protected function buildFallbackExercise(Article $article): array
    {
        $paragraphs = $this->processor->splitParagraphs($article->content);
        $sentences = collect($paragraphs)
            ->flatMap(fn (string $paragraph) => $this->processor->splitSentences($paragraph))
            ->filter()
            ->values();

        $passageSentences = $sentences->take(4)->values();
        $passage = $passageSentences->implode(' ');
        $usedContexts = [];
        $items = [];

        foreach ($passageSentences as $sentence) {
            $candidate = $this->buildBlankFromSentence($sentence, $usedContexts, count($items) + 1);

            if ($candidate) {
                $items[] = $candidate;
            }

            if (count($items) >= 5) {
                break;
            }
        }

        if (count($items) < 3) {
            $items = array_merge($items, $this->buildBackupItems($passageSentences, count($items) + 1));
            $items = array_slice($items, 0, 5);
        }

        return [
            'provider' => filled(config('services.gemini.api_key')) ? 'fallback-after-gemini-error' : 'local-fallback',
            'instruction' => 'Listen to the audio and fill in the missing words or phrases.',
            'passage' => $passage,
            'items' => array_values($items),
            'note' => filled(config('services.gemini.api_key'))
                ? 'Gemini generation failed, so a fallback gap-fill exercise was created locally.'
                : 'Gemini is not configured, so a fallback gap-fill exercise was created locally.',
        ];
    }

    protected function buildBlankFromSentence(string $sentence, array &$usedContexts, int $id): ?array
    {
        $logic = $this->matchFirst($sentence, self::LOGIC_CONNECTORS);
        if ($logic) {
            return $this->makeItem($id, 'Logic connector', $sentence, $logic, $usedContexts);
        }

        if (preg_match('/\b\d+(?:\.\d+)?%?\b/', $sentence, $matches) === 1) {
            return $this->makeItem($id, 'Data point', $sentence, $matches[0], $usedContexts);
        }

        preg_match_all("/\b[A-Za-z][A-Za-z'-]{6,}\b/", $sentence, $matches);
        $keyword = collect($matches[0] ?? [])
            ->reject(fn (string $word) => in_array(strtolower($word), ['throughout', 'because', 'however', 'therefore'], true))
            ->first();

        if ($keyword) {
            return $this->makeItem($id, 'Academic keyword', $sentence, $keyword, $usedContexts);
        }

        return null;
    }

    protected function buildBackupItems(Collection $sentences, int $startingId): array
    {
        $items = [];
        $usedContexts = [];

        foreach ($sentences as $sentence) {
            preg_match_all("/\b[A-Za-z][A-Za-z'-]{5,}\b/", $sentence, $matches);
            $word = collect($matches[0] ?? [])->first();

            if (! $word) {
                continue;
            }

            $candidate = $this->makeItem($startingId + count($items), 'Key word', $sentence, $word, $usedContexts);

            if ($candidate) {
                $items[] = $candidate;
            }
        }

        return $items;
    }

    protected function makeItem(int $id, string $label, string $sentence, string $answer, array &$usedContexts): ?array
    {
        $pattern = '/'.preg_quote($answer, '/').'/i';
        $context = preg_replace($pattern, '_____', $sentence, 1);
        $context = trim((string) preg_replace('/\s+/', ' ', $context ?? ''));

        if ($context === '' || in_array($context, $usedContexts, true)) {
            return null;
        }

        $usedContexts[] = $context;

        return [
            'id' => (string) $id,
            'label' => $label,
            'context' => $context,
            'answer' => $answer,
            'accepted_answers' => [$answer, strtolower($answer)],
        ];
    }

    protected function generateWithGemini(Article $article): ?array
    {
        if (! filled(config('services.gemini.api_key'))) {
            return null;
        }

        $prompt = <<<PROMPT
You are generating an academic English listening gap-fill exercise.

Return valid JSON only with this shape:
{
  "instruction": "string",
  "passage": "string",
  "items": [
    {
      "id": "1",
      "label": "string",
      "context": "string with exactly one blank written as _____",
      "answer": "string",
      "accepted_answers": ["string", "string"]
    }
  ]
}

Rules:
- Use the article text below.
- Create 4 to 5 high-quality listening blanks.
- Prioritize academic keywords, core claims, logical connectors, and result expressions.
- The passage should be a short excerpt from the article, suitable as a listening clip.
- Each item must contain exactly one blank.
- accepted_answers should include lowercase-safe variants.

Article title: {$article->title}
Article content:
{$article->content}
PROMPT;

        try {
            $response = Http::timeout(60)
                ->post(
                    rtrim(config('services.gemini.base_url'), '/').'/'.config('services.gemini.model').':generateContent?key='.config('services.gemini.api_key'),
                    [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.3,
                            'responseMimeType' => 'application/json',
                        ],
                    ]
                )
                ->throw();

            $text = data_get($response->json(), 'candidates.0.content.parts.0.text');
            $decoded = $this->decodeJsonPayload($text);

            if (! is_array($decoded) || ! isset($decoded['instruction'], $decoded['passage'], $decoded['items']) || ! is_array($decoded['items'])) {
                return null;
            }

            $decoded['provider'] = 'gemini';
            $decoded['note'] = 'This gap-fill exercise was generated automatically by Gemini.';
            $decoded['items'] = collect($decoded['items'])
                ->map(function (array $item, int $index) {
                    $item['id'] = (string) ($item['id'] ?? $index + 1);
                    $item['accepted_answers'] = collect($item['accepted_answers'] ?? [$item['answer'] ?? ''])
                        ->filter()
                        ->map(fn ($answer) => trim((string) $answer))
                        ->push(strtolower((string) ($item['answer'] ?? '')))
                        ->unique()
                        ->values()
                        ->all();

                    return $item;
                })
                ->filter(fn (array $item) => isset($item['context'], $item['answer']) && substr_count($item['context'], '_____') === 1)
                ->take(5)
                ->values()
                ->all();

            return count($decoded['items']) >= 3 ? $decoded : null;
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    protected function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9%.\s-]/', '', $value) ?? '';

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    protected function matchFirst(string $sentence, array $needles): ?string
    {
        foreach ($needles as $needle) {
            if (stripos($sentence, $needle) !== false) {
                return $needle;
            }
        }

        return null;
    }

    protected function decodeJsonPayload(?string $text): ?array
    {
        if (! is_string($text) || trim($text) === '') {
            return null;
        }

        $trimmed = trim($text);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```json|^```|```$/m', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        $decoded = json_decode($trimmed, true);

        return is_array($decoded) ? $decoded : null;
    }
}
