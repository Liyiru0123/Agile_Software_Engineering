<?php

namespace App\Http\Controllers;

use App\Models\ReadingHistory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class HistoryController extends Controller
{
    public function index(): View
    {
        $history = ReadingHistory::query()
            ->with('article')
            ->where('user_id', auth()->id())
            ->orderByDesc('last_viewed_at')
            ->paginate(12);

        return view('history.index', [
            'history' => $history,
        ]);
    }

    public function continue(): RedirectResponse
    {
        $history = ReadingHistory::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('last_viewed_at')
            ->first();

        if (! $history || ! $history->continue_url) {
            return redirect()->route('articles.index');
        }

        return redirect()->to($history->continue_url);
    }
}
