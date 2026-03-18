<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Question; // New: Import Question model
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    // Admin article list page
    public function index(Request $request)
    {
        $query = Article::query();
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
        }

        // Sort by creation time (newest first)
        $articles = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.articles.index', compact('articles'));
    }

    // Show create new article form
    public function create()
    {
        // Subject list (matches database enum field)
        $subjects = [
            'Civil Engineering',
            'Mathematics',
            'Computer Science',
            'Mechanical Engineering',
            'Mechanical Engineering with Transportation'
        ];
        
        // Difficulty list (matches database enum field)
        $levels = ['Easy', 'Intermediate', 'Advanced'];
        
        return view('admin.articles.create', compact('subjects', 'levels'));
    }

    // Save new article
    public function store(Request $request)
    {
        // Form validation - Fix text → string to adapt to TEXT type fields
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|in:Civil Engineering,Mathematics,Computer Science,Mechanical Engineering,Mechanical Engineering with Transportation',
            'level' => 'required|in:Easy,Intermediate,Advanced',
            'author' => 'required|string|max:100',
            'excerpt' => 'required|string|max:65535',
            'content' => 'required|string|max:65535',
            'word_count' => 'nullable|integer|min:0',
            'source' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug'
        ]);

        // Auto calculate word count (optimized logic to avoid empty value/special character issues)
        if (empty($validated['word_count'])) {
            $validated['word_count'] = str_word_count(strip_tags($validated['content']));
        }

        // Auto generate slug (if not filled)
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        }

        // Create article
        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully!');
    }

    // Show edit article form
    public function edit(Article $article)
    {
        $subjects = [
            'Civil Engineering',
            'Mathematics',
            'Computer Science',
            'Mechanical Engineering',
            'Mechanical Engineering with Transportation'
        ];
        
        $levels = ['Easy', 'Intermediate', 'Advanced'];
        
        return view('admin.articles.edit', compact('article', 'subjects', 'levels'));
    }

    // Update article
    public function update(Request $request, Article $article)
    {
        // Form validation - Synchronize fix text → string
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|in:Civil Engineering,Mathematics,Computer Science,Mechanical Engineering,Mechanical Engineering with Transportation',
            'level' => 'required|in:Easy,Intermediate,Advanced',
            'author' => 'required|string|max:100',
            'excerpt' => 'required|string|max:65535',
            'content' => 'required|string|max:65535',
            'word_count' => 'nullable|integer|min:0',
            'source' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->article_id
        ]);

        // Auto calculate word count (optimized logic)
        if (empty($validated['word_count'])) {
            $validated['word_count'] = str_word_count(strip_tags($validated['content']));
        }

        // Update article
        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully!');
    }

    // Delete article (Hard delete + Cascade delete questions + Exception handling)
    public function destroy(Article $article)
    {
        try {
            // ========== New core logic ==========
            // 1. Hard delete all questions for this article (cascade delete)
            Question::where('article_id', $article->article_id)->delete();
            // ========== End of new core logic ==========

            // 2. Hard delete article (permanently remove from database)
            $article->forceDelete(); 

            Log::info('Admin hard deleted article successfully', [
                'admin_id' => auth()->id(),
                'article_id' => $article->article_id,
                'article_title' => $article->title,
                'deleted_questions' => "All questions under this article have been hard deleted synchronously"
            ]);

            return response()->json([
                'code' => 0,
                'message' => 'Article and associated questions have been permanently deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to hard delete article', [
                'article_id' => $article->article_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'code' => -1,
                'message' => 'Failed to delete article: ' . $e->getMessage()
            ], 500);
        }
    }

    // New: Batch delete articles (Hard delete + Cascade delete associated questions)
    public function batchDestroy(Request $request)
    {
        $articleIds = $request->input('article_ids', []);
        
        if (empty($articleIds) || !is_array($articleIds)) {
            return response()->json([
                'code' => 1,
                'message' => 'Please select articles to delete'
            ], 400);
        }

        try {
            // Filter valid IDs
            $articleIds = array_filter($articleIds, function($id) {
                return is_numeric($id) && (int)$id > 0;
            });

            if (empty($articleIds)) {
                return response()->json([
                    'code' => 1,
                    'message' => 'Please select valid article IDs'
                ], 400);
            }

            // ========== New core logic ==========
            // 1. Batch hard delete all questions for selected articles
            Question::whereIn('article_id', $articleIds)->delete();
            // ========== End of new core logic ==========

            // 2. Batch hard delete articles
            $deletedCount = Article::whereIn('article_id', $articleIds)->forceDelete();

            Log::info('Admin batch hard deleted articles successfully', [
                'admin_id' => auth()->id(),
                'deleted_ids' => $articleIds,
                'deleted_count' => $deletedCount,
                'deleted_questions' => "All questions under selected articles have been hard deleted synchronously"
            ]);

            return response()->json([
                'code' => 0,
                'message' => "Successfully permanently deleted {$deletedCount} articles and associated questions",
                'deleted_ids' => $articleIds
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to batch hard delete articles', [
                'error' => $e->getMessage(),
                'article_ids' => $articleIds
            ]);
            
            return response()->json([
                'code' => -1,
                'message' => 'Batch deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}