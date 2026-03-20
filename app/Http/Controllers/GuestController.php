<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    public function start(Request $request)
    {
        $request->session()->put('guest', [
            'id' => (string) Str::uuid(),
            'name' => '游客用户',
            'practice_count' => 0,
            'correct_count' => 0,
            'total_questions' => 0,
            'study_seconds' => 0,
        ]);

        return redirect()->route('study.profile');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('guest');

        return redirect()->route('identity.choose');
    }
}