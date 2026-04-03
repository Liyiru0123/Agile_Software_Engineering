<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Submission;
use App\Models\UserPlan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function studyAnalysis(Request $request): View
    {
        $userId = (int) $request->user()->id;
        [$rangeKey, $startAt, $endAt] = $this->resolveRange($request->string('range')->toString());

        $submissions = Submission::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$startAt, $endAt])
            ->with(['exercise:id,type,article_id', 'article:id'])
            ->get();

        $plans = UserPlan::query()
            ->where('user_id', $userId)
            ->whereBetween('plan_date', [$startAt->toDateString(), $endAt->toDateString()])
            ->get();

        $articleCount = Article::query()->count();

        $overview = $this->buildOverviewModule($submissions, $plans, $articleCount, $endAt);
        $effort = $this->buildEffortModule($submissions, $plans, $startAt, $endAt);
        $outcomes = $this->buildOutcomeModule($submissions, $startAt, $endAt);

        return view('analysis.study-analysis', [
            'selectedRange' => $rangeKey,
            'rangeStart' => $startAt,
            'rangeEnd' => $endAt,
            'overview' => $overview,
            'effort' => $effort,
            'outcomes' => $outcomes,
        ]);
    }

    private function resolveRange(?string $range): array
    {
        $endAt = now()->endOfDay();

        $startAt = match ($range) {
            '30d' => now()->subDays(29)->startOfDay(),
            '90d' => now()->subDays(89)->startOfDay(),
            '1y' => now()->subYear()->addDay()->startOfDay(),
            default => now()->subDays(6)->startOfDay(),
        };

        $rangeKey = in_array($range, ['7d', '30d', '90d', '1y'], true) ? $range : '7d';

        return [$rangeKey, $startAt, $endAt];
    }

    private function buildOverviewModule(Collection $submissions, Collection $plans, int $articleCount, Carbon $endAt): array
    {
        $totalStudySeconds = (int) $submissions->sum('time_spent');
        $studyDays = $submissions
            ->pluck('created_at')
            ->filter()
            ->map(fn ($time) => Carbon::parse($time)->toDateString())
            ->unique()
            ->count();

        $overallAccuracy = $this->toPercent((float) $submissions->avg('score'));

        $completedPlans = $plans->where('status', 'completed')->count();
        $totalPlans = $plans->count();
        $completionRate = $totalPlans > 0 ? round(($completedPlans / $totalPlans) * 100, 1) : 0.0;

        $activeArticleCount = $submissions
            ->pluck('article_id')
            ->filter()
            ->unique()
            ->count();
        $articleCoverage = $articleCount > 0 ? round(($activeArticleCount / $articleCount) * 100, 1) : 0.0;

        $abilityScore = round(
            min(100, $overallAccuracy * 0.6 + $completionRate * 0.3 + min(100, $articleCoverage) * 0.1),
            1
        );

        $focusHour = $submissions
            ->pluck('created_at')
            ->filter()
            ->map(fn ($time) => Carbon::parse($time)->hour)
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();

        $focusPeriod = $focusHour === null
            ? 'No clear peak focus hour yet'
            : sprintf('%02d:00 - %02d:00', $focusHour, ($focusHour + 1) % 24);

        $recent7DaysCount = $submissions
            ->filter(fn ($s) => Carbon::parse($s->created_at)->gte($endAt->copy()->subDays(6)->startOfDay()))
            ->count();

        $learningStatus = match (true) {
            $overallAccuracy >= 85 && $completionRate >= 75 && $recent7DaysCount >= 10 => 'Highly efficient & stable',
            $overallAccuracy >= 70 && $completionRate >= 50 => 'Steadily improving',
            $recent7DaysCount === 0 => 'Inactive recently',
            default => 'Needs more focus',
        };

        return [
            'total_study_seconds' => $totalStudySeconds,
            'total_study_hours' => round($totalStudySeconds / 3600, 1),
            'study_days' => $studyDays,
            'overall_accuracy' => $overallAccuracy,
            'completion_rate' => $completionRate,
            'ability_score' => $abilityScore,
            'focus_period' => $focusPeriod,
            'learning_status' => $learningStatus,
        ];
    }

    private function buildEffortModule(Collection $submissions, Collection $plans, Carbon $startAt, Carbon $endAt): array
    {
        $dateLabels = $this->buildDateLabels($startAt, $endAt);

        $dailyStudySeconds = $submissions
            ->groupBy(fn ($s) => Carbon::parse($s->created_at)->toDateString())
            ->map(fn (Collection $group) => (int) $group->sum('time_spent'));

        $dailyStudyCount = $submissions
            ->groupBy(fn ($s) => Carbon::parse($s->created_at)->toDateString())
            ->map(fn (Collection $group) => $group->count());

        $durationTrend = collect($dateLabels)->map(fn ($date) => [
            'date' => $date,
            'seconds' => (int) ($dailyStudySeconds[$date] ?? 0),
            'minutes' => round(((int) ($dailyStudySeconds[$date] ?? 0)) / 60, 1),
        ])->values();

        $countTrend = collect($dateLabels)->map(fn ($date) => [
            'date' => $date,
            'count' => (int) ($dailyStudyCount[$date] ?? 0),
        ])->values();

        $nonZeroDayCounts = $countTrend->pluck('count')->filter(fn ($count) => $count > 0);
        $activeDays = $nonZeroDayCounts->count();

        $oneSessionDays = $nonZeroDayCounts->filter(fn ($count) => $count === 1)->count();
        $twoSessionDays = $nonZeroDayCounts->filter(fn ($count) => $count === 2)->count();
        $threePlusSessionDays = $nonZeroDayCounts->filter(fn ($count) => $count >= 3)->count();

        $frequencyDistribution = [
            'one' => [
                'days' => $oneSessionDays,
                'percent' => $activeDays > 0 ? round(($oneSessionDays / $activeDays) * 100, 1) : 0.0,
            ],
            'two' => [
                'days' => $twoSessionDays,
                'percent' => $activeDays > 0 ? round(($twoSessionDays / $activeDays) * 100, 1) : 0.0,
            ],
            'three_plus' => [
                'days' => $threePlusSessionDays,
                'percent' => $activeDays > 0 ? round(($threePlusSessionDays / $activeDays) * 100, 1) : 0.0,
            ],
        ];

        $totalPlans = $plans->count();
        $completedPlans = $plans->where('status', 'completed')->count();
        $planExecutionRate = $totalPlans > 0 ? round(($completedPlans / $totalPlans) * 100, 1) : 0.0;

        $overduePlans = $plans
            ->filter(fn ($plan) => $plan->status === 'pending' && Carbon::parse($plan->plan_date)->lt(now()->startOfDay()))
            ->count();

        $avgDailyMinutes = round($durationTrend->avg('minutes') ?? 0, 1);
        $avgDailySessions = round($countTrend->avg('count') ?? 0, 1);

        $insights = [
            $avgDailyMinutes >= 45
                ? 'Daily study time is solid. You are maintaining a healthy amount of focused practice.'
                : 'Daily study time is relatively low. Consider adding one fixed study block to your schedule.',
            $avgDailySessions >= 2
                ? 'Your study rhythm is stable with multiple short, high‑frequency sessions.'
                : 'Session frequency is low. Try splitting your practice into at least two sessions per day.',
            $planExecutionRate >= 70
                ? 'Plan execution is strong. Most of your study plans are being completed.'
                : 'Plan execution is weak. Consider reducing the number of plans and raising the completion priority.',
        ];

        return [
            'daily_study_duration_trend' => $durationTrend,
            'daily_study_count_trend' => $countTrend,
            'frequency_distribution' => $frequencyDistribution,
            'plan_execution_rate' => $planExecutionRate,
            'overdue_plan_count' => $overduePlans,
            'insights' => $insights,
        ];
    }

    private function buildOutcomeModule(Collection $submissions, Carbon $startAt, Carbon $endAt): array
    {
        $dateLabels = $this->buildDateLabels($startAt, $endAt);

        $overallAccuracyTrend = collect($dateLabels)->map(function (string $date) use ($submissions) {
            $daySubmissions = $submissions
                ->filter(fn ($s) => Carbon::parse($s->created_at)->toDateString() === $date);

            return [
                'date' => $date,
                'accuracy' => $this->toPercent((float) $daySubmissions->avg('score')),
            ];
        })->values();

        $listeningTrend = collect($dateLabels)->map(function (string $date) use ($submissions) {
            $daySubmissions = $submissions
                ->filter(fn ($s) => Carbon::parse($s->created_at)->toDateString() === $date)
                ->filter(fn ($s) => $s->exercise?->type === 'listening');

            return [
                'date' => $date,
                'accuracy' => $this->toPercent((float) $daySubmissions->avg('score')),
            ];
        })->values();

        $readingTrend = collect($dateLabels)->map(function (string $date) use ($submissions) {
            $daySubmissions = $submissions
                ->filter(fn ($s) => Carbon::parse($s->created_at)->toDateString() === $date)
                ->filter(fn ($s) => $s->exercise?->type === 'reading');

            return [
                'date' => $date,
                'accuracy' => $this->toPercent((float) $daySubmissions->avg('score')),
            ];
        })->values();

        $completedExercises = $submissions->count();
        $totalStudyHours = max(0.1, round(((int) $submissions->sum('time_spent')) / 3600, 2));
        $overallAccuracy = $this->toPercent((float) $submissions->avg('score'));
        $efficiencyIndex = round(($completedExercises / $totalStudyHours) * ($overallAccuracy / 100), 2);

        $listeningOverall = $this->toPercent((float) $submissions->filter(fn ($s) => $s->exercise?->type === 'listening')->avg('score'));
        $readingOverall = $this->toPercent((float) $submissions->filter(fn ($s) => $s->exercise?->type === 'reading')->avg('score'));

        $insights = [
            $overallAccuracy >= 80
                ? 'Overall accuracy is strong. Your current training approach is working.'
                : 'Overall accuracy can still be improved. Prioritize reviewing incorrect questions.',
            abs($listeningOverall - $readingOverall) <= 8
                ? 'Listening and reading are balanced. Your skill profile is relatively stable.'
                : ($listeningOverall > $readingOverall
                    ? 'Listening is stronger than reading. Consider adding more intensive reading practice.'
                    : 'Reading is stronger than listening. Consider adding more intensive listening practice.'),
            $efficiencyIndex >= 1.5
                ? 'Learning efficiency is high. You are getting solid output per study hour.'
                : 'Learning efficiency is low. Try shortening single study sessions and increasing focus.',
        ];

        return [
            'overall_accuracy_trend' => $overallAccuracyTrend,
            'listening_accuracy_trend' => $listeningTrend,
            'reading_accuracy_trend' => $readingTrend,
            'completed_exercises' => $completedExercises,
            'efficiency_index' => $efficiencyIndex,
            'insights' => $insights,
        ];
    }

    private function buildDateLabels(Carbon $startAt, Carbon $endAt): array
    {
        // NOTE:
        // On some versions CarbonPeriod::map() returns a Generator
        // which does not have a toArray() method.
        // Use explicit iteration here to stay compatible.
        $period = CarbonPeriod::create($startAt->copy()->startOfDay(), $endAt->copy()->startOfDay());
        $labels = [];

        foreach ($period as $date) {
            /** @var Carbon $date */
            $labels[] = $date->toDateString();
        }

        return $labels;
    }

    private function toPercent(float $score): float
    {
        if ($score <= 0) {
            return 0.0;
        }

        $normalized = $score <= 1 ? $score * 100 : $score;

        return round(min(100, $normalized), 1);
    }

    private function buildCapabilityDiagnosisModule(int $userId, Carbon $startAt, Carbon $endAt): array
    {
        $rows = DB::table('submissions')
            ->join('exercises', 'submissions.exercise_id', '=', 'exercises.id')
            ->leftJoin('articles', 'submissions.article_id', '=', 'articles.id')
            ->where('submissions.user_id', $userId)
            ->whereBetween('submissions.created_at', [$startAt, $endAt])
            ->whereIn('exercises.type', ['listening', 'reading'])
            ->select([
                'submissions.score',
                'submissions.time_spent',
                'submissions.ai_advice',
                'submissions.user_answer',
                'submissions.created_at',
                'exercises.type as exercise_type',
                'articles.difficulty as article_difficulty',
            ])
            ->get();

        $listeningRows = $rows->where('exercise_type', 'listening')->values();
        $readingRows = $rows->where('exercise_type', 'reading')->values();

        $listening = $this->buildSingleDiagnosis(
            rows: $listeningRows,
            type: 'listening'
        );

        $reading = $this->buildSingleDiagnosis(
            rows: $readingRows,
            type: 'reading'
        );

        $topIssues = $this->buildTopIssues($listening, $reading);

        return [
            'listening' => $listening,
            'reading' => $reading,
            'top_issues' => $topIssues,
        ];
    }

    private function buildSingleDiagnosis(Collection $rows, string $type): array
    {
        $accuracy = $this->toPercent((float) $rows->avg('score'));
        $errorRate = round(max(0, 100 - $accuracy), 1);

        $totalSeconds = (int) $rows->sum('time_spent');
        $totalHours = max(0.1, round($totalSeconds / 3600, 2));
        $completedCount = $rows->count();

        $efficiencyIndex = round(($completedCount / $totalHours) * ($accuracy / 100), 2);

        $errorTags = $rows
            ->flatMap(fn ($row) => $this->extractErrorTags($row->ai_advice, $row->user_answer, $type))
            ->filter()
            ->map(fn ($tag) => mb_substr(trim((string) $tag), 0, 40))
            ->values();

        $errorTypeDistribution = $errorTags
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->map(fn ($count, $tag) => [
                'type' => $tag,
                'count' => $count,
                'percent' => $completedCount > 0 ? round(($count / $completedCount) * 100, 1) : 0.0,
            ])
            ->values()
            ->all();

        $suggestions = $this->buildSuggestions($type, $errorRate, $efficiencyIndex, $errorTypeDistribution);

        return [
            'exercise_type' => $type,
            'error_rate' => $errorRate,
            'error_type_distribution' => $errorTypeDistribution,
            'efficiency_index' => $efficiencyIndex,
            'completed_count' => $completedCount,
            'suggestions' => $suggestions,
        ];
    }

    private function extractErrorTags(mixed $aiAdvice, mixed $userAnswer, string $type): array
    {
        $decodedAdvice = $this->decodeJsonValue($aiAdvice);
        $decodedAnswer = $this->decodeJsonValue($userAnswer);

        $candidates = [];

        if (is_array($decodedAdvice)) {
            $candidates = array_merge($candidates, $this->collectTextCandidates($decodedAdvice));
        }

        if (is_array($decodedAnswer)) {
            $candidates = array_merge($candidates, $this->collectTextCandidates($decodedAnswer));
        }

        $dictionary = $type === 'listening'
            ? [
                'detail omission' => 'Detail omission',
                'omission' => 'Detail omission',
                'keyword mismatch' => 'Keyword mismatch',
                'keyword' => 'Keyword mismatch',
                'inference' => 'Inference error',
                'distractor' => 'Distractor chosen',
                'spelling' => 'Spelling error',
                '听不清' => 'Poor auditory discrimination',
                '细节' => 'Detail omission',
                '关键词' => 'Keyword mismatch',
                '推断' => 'Inference error',
                '干扰' => 'Distractor chosen',
            ]
            : [
                'main idea' => 'Main idea misunderstanding',
                'reference' => 'Reference resolution error',
                'logic' => 'Logical chain break',
                'vocabulary' => 'Vocabulary misunderstanding',
                'inference' => 'Inference error',
                '主旨' => 'Main idea misunderstanding',
                '指代' => 'Reference resolution error',
                '逻辑' => 'Logical chain break',
                '词义' => 'Vocabulary misunderstanding',
                '推断' => 'Inference error',
            ];

        $tags = [];

        foreach ($candidates as $text) {
            $textLower = mb_strtolower((string) $text);

            foreach ($dictionary as $keyword => $label) {
                if (str_contains($textLower, mb_strtolower($keyword))) {
                    $tags[] = $label;
                }
            }
        }

        return array_values(array_unique($tags));
    }

    private function collectTextCandidates(array $payload): array
    {
        $texts = [];

        array_walk_recursive($payload, function ($value, $key) use (&$texts) {
            if (! is_scalar($value)) {
                return;
            }

            $keyStr = mb_strtolower((string) $key);
            $valueStr = trim((string) $value);

            if ($valueStr === '') {
                return;
            }

            if (
                str_contains($keyStr, 'error') ||
                str_contains($keyStr, 'mistake') ||
                str_contains($keyStr, 'weak') ||
                str_contains($keyStr, 'issue') ||
                str_contains($keyStr, 'problem') ||
                str_contains($keyStr, 'advice') ||
                str_contains($keyStr, 'feedback')
            ) {
                $texts[] = $valueStr;
            }
        });

        return $texts;
    }

    private function decodeJsonValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return $value;
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function buildSuggestions(string $type, float $errorRate, float $efficiencyIndex, array $errorTypeDistribution): array
    {
        $primaryType = $errorTypeDistribution[0]['type'] ?? null;

        $common = [
            $errorRate >= 35
                ? 'Your error rate is relatively high. First analyze why you missed questions, then train those patterns specifically.'
                : 'Your error rate is under control. Keep your current pace and continue reinforcing weak points.',
            $efficiencyIndex < 1.2
                ? 'Learning efficiency is low. Try keeping each session within 20–30 minutes and raising focus.'
                : 'Learning efficiency is solid. You can maintain this high‑quality training rhythm.',
        ];

        $typeSpecific = $type === 'listening'
            ? [
                'Add a listen–repeat–compare loop to your listening practice.',
                $primaryType
                    ? "Focus on reducing \"{$primaryType}\". After each session, replay and review similar errors."
                    : 'Prioritize fixing the most frequent error types and build a personal error log.',
            ]
            : [
                'Add paragraph main‑idea identification and logic‑chain annotation to your reading practice.',
                $primaryType
                    ? "Focus on reducing \"{$primaryType}\". After each session, replay and review similar errors."
                    : 'Prioritize fixing the most frequent error types and build a personal error log.',
            ];

        return array_values(array_unique(array_merge($common, $typeSpecific)));
    }

    private function buildTopIssues(array $listening, array $reading): array
    {
        $pool = collect();

        foreach ([$listening, $reading] as $module) {
            $typeLabel = $module['exercise_type'] === 'listening' ? 'Listening' : 'Reading';

            foreach ($module['error_type_distribution'] as $item) {
                $pool->push([
                    'module' => $typeLabel,
                    'issue' => $item['type'],
                    'impact_score' => round($item['percent'] * 0.7 + $module['error_rate'] * 0.3, 2),
                    'suggested_action' => $this->suggestActionByIssue($typeLabel, $item['type']),
                ]);
            }
        }

        $top = $pool
            ->sortByDesc('impact_score')
            ->take(3)
            ->values()
            ->all();

        if (count($top) < 3) {
            $fallback = [
                [
                    'module' => 'Listening',
                    'issue' => 'Frequent errors keep reappearing',
                    'impact_score' => $listening['error_rate'] ?? 0,
                    'suggested_action' => 'Do one set of listening error review every day and record the trigger for each mistake.',
                ],
                [
                    'module' => 'Reading',
                    'issue' => 'Unstable key‑information location',
                    'impact_score' => $reading['error_rate'] ?? 0,
                    'suggested_action' => 'For each passage, mark the structure before answering to speed up locating key information.',
                ],
                [
                    'module' => 'Overall',
                    'issue' => 'Low output per study hour',
                    'impact_score' => 50,
                    'suggested_action' => 'Use Pomodoro‑style sessions and give each round a clear, measurable output goal.',
                ],
            ];

            $top = collect($top)
                ->merge($fallback)
                ->unique(fn ($item) => $item['module'] . '-' . $item['issue'])
                ->take(3)
                ->values()
                ->all();
        }

        return $top;
    }

    private function suggestActionByIssue(string $moduleLabel, string $issue): string
    {
        $issueLower = mb_strtolower($issue);

        if (str_contains($issueLower, '细节') || str_contains($issueLower, '关键词')) {
            return "After each {$moduleLabel} session, spend 5 minutes replaying keywords and build a personal list of tricky words.";
        }

        if (str_contains($issueLower, '推断') || str_contains($issueLower, '逻辑')) {
            return "For {$moduleLabel} questions, write out the reasoning chain first, then verify each step against the answer.";
        }

        if (str_contains($issueLower, '主旨') || str_contains($issueLower, '指代')) {
            return "For {$moduleLabel} passages, mark the topic sentences and reference targets before answering questions.";
        }

        return "After each {$moduleLabel} session, review this issue and complete one focused reinforcement set.";
    }
}
