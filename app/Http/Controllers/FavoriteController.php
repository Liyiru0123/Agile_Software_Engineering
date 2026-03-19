<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    /**
     * My favorite articles list
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()
            ->with('article')
            ->latest('created_at')
            ->paginate(9);
        
        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite/unfavorite (Core API, hard delete + parameter validation)
     * @param int $article_id Article ID
     */
    public function toggle($article_id)
    {
        if (!Auth::check()) {
            return response()->json(['code' => 1, 'message' => 'Please login first before performing this operation']);
        }

        // ========== New: Validate article_id is positive integer ==========
        if (!is_numeric($article_id) || (int)$article_id <= 0) {
            return response()->json(['code' => 1, 'message' => 'Article ID must be a positive integer']);
        }
        $article_id = (int)$article_id;

        try {
            $user_id = Auth::id();
            $favorite = Favorite::where('user_id', $user_id)
                ->where('article_id', $article_id)
                ->first();

            if ($favorite) {
                // Hard delete: Permanently remove record from database
                $favorite->forceDelete();
                return response()->json([
                    'code' => 0, 
                    'message' => 'Unfavorite successful', 
                    'action' => 'unfavorite'
                ]);
            } else {
                // Add to favorites
                Favorite::create([
                    'user_id' => $user_id,
                    'article_id' => $article_id,
                ]);
                return response()->json([
                    'code' => 0, 
                    'message' => 'Favorite successful', 
                    'action' => 'favorite'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Favorite operation failed', ['article_id' => $article_id, 'error' => $e->getMessage()]);
            return response()->json([
                'code' => -1,
                'message' => 'Operation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch unfavorite (Compatible with all request formats + hard delete + parameter filtering)
     */
    public function batchDelete(Request $request)
    {
        // Compatible with JSON/FormData/normal form requests
        $article_ids = [];
        if ($request->isJson()) {
            $data = $request->json()->all();
            $article_ids = $data['article_ids'] ?? [];
        } else {
            $article_ids = $request->input('article_ids', []);
        }

        // Process array format (compatible with string/array)
        if (!is_array($article_ids)) {
            $article_ids = explode(',', $article_ids);
        }

        // ========== New: Filter non-positive integer IDs ==========
        $article_ids = array_filter($article_ids, function($id) {
            return is_numeric($id) && (int)$id > 0;
        });
        $article_ids = array_map('intval', $article_ids); // Convert all to integers

        if (empty($article_ids)) {
            return response()->json(['code' => 1, 'message' => 'Please select valid favorite IDs (positive integers)']);
        }

        try {
            // Hard delete: Permanently remove current user's favorites
            $deletedCount = Auth::user()->favorites()
                ->whereIn('article_id', $article_ids)
                ->forceDelete();

            return response()->json([
                'code' => 0,
                'message' => "Batch unfavorite successful, {$deletedCount} records deleted",
                'deleted_ids' => $article_ids // New: Return actually deleted IDs
            ]);
        } catch (\Exception $e) {
            Log::error('Batch delete favorites failed', ['article_ids' => $article_ids, 'error' => $e->getMessage()]);
            return response()->json([
                'code' => -1,
                'message' => 'Batch delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}