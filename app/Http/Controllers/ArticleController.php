<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\QuestionAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    // Homepage method (must exist)
    public function home()
    {
        // Get 6 featured articles sorted by creation time (newest first)
        $featuredArticles = Article::orderBy('created_at', 'desc')->take(6)->get();
        return view('home', compact('featuredArticles'));
    }

    // Article list page
    public function index(Request $request)
    {
        $query = Article::query();

        // Filter condition - Replace category with subject (adapt to table structure)
        if ($request->has('subject')) {
            $query->where('subject', $request->subject);
        }
        // Difficulty filter - Adapt to enumeration values in table (Easy/Intermediate/Advanced)
        if ($request->has('level')) {
            // Unify frontend parameters to lowercase, convert to capitalized format in table
            $levelMap = [
                'easy' => 'Easy',
                'intermediate' => 'Intermediate',
                'advanced' => 'Advanced'
            ];
            $level = $levelMap[$request->level] ?? $request->level;
            $query->where('level', $level);
        }
        // Keyword search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
        }

        // Sorting - Replace views with read_count (read count field)
        if ($request->sort === 'popular') {
            $query->orderBy('read_count', 'desc'); // Sort by read count
        } else {
            $query->orderBy('created_at', 'desc'); // Sort by latest published
        }

        $articles = $query->paginate(9);

        // Fix: Change $article->id to $article->article_id
        if (auth()->check()) {
            $favoritedIds = auth()->user()->favorites->pluck('article_id')->toArray();
            $articles->each(function ($article) use ($favoritedIds) {
                $article->is_favorited = in_array($article->article_id, $favoritedIds);
            });
        }

        return view('articles.index', compact('articles'));
    }

    // Article detail page (New: Load questions + Clear answer records)
    public function show(Article $article)
    {
        // Increment read count by 1 (update on each detail page visit)
        $article->increment('read_count');
        
        // Fix 1: Change where('id') to where('article_id'), $article->id to $article->article_id
        $relatedArticles = Article::where('subject', $article->subject)
                                  ->where('article_id', '!=', $article->article_id)
                                  ->take(3)->get();
        
        // Fix 2: Change $article->id to $article->article_id
        $article->is_favorited = auth()->check() 
            ? auth()->user()->favorites->contains('article_id', $article->article_id) 
            : false;

        // ========== New core logic ==========
        // 1. Clear current user's answer records for this article (clear when re-entering reading interface)
        if (Auth::check()) {
            QuestionAttempt::where('user_id', Auth::id())
                ->whereHas('question', function($query) use ($article) {
                    $query->where('article_id', $article->article_id);
                })->delete(); // Hard delete answer records
        }

        // 2. Load all questions for this article (attach to detail page)
        $questions = $article->questions()->get();

        // ========== End of new core logic ==========

        // Pass question data to view
        return view('articles.show', compact('article', 'relatedArticles', 'questions'));
    }
}