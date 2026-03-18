<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    // Constructor: Apply admin middleware
    public function __construct()
    {
        $this->middleware('admin'); // Use AdminMiddleware to verify administrator privileges
    }

    // Question list (filter by article/question type)
    public function index(Request $request)
    {
        $query = Question::query();
        
        // Filter by article
        if ($request->has('article_id') && $request->article_id) {
            $query->where('article_id', $request->article_id);
        }

        // Filter by question type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(10);
        $articles = Article::all(); // For filter dropdown

        return view('admin.questions.index', compact('questions', 'articles'));
    }

    // Show create new question form
    public function create(Request $request)
    {
        $articleId = $request->input('article_id', 0);
        $article = $articleId ? Article::findOrFail($articleId) : null;
        $articles = Article::all(); // All articles list
        $types = [
            'single' => 'Single Choice',
            'multiple' => 'Multiple Choice'
        ];

        return view('admin.questions.create', compact('article', 'articles', 'types'));
    }

    // Save new question (Optimized error messages + Compatible data formats)
    public function store(Request $request)
    {
        try {
            // 1. Form validation (Add more user-friendly error messages)
            $validated = $request->validate([
                'article_id' => 'required|exists:articles,article_id',
                'content' => 'required|string|max:65535',
                'options' => 'required|json', // Validate JSON string
                'answer_array' => 'required|json', // Validate answer JSON
                'type' => 'required|in:single,multiple',
                'explanation' => 'nullable|string|max:65535'
            ], [
                'article_id.required' => 'Please select the associated article',
                'article_id.exists' => 'The selected article does not exist',
                'content.required' => 'Please enter question content',
                'options.required' => 'Please fill in question options',
                'options.json' => 'Invalid option format, please check your input',
                'answer_array.required' => 'Please fill in the correct answer',
                'answer_array.json' => 'Invalid answer format',
                'type.required' => 'Please select question type'
            ]);

            // 2. Convert JSON to array and validate
            $options = json_decode($validated['options'], true);
            $answerArray = json_decode($validated['answer_array'], true);
            
            // Validate options array
            if (!is_array($options) || empty($options)) {
                throw ValidationException::withMessages(['options' => 'Options cannot be empty and must be in array format']);
            }
            
            // Validate answer array
            if (!is_array($answerArray) || empty($answerArray)) {
                throw ValidationException::withMessages(['answer_array' => 'Correct answer cannot be empty']);
            }
            
            // Validate single choice answer count
            if ($validated['type'] === 'single' && count($answerArray) > 1) {
                throw ValidationException::withMessages(['answer_array' => 'Single choice questions can only have one correct answer']);
            }
            
            // Validate if answers exist in options
            foreach ($answerArray as $ans) {
                if (!isset($options[$ans])) {
                    throw ValidationException::withMessages(['answer_array' => "Answer「{$ans}」is not in the options list"]);
                }
            }

            // 3. Prepare storage data
            $questionData = [
                'article_id' => $validated['article_id'],
                'type' => $validated['type'],
                'content' => $validated['content'],
                'options' => $options, // Store array directly (ensure database field is JSON type)
                'answer' => $answerArray, // Store answer array
                'explanation' => $validated['explanation'] ?? ''
            ];

            // 4. Create question
            Question::create($questionData);

            Log::info('Admin created new question successfully', [
                'admin_id' => auth()->id(),
                'article_id' => $validated['article_id'],
                'question_content' => mb_substr($validated['content'], 0, 50)
            ]);

            return redirect()->route('admin.questions.index', ['article_id' => $validated['article_id']])
                ->with('success', 'Question created successfully!');
        } catch (ValidationException $e) {
            // Validation failed: Return errors and preserve input
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Other errors: Log and prompt
            Log::error('Admin failed to create new question', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create question: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show edit question form
    public function edit(Question $question)
    {
        $articles = Article::all();
        $types = [
            'single' => 'Single Choice',
            'multiple' => 'Multiple Choice'
        ];

        return view('admin.questions.edit', compact('question', 'articles', 'types'));
    }

    // Update question
    public function update(Request $request, Question $question)
    {
        try {
            // Form validation
            $validated = $request->validate([
                'article_id' => 'required|exists:articles,article_id',
                'content' => 'required|string|max:65535',
                'options' => 'required|json',
                'answer_array' => 'required|json',
                'type' => 'required|in:single,multiple',
                'explanation' => 'nullable|string|max:65535'
            ], [
                'article_id.required' => 'Please select the associated article',
                'article_id.exists' => 'The selected article does not exist',
                'content.required' => 'Please enter question content',
                'options.required' => 'Please fill in question options',
                'options.json' => 'Invalid option format, please check your input',
                'answer_array.required' => 'Please fill in the correct answer',
                'answer_array.json' => 'Invalid answer format',
                'type.required' => 'Please select question type'
            ]);

            // Convert JSON to array
            $validated['options'] = json_decode($validated['options'], true);
            $validated['answer'] = json_decode($validated['answer_array'], true);
            
            // Remove temporary field
            unset($validated['answer_array']);

            // Update question
            $question->update($validated);

            Log::info('Admin updated question successfully', [
                'admin_id' => auth()->id(),
                'question_id' => $question->question_id,
                'article_id' => $validated['article_id']
            ]);

            return redirect()->route('admin.questions.index', ['article_id' => $validated['article_id']])
                ->with('success', 'Question updated successfully!');
        } catch (ValidationException $e) {
            // Handle echo data when validation fails
            $input = $request->all();
            if (!empty($input['options']) && is_string($input['options'])) {
                $input['options'] = json_decode($input['options'], true) ?: [];
            }
            if (!empty($input['answer_array']) && is_string($input['answer_array'])) {
                $input['answer'] = implode(',', json_decode($input['answer_array'], true) ?: []);
                unset($input['answer_array']);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput($input);
        } catch (\Exception $e) {
            Log::error('Admin failed to update question', [
                'question_id' => $question->question_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update question: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Delete question
    public function destroy(Question $question)
    {
        try {
            $questionId = $question->question_id;
            $articleId = $question->article_id;

            $question->forceDelete();

            Log::info('Admin hard deleted question successfully', [
                'admin_id' => auth()->id(),
                'question_id' => $questionId,
                'article_id' => $articleId
            ]);

            return response()->json([
                'code' => 0,
                'message' => 'Question has been permanently deleted, associated records in wrong question notebook have been deleted synchronously!'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin failed to hard delete question', [
                'question_id' => $question->question_id ?? $request->route('question'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'code' => -1,
                'message' => 'Failed to delete question: ' . $e->getMessage()
            ], 500);
        }
    }
}