<?php

namespace App\Http\Controllers;

use App\ListeningExerciseService;
use App\Models\Article;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListeningTrainingController extends Controller
{
    public function __construct(
        protected ListeningExerciseService $listeningExerciseService
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

        return response()->json([
            'result' => $this->listeningExerciseService->evaluateSubmission(
                article: $article,
                exercise: $exercise,
                answers: $payload['answers'],
                userId: $request->user()?->id,
                timeSpent: $payload['time_spent'] ?? 0,
            ),
        ]);
    }
}
