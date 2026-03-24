<?php

namespace App\Http\Controllers;

use App\Models\WordBook;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WordBookController extends Controller
{
    /**
     * Query user's word book (pagination + filter)
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id; // Get user ID from authentication
        $memStatus = $request->query('mem_status'); // Filter by memory status

        $query = WordBook::where('user_id', $userId);
        if ($memStatus !== null && in_array($memStatus, [0, 1, 2])) {
            $query->where('mem_status', $memStatus);
        }

        $list = $query->orderBy('created_at', 'desc')
            ->paginate($request->query('size', 10));

        return response()->json([
            'code' => 200,
            'msg' => 'success',
            'data' => $list
        ]);
    }

  
    public function store(Request $request)
    {
        // Validate parameters
        $validated = $request->validate([
            'word' => 'required|string|max:100',
            'phonetic' => 'nullable|string|max:200',
            'paraphrase' => 'nullable|string',
            'mem_status' => 'nullable|integer|in:0,1,2'
        ]);

        $userId = $request->user()->id;
        // Avoid duplicate addition
        $exists = WordBook::where(['user_id' => $userId, 'word' => $validated['word']])->exists();
        if ($exists) {
            return response()->json([
                'code' => 400,
                'msg' => 'This word has already been added to your word book'
            ], 400);
        }

        $wordBook = WordBook::create([
            'user_id' => $userId,
            'word' => $validated['word'],
            'phonetic' => $validated['phonetic'] ?? '',
            'paraphrase' => $validated['paraphrase'] ?? '',
            'mem_status' => $validated['mem_status'] ?? 0
        ]);

        return response()->json([
            'code' => 200,
            'msg' => 'Added successfully',
            'data' => $wordBook
        ]);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mem_status' => 'required|integer|in:0,1,2'
        ]);

        $wordBook = WordBook::where(['id' => $id, 'user_id' => $request->user()->id])->first();
        if (!$wordBook) {
            return response()->json([
                'code' => 404,
                'msg' => 'The word does not exist'
            ], 404);
        }

        $wordBook->update(['mem_status' => $validated['mem_status']]);

        return response()->json([
            'code' => 200,
            'msg' => 'Updated successfully',
            'data' => $wordBook
        ]);
    }

    public function destroy($id, Request $request)
    {
        $userId = $request->user()->id;
        $count = WordBook::where(['id' => $id, 'user_id' => $userId])->delete();

        if ($count === 0) {
            return response()->json([
                'code' => 404,
                'msg' => 'The word does not exist'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'msg' => 'Deleted successfully'
        ]);
    }

    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'words' => 'required|array',
            'words.*.word' => 'required|string|max:100',
            'words.*.phonetic' => 'nullable|string|max:200',
            'words.*.paraphrase' => 'nullable|string',
            'words.*.mem_status' => 'nullable|integer|in:0,1,2'
        ]);

        $userId = $request->user()->id;
        $insertData = [];
        $existWords = [];

        // Query existing words first to avoid duplicates
        $wordList = collect($validated['words'])->pluck('word')->toArray();
        $exists = WordBook::where('user_id', $userId)
            ->whereIn('word', $wordList)
            ->pluck('word')
            ->toArray();

        foreach ($validated['words'] as $item) {
            if (in_array($item['word'], $exists)) {
                $existWords[] = $item['word'];
                continue;
            }
            $insertData[] = [
                'user_id' => $userId,
                'word' => $item['word'],
                'phonetic' => $item['phonetic'] ?? '',
                'paraphrase' => $item['paraphrase'] ?? '',
                'mem_status' => $item['mem_status'] ?? 0,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Batch insert
        if (!empty($insertData)) {
            WordBook::insert($insertData);
        }

        return response()->json([
            'code' => 200,
            'msg' => 'Batch import completed',
            'data' => [
                'success_count' => count($insertData),
                'exist_words' => $existWords
            ]
        ]);
    }
}