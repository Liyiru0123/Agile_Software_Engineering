<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\UserPlan;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class SkillPlanCompletionService
{
    public function syncForSubmission(int $userId, string $skillType, CarbonInterface|string|null $submittedAt = null): int
    {
        if (! in_array($skillType, ['listening', 'speaking'], true)) {
            return 0;
        }

        $submittedDate = match (true) {
            $submittedAt instanceof CarbonInterface => $submittedAt->toDateString(),
            is_string($submittedAt) && trim($submittedAt) !== '' => Carbon::parse($submittedAt)->toDateString(),
            default => now()->toDateString(),
        };

        $completedCount = Submission::query()
            ->join('exercises', 'submissions.exercise_id', '=', 'exercises.id')
            ->where('submissions.user_id', $userId)
            ->whereDate('submissions.created_at', $submittedDate)
            ->where('exercises.type', $skillType)
            ->count();

        if ($completedCount <= 0) {
            return 0;
        }

        return UserPlan::query()
            ->where('user_id', $userId)
            ->where('plan_kind', 'skill')
            ->where('skill_type', $skillType)
            ->whereDate('plan_date', $submittedDate)
            ->where('status', 'pending')
            ->where('target_count', '<=', $completedCount)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
    }
}
