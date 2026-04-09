<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Submission;
use App\Models\UserPlan;

class ListeningPlanCompletionService
{
    public function syncArticlePlan(int $userId, Article $article): ?UserPlan
    {
        if (! $this->isListeningFlowComplete($userId, $article)) {
            return null;
        }

        $plan = UserPlan::query()
            ->where('user_id', $userId)
            ->where('plan_kind', 'article')
            ->where('article_id', $article->id)
            ->where('status', 'pending')
            ->whereDate('plan_date', '<=', now()->toDateString())
            ->orderBy('plan_date')
            ->orderBy('id')
            ->first();

        if (! $plan) {
            return null;
        }

        $plan->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return $plan->fresh();
    }

    private function isListeningFlowComplete(int $userId, Article $article): bool
    {
        $exercises = $article->exercises()
            ->whereIn('type', ['listening', 'reading', 'writing'])
            ->orderBy('id')
            ->get();

        $listeningIds = $exercises
            ->where('type', 'listening')
            ->pluck('id')
            ->values();

        $readingIds = $exercises
            ->where('type', 'reading')
            ->pluck('id')
            ->values();

        $summaryExercise = $exercises
            ->where('type', 'writing')
            ->first(fn ($exercise) => data_get($exercise->question_data, 'task_type') === 'summary_response')
            ?? $exercises->firstWhere('type', 'writing');

        if ($listeningIds->isEmpty() || $readingIds->isEmpty() || ! $summaryExercise) {
            return false;
        }

        return Submission::query()
            ->where('user_id', $userId)
            ->where('article_id', $article->id)
            ->whereIn('exercise_id', $listeningIds)
            ->exists()
            && Submission::query()
                ->where('user_id', $userId)
                ->where('article_id', $article->id)
                ->whereIn('exercise_id', $readingIds)
                ->exists()
            && Submission::query()
                ->where('user_id', $userId)
                ->where('article_id', $article->id)
                ->where('exercise_id', $summaryExercise->id)
                ->exists();
    }
}