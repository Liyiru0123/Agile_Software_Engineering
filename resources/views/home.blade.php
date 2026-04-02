
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $timeRange = request('range', '7d');
    $selectedDate = request('date', $today ?? now()->toDateString());
    $currentMonth = $currentMonth ?? now()->format('Y-m');

    $actionSummary = $actionSummary ?? [
        'today_total' => 0,
        'today_done' => 0,
        'today_pending' => 0,
        'today_overdue' => 0,
        'estimated_minutes' => 0,
    ];

    $calendarPlans = collect($calendarPlans ?? []);

    $selectedTasks = collect($selectedTasks ?? []);
    $upcomingTasks = collect($upcomingTasks ?? []);
    $overdueTasks = collect($overdueTasks ?? []);

    $favorites = collect($favorites ?? []);
    $favoritesSummary = $favoritesSummary ?? [
        'total' => $favorites->count(),
        'unstarted' => 0,
        'recent' => $favorites->take(3),
    ];

    $history = collect($history ?? []);
    $historySummary = $historySummary ?? [
        'recent' => $history->take(5),
        'active_days_7d' => 0,
        'continue_url' => null,
    ];

    $notebook = collect($notebook ?? []);
    $notebookSummary = $notebookSummary ?? [
        'new_this_week' => 0,
        'review_pending' => 0,
        'recent' => $notebook->take(3),
    ];

    $analysis = $analysis ?? [
        'study_minutes' => 0,
        'streak_days' => 0,
        'listening_accuracy' => 0,
        'reading_accuracy' => 0,
        'overall_accuracy' => 0,
        'trend_label' => 'Stable learning pace',
    ];

    $monthDate = \Carbon\Carbon::parse($currentMonth . '-01');
    $monthStart = $monthDate->copy()->startOfMonth();
    $monthEnd = $monthDate->copy()->endOfMonth();
    $startPadding = $monthStart->dayOfWeek;
    $daysInMonth = $monthEnd->day;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- Header --}}
    <section class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE]/90 shadow-sm p-6">
        <div>
            <div>
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-[#4A2C2A]">Learning Hub</h1>
                <p class="mt-2 text-[#8B6B47]">Act first, then reflect. Start with one task today.</p>
            </div>
        </div>
    </section>

    {{-- Primary Entry: Start Now + Calendar Link --}}
    <section class="grid grid-cols-1 xl:grid-cols-12 items-stretch gap-6">
        {{-- Start Now --}}
        <div class="xl:col-span-5 h-full flex flex-col gap-4">
            {{-- Module 1: Summary + Start Today --}}
            <div class="rounded-2xl bg-[#4A2C2A] text-[#F5E6D3] shadow-xl p-5 sm:p-6 space-y-5">
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-[#FDF7EE]/10 border border-white/15 p-4">
                        <div class="text-xs text-[#D4B970]">Today's Tasks</div>
                        <div class="mt-1 text-2xl font-black">{{ $actionSummary['today_total'] }}</div>
                    </div>
                    <div class="rounded-xl bg-[#FDF7EE]/10 border border-white/15 p-4">
                        <div class="text-xs text-[#D4B970]">Estimated Time</div>
                        <div class="mt-1 text-2xl font-black">{{ $actionSummary['estimated_minutes'] }}m</div>
                    </div>
                    <div class="rounded-xl bg-[#FDF7EE]/10 border border-white/15 p-4">
                        <div class="text-xs text-[#D4B970]">Completed</div>
                        <div class="mt-1 text-2xl font-black">{{ $actionSummary['today_done'] }}</div>
                    </div>
                    <div class="rounded-xl bg-[#FDF7EE]/10 border border-white/15 p-4">
                        <div class="text-xs text-[#D4B970]">Pending</div>
                        <div class="mt-1 text-2xl font-black">{{ $actionSummary['today_pending'] + $actionSummary['today_overdue'] }}</div>
                    </div>
                </div>

                <a href="{{ url('/study/start?date=' . $selectedDate . '&range=' . $timeRange) }}"
                   class="w-full inline-flex justify-center items-center rounded-xl bg-[#FDF7EE] hover:bg-white text-[#4A2C2A] font-black text-2xl tracking-wide px-4 py-4 ring-2 ring-[#D4B970] ring-offset-2 ring-offset-[#4A2C2A] shadow-[0_10px_30px_rgba(0,0,0,0.28)] hover:scale-[1.01] transition">
                    Start Today
                </a>
            </div>

            {{-- Module 2: Plan + Action Tasks --}}
            <div class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] text-[#4A2C2A] shadow-sm p-5 sm:p-6 flex-1">
                @php
                    $overdueCount = $overdueTasks->count();
                    $todayCount = $selectedTasks->count();
                    $upcomingCount = $upcomingTasks->count();
                    $rawTotal = $overdueCount + $todayCount + $upcomingCount;
                    $safeTotal = max($rawTotal, 1);

                    $overduePercent = (int) round(($overdueCount / $safeTotal) * 100);
                    $todayPercent = (int) round(($todayCount / $safeTotal) * 100);
                    $upcomingPercent = max(0, 100 - $overduePercent - $todayPercent);

                    $overdueDeg = round(($overdueCount / $safeTotal) * 360, 2);
                    $todayDeg = round(($todayCount / $safeTotal) * 360, 2);
                    $upcomingStart = $overdueDeg + $todayDeg;

                    $pieStyle = $rawTotal > 0
                        ? "background: conic-gradient(#C9A961 0deg {$overdueDeg}deg, #8B6B47 {$overdueDeg}deg {$upcomingStart}deg, #D9C6A8 {$upcomingStart}deg 360deg);"
                        : "background: #E9DDCC";
                @endphp

                <div class="h-full flex flex-col items-center gap-5">
                    <div class="w-full flex items-center justify-between gap-3">
                        <h4 class="text-base font-black text-[#4A2C2A]">Plan Execution</h4>
                        <a href="{{ url('/plans?date=' . $selectedDate) }}"
                           class="inline-flex items-center justify-center rounded-lg border border-[#C9A961] bg-[#4A2C2A] px-3 py-1.5 text-xs font-bold text-[#FDF7EE] hover:bg-[#6B3D2E] transition whitespace-nowrap">
                            Manage All Plans
                        </a>
                    </div>

                    <div class="relative mt-1 w-56 h-56 rounded-full border border-[#D8C3A6] shadow-[0_0_0_4px_rgba(74,44,42,0.06)]" style="{{ $pieStyle }}">
                        <div class="absolute inset-[28%] rounded-full bg-[#FDF7EE] border border-[#D8C3A6] flex flex-col items-center justify-center">
                            <span class="text-2xl font-black text-[#4A2C2A]">{{ $rawTotal }}</span>
                            <span class="text-[11px] tracking-wide text-[#8B6B47]">Total</span>
                        </div>

                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-md bg-[#F5E6D3] border border-[#C9A961] px-2 py-1 text-[11px] font-bold text-[#4A2C2A]">
                            Overdue {{ $rawTotal > 0 ? $overduePercent : 0 }}%
                        </div>
                        <div class="absolute top-1/2 -right-8 -translate-y-1/2 rounded-md bg-[#F5E6D3] border border-[#C9A961] px-2 py-1 text-[11px] font-bold text-[#4A2C2A]">
                            Today {{ $rawTotal > 0 ? $todayPercent : 0 }}%
                        </div>
                        <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 rounded-md bg-[#F5E6D3] border border-[#C9A961] px-2 py-1 text-[11px] font-bold text-[#4A2C2A]">
                            Upcoming {{ $rawTotal > 0 ? $upcomingPercent : 0 }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Calendar + Action Link --}}
        <div class="xl:col-span-7 h-full rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-5 sm:p-6 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="text-2xl font-black text-[#4A2C2A]">Study Calendar</h3>
                    <p class="text-sm text-[#A58A6A]">Select a date to sync your action tasks.</p>
                </div>
                <div class="text-sm font-semibold text-[#8B6B47]">{{ $monthDate->format('F Y') }}</div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs font-bold text-[#A58A6A]">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                    <div class="py-2">{{ $d }}</div>
                @endforeach
            </div>

            <div class="grid grid-cols-7 gap-1">
                @for($i = 0; $i < $startPadding; $i++)
                    <div class="aspect-square"></div>
                @endfor

                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = $monthDate->copy()->day($day)->format('Y-m-d');
                        $plan = $calendarPlans->get($dateStr);
                        $isSelected = $selectedDate === $dateStr;
                        $status = $plan->status ?? null;
                        $isOverdue = ($plan && ($plan->status ?? '') === 'pending' && $dateStr < now()->toDateString());

                        if ($isOverdue) {
                            $cell = 'bg-red-50 border-red-300 text-red-700';
                            $dot = 'bg-red-500';
                        } elseif ($status === 'completed') {
                            $cell = 'bg-emerald-50 border-emerald-300 text-emerald-700';
                            $dot = 'bg-emerald-500';
                        } elseif ($status === 'pending') {
                            $cell = 'bg-amber-50 border-amber-300 text-amber-700';
                            $dot = 'bg-amber-500';
                        } else {
                            $cell = 'bg-[#F9EFE2] border-[#E6D3BC] text-[#A58A6A]';
                            $dot = '';
                        }
                    @endphp

                    <a
                        href="{{ url()->current() . '?range=' . urlencode($timeRange) . '&date=' . $dateStr }}"
                        class="relative aspect-square border rounded-lg p-1 text-xs font-semibold hover:shadow transition
                            {{ $cell }} {{ $isSelected ? 'ring-2 ring-[#4A2C2A]' : '' }}"
                    >
                        <span>{{ $day }}</span>
                        @if($dot)
                            <span class="absolute bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full {{ $dot }}"></span>
                        @endif
                    </a>
                @endfor
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs text-[#8B6B47]">
                <span class="inline-flex items-center gap-1"><i class="w-3 h-3 rounded bg-emerald-100 border border-emerald-300"></i>Completed</span>
                <span class="inline-flex items-center gap-1"><i class="w-3 h-3 rounded bg-amber-100 border border-amber-300"></i>Pending</span>
                <span class="inline-flex items-center gap-1"><i class="w-3 h-3 rounded bg-red-100 border border-red-300"></i>Overdue</span>
                <span class="inline-flex items-center gap-1"><i class="w-3 h-3 rounded bg-[#F5E6D3] border border-[#C9A961]"></i>No Plan</span>
            </div>
        </div>
    </section>

    {{-- Four Core Modules --}}
    <section class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- 1 Favorites --}}
            <article class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h4 class="text-lg font-black text-[#4A2C2A]">Favorites</h4>
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <a href="{{ url('/favorites') }}" class="px-4 py-2 rounded-lg bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] text-sm font-semibold hover:bg-[#6B3D2E] transition">Open Favorites</a>
                        <a href="{{ url('/favorites/plan') }}" class="px-4 py-2 rounded-lg border border-[#C9A961] text-sm font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">Generate Plan from Favorites</a>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Total Favorites</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ $favoritesSummary['total'] }}</div>
                    </div>
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Unstarted</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ $favoritesSummary['unstarted'] }}</div>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse(collect($favoritesSummary['recent']) as $item)
                        <div class="text-sm text-[#6B3D2E] truncate">• {{ $item->title ?? ($item['title'] ?? 'Untitled Article') }}</div>
                    @empty
                        <div class="text-sm text-[#A58A6A]">No favorites yet</div>
                    @endforelse
                </div>

            </article>

            {{-- 2 Browsing History --}}
            <article class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h4 class="text-lg font-black text-[#4A2C2A]">Browsing History</h4>
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <a href="{{ $historySummary['continue_url'] ?? url('/history/continue') }}"
                           class="px-4 py-2 rounded-lg bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] text-sm font-semibold hover:bg-[#6B3D2E] transition">
                            Continue Last Session
                        </a>
                        <a href="{{ url('/history') }}" class="px-4 py-2 rounded-lg border border-[#C9A961] text-sm font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">View Full History</a>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Active Days (7d)</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ $historySummary['active_days_7d'] }}</div>
                    </div>
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Recent Views</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ collect($historySummary['recent'])->count() }}</div>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse(collect($historySummary['recent']) as $item)
                        <div class="text-sm text-[#6B3D2E] truncate">• {{ $item->title ?? ($item['title'] ?? 'Untitled Content') }}</div>
                    @empty
                        <div class="text-sm text-[#A58A6A]">No browsing history yet</div>
                    @endforelse
                </div>

            </article>

            {{-- 3 Notebook --}}
            <article class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h4 class="text-lg font-black text-[#4A2C2A]">Notebook</h4>
                    <div class="flex flex-wrap items-center justify-end gap-2">
                        <a href="{{ url('/notebook/review') }}" class="px-4 py-2 rounded-lg bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] text-sm font-semibold hover:bg-[#6B3D2E] transition">Start Review</a>
                        <a href="{{ url('/notebook') }}" class="px-4 py-2 rounded-lg border border-[#C9A961] text-sm font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">Open Notebook</a>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    @forelse(collect($notebookSummary['recent']) as $item)
                        <div class="text-sm text-[#6B3D2E] truncate">• {{ $item->text ?? ($item['text'] ?? 'Untitled Note') }}</div>
                    @empty
                        <div class="text-sm text-[#A58A6A]">No notes yet</div>
                    @endforelse
                </div>

            </article>

            {{-- 4 Study Status Analysis --}}
            <article class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h4 class="text-lg font-black text-[#4A2C2A]">Study Status Analysis</h4>
                    <a href="{{ url('/analysis?range=' . $timeRange) }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] text-sm font-semibold hover:bg-[#6B3D2E] transition">
                        View Full Analysis Report
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Study Time</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ $analysis['study_minutes'] }}m</div>
                    </div>
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Streak</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ $analysis['streak_days'] }}d</div>
                    </div>
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Listening Accuracy</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ number_format((float)$analysis['listening_accuracy'], 1) }}%</div>
                    </div>
                    <div class="rounded-lg bg-[#F9EFE2] p-3">
                        <div class="text-xs text-[#A58A6A]">Reading Accuracy</div>
                        <div class="text-xl font-black text-[#4A2C2A] mt-1">{{ number_format((float)$analysis['reading_accuracy'], 1) }}%</div>
                    </div>
                </div>

                <div class="mt-4 rounded-lg bg-[#F5E6D3] border border-[#C9A961] p-3">
                    @php
                        $focusArea = (float)$analysis['listening_accuracy'] <= (float)$analysis['reading_accuracy'] ? 'Listening' : 'Reading';
                        $focusAccuracy = $focusArea === 'Listening'
                            ? number_format((float)$analysis['listening_accuracy'], 1)
                            : number_format((float)$analysis['reading_accuracy'], 1);
                        $rhythmTip = (int)$analysis['streak_days'] >= 3
                            ? 'Keep your streak and add one focused practice set today.'
                            : 'Build momentum with one short focused session today.';
                    @endphp

                    <div class="text-sm font-semibold text-[#6B3D2E]">Smart Recommendations</div>
                    <ul class="mt-2 space-y-1.5 text-xs text-[#6B3D2E]">
                        <li>• Priority: strengthen {{ $focusArea }} (current {{ $focusAccuracy }}%).</li>
                        <li>• {{ $rhythmTip }}</li>
                    </ul>
                </div>


            </article>
        </div>
    </section>
</div>
@endsection
