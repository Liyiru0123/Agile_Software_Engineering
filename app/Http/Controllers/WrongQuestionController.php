<?php

namespace App\Http\Controllers;

use App\Models\WrongQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WrongQuestionController extends Controller
{
    // Constructor: Authentication required to access wrong question notebook
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Wrong question notebook list page
    public function index()
    {
        $userId = Auth::id();
        $wrongQuestions = WrongQuestion::with(['question.article'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('wrong-questions.index', compact('wrongQuestions'));
    }

    // Hard delete wrong question record (final optimized version)
    public function destroy(WrongQuestion $wrongQuestion)
    {
        try {
            // Permission check: Only allow deleting own wrong questions
            if ($wrongQuestion->user_id !== Auth::id()) {
                return response()->json([
                    'code' => 1,
                    'message' => 'No permission to delete this wrong question!'
                ], 403);
            }

            // Log operation details
            $logData = [
                'user_id' => Auth::id(),
                'wrong_question_id' => $wrongQuestion->wrong_question_id,
                'question_id' => $wrongQuestion->question_id
            ];
            Log::info('User deleted wrong question successfully', $logData);

            // Execute deletion
            $wrongQuestion->delete();

            return response()->json([
                'code' => 0,
                'message' => 'Wrong question deleted successfully!'
            ]);
        } catch (ModelNotFoundException $e) {
            Log::warning('User failed to delete wrong question: Record does not exist', [
                'user_id' => Auth::id(),
                'wrong_question_id' => request()->route('wrongQuestion'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'code' => 2,
                'message' => 'Wrong question does not exist or has been deleted!'
            ], 404);
        } catch (\Exception $e) {
            Log::error('User failed to delete wrong question', [
                'user_id' => Auth::id(),
                'wrong_question_id' => request()->route('wrongQuestion'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'code' => -1,
                'message' => 'Failed to delete wrong question: ' . $e->getMessage()
            ], 500);
        }
    }

    // Supplement: Clear all wrong questions (for batch deletion)
    public function clear()
    {
        try {
            $userId = Auth::id();
            WrongQuestion::where('user_id', $userId)->delete();
            
            Log::info('User cleared all wrong questions successfully', ['user_id' => $userId]);
            return response()->json([
                'code' => 0,
                'message' => 'All wrong questions have been cleared!'
            ]);
        } catch (\Exception $e) {
            Log::error('User failed to clear wrong questions', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'code' => -1,
                'message' => 'Clear failed: ' . $e->getMessage()
            ], 500);
        }
    }
}