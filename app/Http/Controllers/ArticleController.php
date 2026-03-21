<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Article::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        $articles = $query->paginate(10)->withQueryString();
        
        $levels = Article::select('level')->whereNotNull('level')->where('level', '!=', '')->distinct()->pluck('level');

        return view('articles.index', compact('articles', 'levels'));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }
}