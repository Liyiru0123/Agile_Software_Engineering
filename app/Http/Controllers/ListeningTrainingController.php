<?php

namespace App\Http\Controllers;

use App\ListeningExerciseService;
use App\Models\Article;
use App\Models\Exercise;
use App\Services\CompanionService;
use App\Services\ListeningPlanCompletionService;
use App\Services\SkillPlanCompletionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListeningTrainingController extends Controller
{
    public function __construct(
        protected ListeningExerciseService $listeningExerciseService,
        protected CompanionService $companionService,
        protected ListeningPlanCompletionService $listeningPlanCompletionService,
        protected SkillPlanCompletionService $skillPlanCompletionService
    ) {
    }

    public function evaluate(Request $request, Article $article): JsonResponse
    {
        $payload = $request->validate([
            'exercise_id' => ['required', Rule::exists('exercises', 'id')->where('article_id', $article->id)->where('type', 'listening')],
            'answers' => ['required', 'array'],
            'answers.items' => ['sometimes', 'array'],
            'time_spent' => ['sometimes', 'nullable', 'integer', 'min:0'],
        ]);

        $exercise = Exercise::query()->findOrFail($payload['exercise_id']);
        $result = $this->listeningExerciseService->evaluateSubmission(
            article: $article,
            exercise: $exercise,
            answers: $payload['answers'],
            userId: $request->user()?->id,
            timeSpent: $payload['time_spent'] ?? 0,
        );

        $reward = null;
        $completedPlan = null;
        $completedSkillPlans = 0;
        if ($request->user()) {
            $reward = $this->companionService->grantLearningReward($request->user(), 'listening', $article->id);
            $completedPlan = $this->listeningPlanCompletionService->syncArticlePlan($request->user()->id, $article);
            $completedSkillPlans = $this->skillPlanCompletionService->syncForSubmission($request->user()->id, 'listening');
        }

        return response()->json([
            'result' => $result,
            'companion_reward' => $reward,
            'plan_auto_completed' => $completedPlan !== null,
            'skill_plan_auto_completed' => $completedSkillPlans > 0,
        ]);
    }
}
