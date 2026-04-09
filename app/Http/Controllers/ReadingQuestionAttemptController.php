<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\Submission;
use App\Services\CompanionService;
use App\Services\ListeningPlanCompletionService;
use App\Services\ReadingExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingQuestionAttemptController extends Controller
{
    public function __construct(
        protected ReadingExerciseService $readingExerciseService,
        protected CompanionService $companionService,
        protected ListeningPlanCompletionService $listeningPlanCompletionService
    ) {
    }

    public function index(Article $article): JsonResponse
    {
        return response()->json([
            'article_id' => $article->id,
            'readingQuestions' => $this->readingExerciseService->getPublicQuestions($article),
        ]);
    }

    public function submit(Request $request, Article $article): JsonResponse
    {
        $payload = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'string'],
            'answers.*.selected' => ['nullable', 'string', 'max:10'],
        ]);

        $result = $this->readingExerciseService->evaluate($article, $payload['answers']);
        $reward = null;
        $completedPlan = null;

        if ($request->user()) {
            $readingExercise = Exercise::query()
                ->where('article_id', $article->id)
                ->where('type', 'reading')
                ->orderBy('id')
                ->first();

            if ($readingExercise) {
                $attemptCount = (int) Submission::query()
                    ->where('user_id', $request->user()->id)
                    ->where('article_id', $article->id)
                    ->where('exercise_id', $readingExercise->id)
                    ->max('attempt_count');

                Submission::query()->create([
                    'user_id' => $request->user()->id,
                    'exercise_id' => $readingExercise->id,
                    'article_id' => $article->id,
                    'user_answer' => ['answers' => $payload['answers']],
                    'score' => $result['score'] ?? 0,
                    'time_spent' => 0,
                    'attempt_count' => $attemptCount + 1,
                    'ai_advice' => $result,
                ]);
            }

            $reward = $this->companionService->grantLearningReward($request->user(), 'reading', $article->id);
            $completedPlan = $this->listeningPlanCompletionService->syncArticlePlan($request->user()->id, $article);
        }

        return response()->json(array_merge(
            $result,
            [
                'companion_reward' => $reward,
                'plan_auto_completed' => $completedPlan !== null,
            ]
        ));
    }
}