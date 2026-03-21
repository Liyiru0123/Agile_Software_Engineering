<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudyProfileController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();

            $currentUser = $user->name;
            $practiceCount = $user->practice_count ?? 0;
            $correctCount = $user->correct_count ?? 0;
            $totalQuestions = $user->total_questions ?? 0;
            $studySeconds = $user->study_seconds ?? 0;
        } elseif ($request->session()->has('guest')) {
            $guest = $request->session()->get('guest');

            $currentUser = $guest['name'] ?? '游客用户';
            $practiceCount = $request->session()->get('practice_count', 0);
            $correctCount = $request->session()->get('correct_count', 0);
            $totalQuestions = $request->session()->get('total_questions', 0);
            $studySeconds = $request->session()->get('study_seconds', 0);
        } else {
            return redirect()->route('identity.choose');
        }

        $accuracy = $totalQuestions > 0
            ? round(($correctCount / $totalQuestions) * 100, 2)
            : 0;

        return view('study-profile', [
            'currentUser' => $currentUser,
            'practiceCount' => $practiceCount,
            'accuracy' => $accuracy,
            'studySeconds' => $studySeconds,
        ]);
    }
}