<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::all();
        return view('articles.index', compact('articles'));
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.show', compact('article'));
    }

    public function speaking($id)
    {
        $article = Article::with('segments')->findOrFail($id);
        return view('articles.speaking', compact('article'));
    }
    public function listening($id)
    {
        $article = Article::with(['segments' => function($query) {
            $query->orderBy('paragraph_index')->orderBy('sentence_index');
        }])->findOrFail($id);
        return view('articles.listening', compact('article'));
    }
}