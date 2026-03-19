<?php

namespace App\Http\Controllers;

use App\Models\ReadingHistory;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReadingHistoryController extends Controller
{
    // Reading history list page (preserved)
    public function index()
    {
        $histories = Auth::user()->readingHistories()
            ->with('article')
            ->orderBy('read_at', 'desc')
            ->paginate(9);
        return view('history.index', compact('histories'));
    }

    // Create/update reading history (Modified: Hard delete old record first, then create new one to avoid primary key conflict)
    public function start(Request $request, $article_id)
    {
        if (empty($article_id)) {
            throw ValidationException::withMessages([
                'article_id' => 'Article ID cannot be empty'
            ]);
        }

        $article = Article::find($article_id);
        if (!$article) {
            throw ValidationException::withMessages([
                'article_id' => 'This article does not exist'
            ]);
        }

        // Critical modification: Hard delete old record first (delete() is hard delete after removing soft deletes)
        ReadingHistory::where([
            'user_id' => Auth::id(),
            'article_id' => $article_id,
        ])->delete();

        // Then create new record (replace original updateOrCreate to completely avoid primary key conflict)
        $history = ReadingHistory::create([
            'user_id' => Auth::id(),
            'article_id' => $article_id,
            'progress' => 0,
            'read_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['code' => 0, 'progress' => $history->progress]);
    }

    // Delete method: Adapted for hard delete (no logic change, only comment explanation)
    public function destroy($article_id)
    {
        if (empty($article_id)) {
            return response()->json(['code' => 1, 'message' => 'Article ID cannot be empty']);
        }

        $history = ReadingHistory::where([
            'user_id' => Auth::id(),
            'article_id' => $article_id
        ])->first();

        if (!$history) {
            return response()->json(['code' => 1, 'message' => 'Reading history does not exist']);
        }
        if ($history->user_id !== Auth::id()) {
            return response()->json(['code' => 1, 'message' => 'No operation permission']);
        }

        // After removing soft deletes, delete() here is hard delete (directly delete database record)
        $history->delete();
        return response()->json(['code' => 0, 'message' => 'Deleted successfully']);
    }

    // Clear all reading history: Adapted for hard delete
    public function clear()
    {
        // delete() here is also hard delete, which will clear all reading history of current user
        Auth::user()->readingHistories()->delete();
        return response()->json(['code' => 0, 'message' => 'Reading history cleared']);
    }

    // Update progress (preserved)
    public function updateProgress(Request $request, $article_id)
    {
        return response()->json(['code' => 0, 'message' => 'Progress update API is not enabled yet']);
    }
}