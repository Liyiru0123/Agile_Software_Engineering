<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Exercise;
use App\Services\CompanionService;
use App\Services\ListeningPlanCompletionService;
use App\WritingExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WritingTrainingController extends Controller
{
    public function __construct(
        protected WritingExerciseService $writingExerciseService,
        protected CompanionService $companionService,
        protected ListeningPlanCompletionService $listeningPlanCompletionService
    ) {
    }

    public function evaluate(Request $request, Article $article): JsonResponse
    {
        $payload = $request->validate([
            'exercise_id' => ['required', Rule::exists('exercises', 'id')->where('article_id', $article->id)->where('type', 'writing')],
            'draft' => ['required', 'string', 'min:10'],
            'time_spent' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $exercise = Exercise::query()->findOrFail($payload['exercise_id']);
        $result = $this->writingExerciseService->evaluateSubmission(
            article: $article,
            exercise: $exercise,
            draft: $payload['draft'],
            userId: $request->user()?->id,
            timeSpent: $payload['time_spent'] ?? 0,
        );

        $reward = null;
        $completedPlan = null;
        if ($request->user()) {
            $reward = $this->companionService->grantLearningReward($request->user(), 'writing', $article->id);
            $completedPlan = $this->listeningPlanCompletionService->syncArticlePlan($request->user()->id, $article);
        }

        return response()->json([
            'result' => $result,
            'companion_reward' => $reward,
            'plan_auto_completed' => $completedPlan !== null,
        ]);
    }
}