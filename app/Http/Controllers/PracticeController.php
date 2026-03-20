<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function show(Request $request)
    {
        // 记录进入练习页面的时间，用来计算学习时长
        session(['practice_start_time' => now()->timestamp]);

        $question = [
            'id' => 1,
            'type' => 'vocabulary',
            'title' => '请选择 academic 的正确含义：',
            'options' => [
                'A' => '学术的',
                'B' => '农业的',
                'C' => '商业的',
                'D' => '日常的',
            ],
            'correct_answer' => 'A',
            'explanation' => 'academic 通常表示“学术的、学院的”，是学术英语中的高频词。',
        ];

        return view('practice', compact('question'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'answer' => 'required|in:A,B,C,D',
        ], [
            'answer.required' => '请选择一个答案后再提交。',
        ]);

        $question = [
            'id' => 1,
            'type' => 'vocabulary',
            'title' => '请选择 academic 的正确含义：',
            'options' => [
                'A' => '学术的',
                'B' => '农业的',
                'C' => '商业的',
                'D' => '日常的',
            ],
            'correct_answer' => 'A',
            'explanation' => 'academic 通常表示“学术的、学院的”，是学术英语中的高频词。',
        ];

        $userAnswer = $request->input('answer');
        $isCorrect = $userAnswer === $question['correct_answer'];

        $startTime = session('practice_start_time', now()->timestamp);
        $duration = now()->timestamp - $startTime;

        if ($duration < 0) {
            $duration = 0;
        }

        // 已登录用户：写入数据库
        if (auth()->check()) {
            $user = auth()->user();

            $user->practice_count = ($user->practice_count ?? 0) + 1;
            $user->total_questions = ($user->total_questions ?? 0) + 1;

            if ($isCorrect) {
                $user->correct_count = ($user->correct_count ?? 0) + 1;
            }

            $user->study_seconds = ($user->study_seconds ?? 0) + $duration;
            $user->save();
        }
        // 游客用户：写入 session
        elseif ($request->session()->has('guest')) {
            $request->session()->put('practice_count', $request->session()->get('practice_count', 0) + 1);
            $request->session()->put('total_questions', $request->session()->get('total_questions', 0) + 1);

            if ($isCorrect) {
                $request->session()->put('correct_count', $request->session()->get('correct_count', 0) + 1);
            }

            $request->session()->put('study_seconds', $request->session()->get('study_seconds', 0) + $duration);
        }

        return view('practice-result', [
            'question' => $question,
            'userAnswer' => $userAnswer,
            'isCorrect' => $isCorrect,
            'duration' => $duration,
        ]);
    }
}