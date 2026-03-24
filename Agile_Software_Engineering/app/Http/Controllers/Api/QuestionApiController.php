<?php

namespace App\Http\Controllers\Api;

use App\Models\Question;
use Illuminate\Http\JsonResponse;

class QuestionApiController extends ApiController
{
    public function index(int $articleId): JsonResponse
    {
        $questions = Question::query()
            ->where('article_id', $articleId)
            ->get()
            ->map(fn (Question $question) => [
                'id' => $question->id,
                'article_id' => $question->article_id,
                'content' => $question->content,
                'options' => $question->options,
                'answer' => $question->answer,
                'explanation' => $question->explanation,
            ]);

        return $this->success($questions);
    }
}
