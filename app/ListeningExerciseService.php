<?php

namespace App;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\Submission;

class ListeningExerciseService
{
    public function getForArticle(Article $article, ?int $userId = null): ?array
    {
        $exercise = $article->exercises()
            ->where('type', 'listening')
            ->orderBy('id')
            ->first();

        if (! $exercise) {
            return null;
        }

        $normalizedExercise = $this->formatExercise($exercise);

        if (! $normalizedExercise) {
            return null;
        }

        if ($userId) {
            $latestSubmission = Submission::query()
                ->where('user_id', $userId)
                ->where('exercise_id', $exercise->id)
                ->where('article_id', $article->id)
                ->orderByDesc('id')
                ->first();

            if ($latestSubmission) {
                $normalizedExercise['latest_submission'] = [
                    'answers' => $latestSubmission->user_answer['items'] ?? [],
                    'result' => $this->buildResultFromAnswers(
                        $normalizedExercise['items'],
                        $latestSubmission->user_answer['items'] ?? []
                    ),
                    'submitted_at' => optional($latestSubmission->created_at)?->toIso8601String(),
                ];
            }
        }

        return $normalizedExercise;
    }

    public function evaluateSubmission(
        Article $article,
        Exercise $exercise,
        array $answers,
        ?int $userId = null,
        int $timeSpent = 0
    ): array {
        $normalizedExercise = $this->formatExercise($exercise);
        $result = $this->buildResultFromAnswers(
            $normalizedExercise['items'] ?? [],
            $answers['items'] ?? []
        );

        if ($userId) {
            Submission::query()->create([
                'user_id' => $userId,
                'exercise_id' => $exercise->id,
                'article_id' => $article->id,
                'user_answer' => [
                    'items' => $answers['items'] ?? [],
                ],
                'score' => $result['score'],
                'time_spent' => $timeSpent,
                'attempt_count' => 1,
                'ai_advice' => [
                    'provider' => 'database-check',
                    'summary' => $result['correct_count'] === $result['total_count']
                        ? 'All blanks were completed correctly.'
                        : 'Review the incorrect blanks and compare them with the source audio.',
                ],
            ]);
        }

        return $result;
    }

    protected function buildResultFromAnswers(array $items, array $rawAnswers): array
    {
        $itemCollection = collect($items);
        $userItemAnswers = collect($rawAnswers);

        $itemResults = $itemCollection->map(function (array $item) use ($userItemAnswers) {
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

    protected function formatExercise(Exercise $exercise): ?array
    {
        $questionData = $exercise->question_data ?? [];
        $items = $this->normalizeItems($questionData, $exercise->answer ?? []);

        if ($items === []) {
            return null;
        }

        return [
            'id' => $exercise->id,
            'instruction' => $questionData['instruction'] ?? 'Listen to the audio and fill in the missing words or phrases.',
            'items' => $items,
            'note' => $questionData['note'] ?? null,
        ];
    }

    protected function normalizeItems(array $questionData, mixed $answerData): array
    {
        $rawItems = [];

        if (is_array($questionData['items'] ?? null)) {
            $rawItems = $questionData['items'];
        } elseif (is_array($questionData['blanks'] ?? null)) {
            $rawItems = $questionData['blanks'];
        }

        return collect($rawItems)
            ->map(function (array $item, int $index) use ($answerData) {
                $id = (string) ($item['id'] ?? $item['index'] ?? $index + 1);
                $acceptedAnswers = $this->resolveAcceptedAnswers($id, $item, $answerData, $index);
                $primaryAnswer = $acceptedAnswers[0] ?? trim((string) ($item['answer'] ?? ''));
                $context = trim((string) ($item['context'] ?? ''));

                if ($context === '' || substr_count($context, '_____') !== 1 || $primaryAnswer === '') {
                    return null;
                }

                return [
                    'id' => $id,
                    'label' => (string) ($item['label'] ?? 'Blank '.$id),
                    'context' => $context,
                    'answer' => $primaryAnswer,
                    'accepted_answers' => $acceptedAnswers,
                    'hint' => $item['hint'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveAcceptedAnswers(string $id, array $item, mixed $answerData, int $index): array
    {
        $answers = collect();

        if (is_array($item['accepted_answers'] ?? null)) {
            $answers = $answers->merge($item['accepted_answers']);
        }

        if (filled($item['answer'] ?? null)) {
            $answers->push($item['answer']);
        }

        $answerLookup = $this->extractAnswerLookup($answerData);

        foreach ([$id, (string) ($index + 1), $index + 1, $item['index'] ?? null, (string) ($item['index'] ?? '')] as $key) {
            if ($key === null || $key === '') {
                continue;
            }

            if (! array_key_exists($key, $answerLookup)) {
                continue;
            }

            $storedAnswer = $answerLookup[$key];

            if (is_array($storedAnswer)) {
                $answers = $answers->merge($storedAnswer);
            } else {
                $answers->push($storedAnswer);
            }
        }

        return $answers
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => trim((string) $value))
            ->unique()
            ->values()
            ->all();
    }

    protected function extractAnswerLookup(mixed $answerData): array
    {
        if (! is_array($answerData)) {
            return [];
        }

        if (is_array($answerData['items'] ?? null)) {
            return $answerData['items'];
        }

        return $answerData;
    }

    protected function normalize(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9%.\s-]/', '', $value) ?? '';

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }
}
