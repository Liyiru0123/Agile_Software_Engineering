<?php

namespace App\Http\Controllers;

use App\Models\LearningRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningRecordController extends Controller
{
    /**
     * Start learning: Create learning record (only record start time)
     */
    public function startLearning(Request $request)
    {
        $userId = $request->user()->id;
        $learningType = $request->input('learning_type', 'unknown'); // Optional: Learning type

        // Check for unfinished learning records (avoid duplicate start)
        $unfinishedRecord = LearningRecord::where([
            'user_id' => $userId,
            'end_time' => null
        ])->latest('start_time')->first();

        if ($unfinishedRecord) {
            return response()->json([
                'code' => 400,
                'msg' => 'There is an unfinished learning record, cannot start again',
                'data' => ['record_id' => $unfinishedRecord->id]
            ], 400);
        }

        // Create new learning record
        $record = LearningRecord::create([
            'user_id' => $userId,
            'start_time' => Carbon::now(), // Current time as start time
            'learning_type' => $learningType,
            'duration' => 0 // Initial duration is 0
        ]);

        return response()->json([
            'code' => 200,
            'msg' => 'Start learning successfully',
            'data' => ['record_id' => $record->id]
        ]);
    }

    /**
     * End learning: Update end time and calculate learning duration
     */
    public function endLearning(Request $request)
    {
        $userId = $request->user()->id;
        $recordId = $request->input('record_id'); // Optional: Specify record ID to end

        // Find unfinished learning record
        $query = LearningRecord::where([
            'user_id' => $userId,
            'end_time' => null
        ]);
        if ($recordId) {
            $query->where('id', $recordId);
        }
        $record = $query->latest('start_time')->first();

        if (!$record) {
            return response()->json([
                'code' => 404,
                'msg' => 'No unfinished learning record found'
            ], 404);
        }

        // Calculate learning duration (seconds)
        $endTime = Carbon::now();
        $startTime = Carbon::parse($record->start_time);
        $duration = $endTime->diffInSeconds($startTime);

        // Filter abnormal data (e.g. duration < 5 seconds, regarded as misoperation)
        if ($duration < 5) {
            $record->delete(); // Delete invalid record
            return response()->json([
                'code' => 200,
                'msg' => 'Learning duration is too short, record has been ignored'
            ]);
        }

        // Update record
        $record->update([
            'end_time' => $endTime,
            'duration' => $duration
        ]);

        return response()->json([
            'code' => 200,
            'msg' => 'End learning successfully',
            'data' => [
                'record_id' => $record->id,
                'duration' => $duration, // Seconds
                'duration_min' => round($duration / 60, 1) // Minutes (1 decimal place)
            ]
        ]);
    }

    /**
     * Query learning duration statistics (today/this week/this month/total duration)
     */
    public function getLearningStat(Request $request)
    {
        $userId = $request->user()->id;
        $now = Carbon::now();

        // Today: 00:00:00 to current time
        $todayStart = $now->copy()->startOfDay();
        $todayDuration = LearningRecord::where('user_id', $userId)
            ->where('end_time', '>=', $todayStart)
            ->sum('duration');

        // This week: Monday 00:00:00 to current time (Laravel defaults to Monday as week start)
        $weekStart = $now->copy()->startOfWeek();
        $weekDuration = LearningRecord::where('user_id', $userId)
            ->where('end_time', '>=', $weekStart)
            ->sum('duration');

        // This month: 1st 00:00:00 to current time
        $monthStart = $now->copy()->startOfMonth();
        $monthDuration = LearningRecord::where('user_id', $userId)
            ->where('end_time', '>=', $monthStart)
            ->sum('duration');

        // Total duration
        $totalDuration = LearningRecord::where('user_id', $userId)
            ->sum('duration');

        // Convert to minutes (more readable)
        $formatDuration = function ($seconds) {
            return round($seconds / 60, 1);
        };

        return response()->json([
            'code' => 200,
            'msg' => 'Query successful',
            'data' => [
                'today' => $formatDuration($todayDuration), // Today's duration (minutes)
                'week' => $formatDuration($weekDuration),   // This week's duration (minutes)
                'month' => $formatDuration($monthDuration), // This month's duration (minutes)
                'total' => $formatDuration($totalDuration), // Total duration (minutes)
                'unit' => 'minutes'
            ]
        ]);
    }

    /**
     * Query learning trend (by day/week)
     */
    public function getLearningTrend(Request $request)
    {
        $userId = $request->user()->id;
        $type = $request->input('type', 'day'); // day-last 7 days, week-last 4 weeks
        $now = Carbon::now();

        if ($type === 'day') {
            // Trend for last 7 days (statistics by day)
            $days = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->format('Y-m-d');
                $start = Carbon::parse($date)->startOfDay();
                $end = Carbon::parse($date)->endOfDay();

                $duration = LearningRecord::where('user_id', $userId)
                    ->whereBetween('end_time', [$start, $end])
                    ->sum('duration');

                $days[] = [
                    'date' => $date,
                    'duration_min' => round($duration / 60, 1) // Minutes
                ];
            }

            $data = $days;
        } else {
            // Trend for last 4 weeks (statistics by week)
            $weeks = [];
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $weekLabel = $weekStart->format('Y-m-d') . ' to ' . $weekEnd->format('Y-m-d');

                $duration = LearningRecord::where('user_id', $userId)
                    ->whereBetween('end_time', [$weekStart, $weekEnd])
                    ->sum('duration');

                $weeks[] = [
                    'week' => $weekLabel,
                    'duration_min' => round($duration / 60, 1) // Minutes
                ];
            }

            $data = $weeks;
        }

        return response()->json([
            'code' => 200,
            'msg' => 'Query successful',
            'data' => [
                'type' => $type,
                'trend' => $data,
                'unit' => 'minutes'
            ]
        ]);
    }
}