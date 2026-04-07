
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
    <section class="overflow-hidden rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE]/90 shadow-sm">
        <div class="grid lg:grid-cols-[minmax(0,1.2fr)_430px] gap-0">
            <div class="px-6 py-8 sm:px-8 sm:py-10 lg:px-10 lg:py-12">
                <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                    EAPlus Learning Hub
                </div>

                <h1 class="mt-5 text-4xl sm:text-5xl font-black tracking-tight text-[#4A2C2A] leading-[1.05]">
                    Learn English with a calmer pace and a clearer focus.
                </h1>

                <p class="mt-4 max-w-2xl text-[#8B6B47] text-base sm:text-lg leading-8">
                    Act first, then reflect. Start one task today, keep your favorites close, and return to the articles and excerpts that matter most.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <div class="rounded-2xl border border-[#E2CFB3] bg-white/80 px-4 py-3">
                        <div class="text-xs uppercase tracking-[0.14em] text-[#A58A6A]">Saved Articles</div>
                        <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $favoritesSummary['total'] }}</div>
                    </div>
                    <div class="rounded-2xl border border-[#E2CFB3] bg-white/80 px-4 py-3">
                        <div class="text-xs uppercase tracking-[0.14em] text-[#A58A6A]">Notebook Entries</div>
                        <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $notebookSummary['review_pending'] }}</div>
                    </div>
                    <div class="rounded-2xl border border-[#E2CFB3] bg-white/80 px-4 py-3">
                        <div class="text-xs uppercase tracking-[0.14em] text-[#A58A6A]">Recent Activity</div>
                        <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $historySummary['active_days_7d'] }}d</div>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[320px] lg:min-h-full bg-[radial-gradient(circle_at_top,_rgba(255,245,230,0.95),_rgba(233,198,154,0.65)_32%,_rgba(136,88,66,0.88)_72%,_rgba(74,44,42,1)_100%)]">
                <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(255,255,255,0.16),transparent_45%,rgba(74,44,42,0.18))]"></div>

                <div class="absolute right-6 top-6 w-[72%] max-w-[290px] rounded-[2rem] border border-white/30 bg-white/12 p-4 backdrop-blur-sm shadow-[0_20px_45px_rgba(44,24,16,0.25)]">
                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/80 mb-3">Sunset Focus</div>
                    <div class="overflow-hidden rounded-[1.5rem] bg-[linear-gradient(180deg,#ffd8a8_0%,#ffb67a_28%,#cf7d57_52%,#6b3d2e_80%,#3a2221_100%)]">
                        <svg viewBox="0 0 320 220" class="w-full h-auto" role="img" aria-label="Sunset over hills">
                            <defs>
                                <linearGradient id="sun-glow" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#fff1c9" stop-opacity="0.95"/>
                                    <stop offset="100%" stop-color="#ffd27d" stop-opacity="0.15"/>
                                </linearGradient>
                            </defs>
                            <circle cx="220" cy="72" r="40" fill="url(#sun-glow)"/>
                            <circle cx="220" cy="72" r="28" fill="#ffe7a3"/>
                            <path d="M0 168 C46 150 88 154 128 171 C172 189 220 190 320 152 L320 220 L0 220 Z" fill="#764232"/>
                            <path d="M0 184 C64 160 126 170 172 188 C220 206 260 202 320 176 L320 220 L0 220 Z" fill="#4a2c2a"/>
                            <path d="M18 148 C44 132 64 130 90 142" stroke="#fff0c7" stroke-opacity="0.75" stroke-width="3" stroke-linecap="round" fill="none"/>
                            <path d="M70 118 C102 98 126 94 154 108" stroke="#fff0c7" stroke-opacity="0.48" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                        </svg>
                    </div>
                </div>

                <div class="absolute left-6 bottom-6 w-[68%] max-w-[250px] rounded-[2rem] border border-white/20 bg-[#fdf7ee]/90 p-4 shadow-[0_16px_40px_rgba(44,24,16,0.2)]">
                    <div class="text-[11px] font-bold uppercase tracking-[0.2em] text-[#8B6B47] mb-3">Book Stack</div>
                    <div class="overflow-hidden rounded-[1.5rem] bg-[linear-gradient(180deg,#fffaf3_0%,#f5e5d0_100%)]">
                        <svg viewBox="0 0 280 210" class="w-full h-auto" role="img" aria-label="Stack of books and notes">
                            <rect width="280" height="210" fill="#fffaf3"/>
                            <rect x="38" y="150" width="170" height="18" rx="8" fill="#6b3d2e"/>
                            <rect x="45" y="132" width="170" height="16" rx="8" fill="#d8b777"/>
                            <rect x="55" y="114" width="170" height="16" rx="8" fill="#8a654e"/>
                            <rect x="66" y="96" width="170" height="16" rx="8" fill="#c9a961"/>
                            <rect x="176" y="54" width="52" height="34" rx="8" fill="#fff" stroke="#d7c2a8" stroke-width="2"/>
                            <path d="M186 64 H218" stroke="#a58a6a" stroke-width="3" stroke-linecap="round"/>
                            <path d="M186 72 H212" stroke="#c0a27c" stroke-width="3" stroke-linecap="round"/>
                            <path d="M186 80 H220" stroke="#d3b18a" stroke-width="3" stroke-linecap="round"/>
                            <path d="M88 84 C106 60 130 48 150 48 C176 48 194 64 200 92" fill="none" stroke="#4a2c2a" stroke-width="6" stroke-linecap="round"/>
                            <path d="M92 86 C116 74 136 72 158 78" fill="none" stroke="#f3e7d8" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
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
                        <a href="{{ route('favorites.index') }}" class="px-4 py-2 rounded-lg bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] text-sm font-semibold hover:bg-[#6B3D2E] transition">Open Favorites</a>
                        <a href="{{ route('favorites.plan') }}" class="px-4 py-2 rounded-lg border border-[#C9A961] text-sm font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">Generate Plan from Favorites</a>
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
                        <div class="text-sm text-[#6B3D2E] truncate">- {{ $item->title ?? ($item['title'] ?? 'Untitled Article') }}</div>
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
                        <div class="text-sm text-[#6B3D2E] truncate">- {{ $item->title ?? ($item['title'] ?? 'Untitled Content') }}</div>
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
                        <div class="rounded-lg bg-[#F9EFE2] px-3 py-2">
                            <div class="text-sm text-[#6B3D2E] truncate">{{ $item->text ?? ($item['text'] ?? 'Untitled Note') }}</div>
                            <div class="mt-1 text-xs text-[#A58A6A] truncate">{{ $item->article_title ?? ($item['article_title'] ?? 'Untitled Article') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-[#A58A6A]">No notes yet</div>
                    @endforelse
                </div>

            </article>

            {{-- 4 Study Status Analysis --}}
            <article class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h4 class="text-lg font-black text-[#4A2C2A]">Study Status Analysis</h4>
                    <a href="{{ route('study.analysis', ['range' => $timeRange]) }}"
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
                        <li>- Priority: strengthen {{ $focusArea }} (current {{ $focusAccuracy }}%).</li>
                        <li>- {{ $rhythmTip }}</li>
                    </ul>
                </div>


            </article>
        </div>
    </section>

</div>
@endsection
