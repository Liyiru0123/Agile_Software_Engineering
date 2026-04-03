<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\CompanionService;
use App\Services\ReadingExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingQuestionAttemptController extends Controller
{
    public function __construct(
        protected ReadingExerciseService $readingExerciseService,
        protected CompanionService $companionService
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

        if ($request->user()) {
            $reward = $this->companionService->grantLearningReward($request->user(), 'reading', $article->id);
        }

        return response()->json(array_merge(
            $result,
            ['companion_reward' => $reward]
        ));
    }
}
