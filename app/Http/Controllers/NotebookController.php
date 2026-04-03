<?php

namespace App\Http\Controllers;

use App\Models\SelectionFavorite;
use Illuminate\Contracts\View\View;

class NotebookController extends Controller
{
    public function index(): View
    {
        $notes = SelectionFavorite::query()
            ->with('article')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('notebook.index', [
            'notes' => $notes,
        ]);
    }

    public function review(): View
    {
        $notes = SelectionFavorite::query()
            ->with('article')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get()
            ->map(function (SelectionFavorite $note) {
                return [
                    'id' => $note->id,
                    'selected_text' => $note->selected_text,
                    'translated_text' => $note->translated_text,
                    'paragraph_text' => $note->paragraph_text,
                    'article_title' => $note->article?->title ?? 'Untitled Article',
                    'article_url' => route('articles.show', $note->article_id),
                    'created_at' => optional($note->created_at)?->format('Y-m-d H:i'),
                ];
            })
            ->values();

        return view('notebook.review', [
            'notes' => $notes,
        ]);
    }
}
