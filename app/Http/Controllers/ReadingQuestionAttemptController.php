<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingQuestionAttemptController extends Controller
{
    public function index(Article $article): JsonResponse
    {
        $readingQuestions = $article->readingQuestions()
            ->select('id', 'article_id', 'question_text', 'options')
            ->orderBy('id')
            ->get();

        return response()->json([
            'article_id' => $article->id,
            'readingQuestions' => $readingQuestions,
        ]);
    }

    public function submit(Request $request, Article $article): JsonResponse
    {
        $payload = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.question_id' => ['required', 'integer'],
            'answers.*.selected' => ['nullable', 'string', 'max:10'],
        ]);

        $readingQuestions = $article->readingQuestions()
            ->select('id', 'question_text', 'options', 'correct_answer', 'explanation')
            ->get()
            ->keyBy('id');

        $answerMap = collect($payload['answers'])->keyBy('question_id');

        $results = [];
        $correctCount = 0;

        foreach ($readingQuestions as $questionId => $question) {
            $selected = $answerMap[$questionId]['selected'] ?? null;
            $isCorrect = $selected !== null && strtoupper($selected) === strtoupper($question->correct_answer);

            if ($isCorrect) {
                $correctCount++;
            }

            $results[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'options' => $question->options,
                'your_answer' => $selected,
                'correct_answer' => $question->correct_answer,
                'is_correct' => $isCorrect,
                'explanation' => $question->explanation,
            ];
        }

        $total = count($results);
        $score = $total > 0 ? round(($correctCount / $total) * 100, 2) : 0;

        return response()->json([
            'article_id' => $article->id,
            'total' => $total,
            'correct_count' => $correctCount,
            'wrong_count' => $total - $correctCount,
            'score' => $score,
            'results' => $results,
        ]);
    }
}
