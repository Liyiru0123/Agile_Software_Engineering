<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionAttempt;
use App\Models\WrongQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReadingAnswerController extends Controller
{
    // Constructor: Authentication required to answer questions
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Submit answers (Core fix: json_decode type safety + fallback for empty values)
    public function submit(Request $request)
    {
        try {
            $userId = Auth::id();
            $answers = $request->input('answers', []); // Format: [question_id => user_answer]

            // 1. Basic validation: Answer data cannot be empty
            if (empty($answers) || !is_array($answers)) {
                return response()->json([
                    'code' => 1,
                    'message' => 'Please answer at least one question!'
                ], 400);
            }

            $results = []; // Answer results
            foreach ($answers as $questionId => $userAnswer) {
                // 2. Verify question exists
                $question = Question::findOrFail($questionId);
                
                // 3. Safely decode correct answer (compatible with empty values/non-JSON strings)
                $correctAnswer = $this->safeJsonDecode($question->answer, []);
                if (empty($correctAnswer)) {
                    throw new \Exception("Question ID:{$questionId} has no correct answer configured");
                }

                // 4. Safely decode user answer (compatible with array/JSON string/empty value)
                $userAnswerArr = $this->safeJsonDecode($userAnswer, []);
                if (empty($userAnswerArr)) {
                    throw new \Exception("Question ID:{$questionId} no answer selected");
                }

                // 5. Determine if answer is correct (add array type validation)
                $isCorrect = false;
                if ($question->type === 'single') {
                    // Single choice: Ensure it's an array and compare first element
                    $correctSingle = is_array($correctAnswer) ? $correctAnswer[0] : $correctAnswer;
                    $userSingle = is_array($userAnswerArr) ? $userAnswerArr[0] : $userAnswerArr;
                    $isCorrect = $userSingle === $correctSingle;
                } else {
                    // Multiple choice: Compare after sorting (ensure both are arrays first)
                    $correctMulti = is_array($correctAnswer) ? $correctAnswer : [$correctAnswer];
                    $userMulti = is_array($userAnswerArr) ? $userAnswerArr : [$userAnswerArr];
                    sort($correctMulti);
                    sort($userMulti);
                    $isCorrect = $correctMulti === $userMulti;
                }

                // 6. Save answer record (hard create)
                QuestionAttempt::create([
                    'user_id' => $userId,
                    'question_id' => $questionId,
                    'user_answer' => json_encode($userAnswerArr),
                    'is_correct' => $isCorrect
                ]);

                // 7. Assemble results
                $results[] = [
                    'question_id' => $questionId,
                    'content' => $question->content,
                    'user_answer' => $userAnswerArr,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $isCorrect,
                    'explanation' => $question->explanation ?? 'No explanation'
                ];
            }

            // 8. Log record
            Log::info('User submitted answers successfully', [
                'user_id' => $userId,
                'question_count' => count($results),
                'correct_count' => collect($results)->where('is_correct', true)->count()
            ]);

            // 9. Return success response
            return response()->json([
                'code' => 0,
                'message' => 'Answers submitted successfully!',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('User failed to submit answers', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'answers' => $request->input('answers', [])
            ]);
            
            return response()->json([
                'code' => -1,
                'message' => 'Failed to submit answers: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add to wrong question notebook (Fix: User answer parsing + duplicate addition optimization)
    public function addToWrong(Request $request)
    {
        try {
            $userId = Auth::id();
            $questionId = $request->input('question_id');
            $userAnswer = $request->input('user_answer');

            // 1. Basic parameter validation
            if (empty($questionId)) {
                return response()->json([
                    'code' => 1,
                    'message' => 'Question ID cannot be empty!'
                ], 400);
            }

            // 2. Verify question exists
            $question = Question::findOrFail($questionId);

            // 3. Safely decode user answer
            $userAnswerArr = $this->safeJsonDecode($userAnswer, []);

            // 4. Avoid duplicate additions (update if exists, create if not)
            WrongQuestion::updateOrCreate(
                ['user_id' => $userId, 'question_id' => $questionId],
                [
                    'user_answer' => json_encode($userAnswerArr),
                    'updated_at' => now() // Update time when adding duplicates
                ]
            );

            Log::info('User added question to wrong notebook successfully', [
                'user_id' => $userId,
                'question_id' => $questionId,
                'article_id' => $question->article_id
            ]);

            return response()->json([
                'code' => 0,
                'message' => 'Successfully added to wrong question notebook!'
            ]);
        } catch (\Exception $e) {
            Log::error('User failed to add question to wrong notebook', [
                'user_id' => Auth::id(),
                'question_id' => $request->input('question_id'),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'code' => -1,
                'message' => 'Failed to add to wrong question notebook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Safe JSON decoding function (Core fix utility function)
     * @param mixed $data Data to decode (array/string/empty value)
     * @param mixed $default Default value when decoding fails
     * @return mixed
     */
    private function safeJsonDecode($data, $default = [])
    {
        // 1. Return directly if already an array
        if (is_array($data)) {
            return $data;
        }

        // 2. Return default value if empty or not a string
        if (empty($data) || !is_string($data)) {
            return $default;
        }

        // 3. Attempt JSON decoding for strings
        $decoded = json_decode($data, true);
        
        // 4. Return default value if decoding fails
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
    }
}