<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Exercise;
use App\WritingExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WritingTrainingController extends Controller
{
    public function __construct(
        protected WritingExerciseService $writingExerciseService
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

        return response()->json([
            'result' => $this->writingExerciseService->evaluateSubmission(
                article: $article,
                exercise: $exercise,
                draft: $payload['draft'],
                userId: $request->user()?->id,
                timeSpent: $payload['time_spent'] ?? 0,
            ),
        ]);
    }
}
