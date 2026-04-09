@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $selectedDateValue = $selectedDate instanceof \Carbon\CarbonInterface
        ? $selectedDate->copy()
        : \Carbon\Carbon::parse($selectedDate ?? now());
    $currentMonthValue = $currentMonth instanceof \Carbon\CarbonInterface
        ? $currentMonth->copy()
        : \Carbon\Carbon::parse(($currentMonth ?? now()->format('Y-m')).'-01');
    $todayValue = $today instanceof \Carbon\CarbonInterface
        ? $today->copy()
        : \Carbon\Carbon::parse($today ?? now());

    $calendarStart = $currentMonthValue->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::MONDAY);
    $calendarEnd = $currentMonthValue->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::MONDAY);
    $calendarDays = \Carbon\CarbonPeriod::create($calendarStart, $calendarEnd);

    $prevMonth = $currentMonthValue->copy()->subMonthNoOverflow();
    $nextMonth = $currentMonthValue->copy()->addMonthNoOverflow();
    $summaryIcon = function (string $key): string {
        return match ($key) {
            'week' => '<svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><rect x="9" y="11" width="30" height="26" rx="8" fill="#FFF8F0" stroke="#6B3D2E" stroke-width="3"/><path d="M16 9V16" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M32 9V16" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M16 23H24" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/><path d="M16 29H31" stroke="#C9A961" stroke-width="3" stroke-linecap="round"/></svg>',
            'month' => '<svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><rect x="8" y="10" width="32" height="28" rx="8" fill="#FFFDF8" stroke="#8B6B47" stroke-width="3"/><path d="M16 18H32" stroke="#8B6B47" stroke-width="3" stroke-linecap="round"/><path d="M16 25H26" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/><circle cx="31" cy="28" r="5" fill="#FFF3CF" stroke="#C9A961" stroke-width="2.5"/></svg>',
            'today' => '<svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="14" fill="#FFF3CF" stroke="#C9A961" stroke-width="3"/><path d="M24 17V24L29 28" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            default => '<svg class="h-9 w-9" viewBox="0 0 48 48" fill="none"><path d="M24 10L38 34H10L24 10Z" fill="#FBE4DB" stroke="#C95F43" stroke-width="3" stroke-linejoin="round"/><path d="M24 19V25" stroke="#C95F43" stroke-width="3" stroke-linecap="round"/><circle cx="24" cy="31" r="2" fill="#C95F43"/></svg>',
        };
    };
    $toolIcon = function (string $key): string {
        return match ($key) {
            'favorites' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><path d="M24 38L11 25C7.5 21.5 7.5 15.8 11 12.4C14.4 9 20 9 23.4 12.4L24 13L24.6 12.4C28 9 33.6 9 37 12.4C40.5 15.8 40.5 21.5 37 25L24 38Z" fill="#FBE4DB" stroke="#C95F43" stroke-width="3" stroke-linejoin="round"/></svg>',
            'history' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><circle cx="24" cy="24" r="14" fill="#FFF3CF" stroke="#C9A961" stroke-width="3"/><path d="M24 17V24L29 27" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 24H8" stroke="#8B6B47" stroke-width="3" stroke-linecap="round"/></svg>',
            'notebook' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><rect x="11" y="8" width="26" height="32" rx="7" fill="#FFF8F0" stroke="#6B3D2E" stroke-width="3"/><path d="M18 17H30" stroke="#6B3D2E" stroke-width="3" stroke-linecap="round"/><path d="M18 23H30" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/><path d="M18 29H25" stroke="#C9A961" stroke-width="3" stroke-linecap="round"/></svg>',
            'analysis' => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><path d="M12 35V18" stroke="#6B3D2E" stroke-width="4" stroke-linecap="round"/><path d="M24 35V12" stroke="#D88C5A" stroke-width="4" stroke-linecap="round"/><path d="M36 35V22" stroke="#C9A961" stroke-width="4" stroke-linecap="round"/><path d="M10 35H38" stroke="#8B6B47" stroke-width="3" stroke-linecap="round"/></svg>',
            default => '<svg class="h-10 w-10" viewBox="0 0 48 48" fill="none"><rect x="10" y="10" width="28" height="28" rx="10" fill="#FFFDF8" stroke="#8B6B47" stroke-width="3"/><path d="M16 24H32" stroke="#8B6B47" stroke-width="3" stroke-linecap="round"/><path d="M24 16V32" stroke="#D88C5A" stroke-width="3" stroke-linecap="round"/></svg>',
        };
    };
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    @if(session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <section class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 sm:p-8 shadow-sm">
        <div class="grid gap-6 lg:grid-cols-1 lg:items-end">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                    Dashboard
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-[#4A2C2A]">
                    Plan the week, finish today, and keep the next task obvious.
                </h1>
                <div class="mt-5 flex flex-wrap gap-3 text-sm text-[#8B6B47]">
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#FBF6EF] px-3 py-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FBE4DB]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 12H19" stroke="#C95F43" stroke-width="2.2" stroke-linecap="round"/><path d="M12 5V19" stroke="#C95F43" stroke-width="2.2" stroke-linecap="round"/></svg>
                        </span>
                        Quick planning
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#FBF6EF] px-3 py-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#FFF3CF]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 8H20" stroke="#C9A961" stroke-width="2.2" stroke-linecap="round"/><path d="M7 12H17" stroke="#C9A961" stroke-width="2.2" stroke-linecap="round"/><path d="M10 16H14" stroke="#C9A961" stroke-width="2.2" stroke-linecap="round"/></svg>
                        </span>
                        Calendar-led view
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full bg-[#FBF6EF] px-3 py-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[#E8D6C7]">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M6 12L10 16L18 8" stroke="#6B3D2E" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        One clear next step
                    </span>
                </div>

            </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-[1rem] bg-[#FBF6EF]">{!! $summaryIcon('week') !!}</div>
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">This Week</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $weeklySummary['completion_rate'] }}%</div>
                <div class="mt-1 text-sm text-[#8B6B47]">{{ $weeklySummary['completed'] }} of {{ $weeklySummary['total'] }} tasks completed</div>
            </article>

            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-[1rem] bg-[#FBF6EF]">{!! $summaryIcon('month') !!}</div>
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">This Month</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $monthlySummary['completion_rate'] }}%</div>
                <div class="mt-1 text-sm text-[#8B6B47]">{{ $monthlySummary['completed'] }} of {{ $monthlySummary['total'] }} tasks completed</div>
            </article>

            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-[1rem] bg-[#FBF6EF]">{!! $summaryIcon('today') !!}</div>
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">Today</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $todaySummary['pending'] }}</div>
                <div class="mt-1 text-sm text-[#8B6B47]">{{ $todaySummary['completed'] }} done, {{ $todaySummary['skipped'] }} skipped</div>
            </article>

            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-[1rem] bg-[#FBF6EF]">{!! $summaryIcon('overdue') !!}</div>
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">Overdue</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $overdueTasks->count() }}</div>
                <div class="mt-1 text-sm text-[#8B6B47]">Pending tasks from earlier dates</div>
            </article>
        </div>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <article class="xl:col-span-8 rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-2xl font-black text-[#4A2C2A]">Plan Calendar</h2>
                    <p class="mt-1 text-sm text-[#8B6B47]">Use the calendar to track completion and switch the task list by day.</p>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home', ['month' => $prevMonth->format('Y-m'), 'date' => $prevMonth->copy()->startOfMonth()->toDateString()]) }}"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#D8C3A6] text-[#6B3D2E] hover:bg-white transition"
                       aria-label="Previous month">
                        &larr;
                    </a>
                    <div class="min-w-[9rem] rounded-xl bg-white px-4 py-2 text-center text-sm font-semibold text-[#4A2C2A] border border-[#E8D9C7]">
                        {{ $currentMonthValue->format('F Y') }}
                    </div>
                    <a href="{{ route('home', ['month' => $nextMonth->format('Y-m'), 'date' => $nextMonth->copy()->startOfMonth()->toDateString()]) }}"
                       class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[#D8C3A6] text-[#6B3D2E] hover:bg-white transition"
                       aria-label="Next month">
                        &rarr;
                    </a>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">Weekly Progress</div>
                            <div class="mt-1 text-lg font-black text-[#4A2C2A]">{{ $weeklySummary['label'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-[#4A2C2A]">{{ $weeklySummary['completion_rate'] }}%</div>
                            <div class="text-xs text-[#8B6B47]">{{ $weeklySummary['completed'] }}/{{ $weeklySummary['total'] }}</div>
                        </div>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-[#EFE2D3]">
                        <div class="h-2 rounded-full bg-[#4A2C2A]" style="width: {{ $weeklySummary['completion_rate'] }}%"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">Monthly Progress</div>
                            <div class="mt-1 text-lg font-black text-[#4A2C2A]">{{ $monthlySummary['label'] }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-black text-[#4A2C2A]">{{ $monthlySummary['completion_rate'] }}%</div>
                            <div class="text-xs text-[#8B6B47]">{{ $monthlySummary['completed'] }}/{{ $monthlySummary['total'] }}</div>
                        </div>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-[#EFE2D3]">
                        <div class="h-2 rounded-full bg-[#C9A961]" style="width: {{ $monthlySummary['completion_rate'] }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-7 gap-2 text-center text-xs font-bold uppercase tracking-[0.12em] text-[#A58A6A]">
                @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayLabel)
                    <div class="py-2">{{ $dayLabel }}</div>
                @endforeach
            </div>

            <div class="mt-2 grid grid-cols-7 gap-2">
                @foreach($calendarDays as $calendarDay)
                    @php
                        $dateString = $calendarDay->toDateString();
                        $dayMeta = $calendarPlans->get($dateString, [
                            'total' => 0,
                            'completed' => 0,
                            'pending' => 0,
                            'skipped' => 0,
                            'completion_rate' => 0,
                            'has_overdue' => false,
                        ]);
                        $isCurrentMonth = $calendarDay->month === $currentMonthValue->month;
                        $isSelected = $selectedDateValue->isSameDay($calendarDay);
                        $isToday = $todayValue->isSameDay($calendarDay);

                        if (! $isCurrentMonth) {
                            $tone = 'border-[#EEE4D8] bg-[#FBF7F1] text-[#C4B19A]';
                        } elseif ($dayMeta['has_overdue']) {
                            $tone = 'border-red-200 bg-red-50 text-red-700';
                        } elseif ($dayMeta['total'] > 0 && $dayMeta['completed'] === $dayMeta['total']) {
                            $tone = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                        } elseif ($dayMeta['completed'] > 0) {
                            $tone = 'border-amber-200 bg-amber-50 text-amber-700';
                        } elseif ($dayMeta['total'] > 0) {
                            $tone = 'border-[#D8C3A6] bg-[#F6EBDD] text-[#6B3D2E]';
                        } else {
                            $tone = 'border-[#E8D9C7] bg-white text-[#8B6B47]';
                        }
                    @endphp

                    <a href="{{ route('home', ['month' => $currentMonthValue->format('Y-m'), 'date' => $dateString]) }}"
                       class="min-h-[96px] rounded-2xl border p-2 text-left transition hover:shadow-sm {{ $tone }} {{ $isSelected ? 'ring-2 ring-[#4A2C2A]' : '' }}">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-sm font-black {{ $isCurrentMonth ? '' : 'opacity-70' }}">{{ $calendarDay->day }}</span>
                            @if($isToday)
                                <span class="rounded-full bg-[#4A2C2A] px-2 py-0.5 text-[10px] font-bold uppercase tracking-[0.12em] text-[#F5E6D3]">
                                    Today
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 space-y-1">
                            @if($dayMeta['total'] > 0)
                                <div class="text-xs font-semibold">{{ $dayMeta['completed'] }}/{{ $dayMeta['total'] }} done</div>
                                @if($dayMeta['completed'] === $dayMeta['total'])
                                    <div class="text-[11px] opacity-80">All done</div>
                                @elseif($dayMeta['pending'] > 0 && $dayMeta['completed'] > 0)
                                    <div class="text-[11px] opacity-80">Partially done, {{ $dayMeta['pending'] }} pending</div>
                                @elseif($dayMeta['pending'] > 0)
                                    <div class="text-[11px] opacity-80">{{ $dayMeta['pending'] }} pending</div>
                                @elseif($dayMeta['skipped'] === $dayMeta['total'])
                                    <div class="text-[11px] opacity-80">Skipped</div>
                                @else
                                    <div class="text-[11px] opacity-80">No pending tasks</div>
                                @endif
                            @else
                                <div class="text-xs opacity-75">No plan</div>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-5 flex flex-wrap gap-4 text-xs font-medium text-[#8B6B47]">
                <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-emerald-100 border border-emerald-200"></i>All done</span>
                <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-amber-100 border border-amber-200"></i>Partially done</span>
                <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-[#F6EBDD] border border-[#D8C3A6]"></i>Planned</span>
                <span class="inline-flex items-center gap-2"><i class="h-3 w-3 rounded bg-red-100 border border-red-200"></i>Overdue</span>
            </div>
        </article>

        <div class="xl:col-span-4 space-y-6">
            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-black text-[#4A2C2A]">
                            {{ $selectedDateValue->isSameDay($todayValue) ? "Today's Tasks" : $selectedDateValue->format('M j').' Tasks' }}
                        </h2>
                        <p class="mt-1 text-sm text-[#8B6B47]">
                            {{ $selectedTasks->count() }} tasks scheduled for {{ $selectedDateValue->format('F j, Y') }}.
                        </p>
                    </div>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-[#6B3D2E]">
                        {{ $selectedTasks->where('status', 'completed')->count() }}/{{ $selectedTasks->count() ?: 0 }} done
                    </span>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($selectedTasks as $task)
                        @php
                            $badgeClasses = match ($task->status) {
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'skipped' => 'bg-slate-200 text-slate-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp

                        <div class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold text-[#4A2C2A]">
                                        {{ $task->displayTitle() }}
                                    </div>
                                    <div class="mt-1 text-xs text-[#8B6B47]">
                                        @if($task->plan_kind === 'skill' && $task->skill_type)
                                            {{ ucfirst($task->skill_type) }} training target
                                            @if($task->target_count)
                                                璺?{{ $task->target_count }} sets
                                            @endif
                                        @elseif($task->plan_kind === 'custom')
                                            Custom study task
                                        @else
                                            Article study task
                                        @endif
                                        璺?{{ $task->plan_date?->format('M j, Y') }}
                                    </div>
                                </div>
                                <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.12em] {{ $badgeClasses }}">
                                    {{ $task->status }}
                                </span>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($task->plan_kind === 'article' && $task->article)
                                    <a href="{{ route('articles.show', $task->article->id) }}"
                                       class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                                        Open Article
                                    </a>
                                @elseif($task->plan_kind === 'skill' && $task->skill_type)
                                    <a href="{{ route('articles.index', ['skill' => $task->skill_type]) }}"
                                       class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                                        Open {{ ucfirst($task->skill_type) }}
                                    </a>
                                @endif

                                @if($task->status !== 'completed')
                                    <button type="button"
                                            data-url="{{ route('plans.update', $task) }}"
                                            data-status="completed"
                                            class="plan-status-button inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                                        Mark Complete
                                    </button>
                                @else
                                    <button type="button"
                                            data-url="{{ route('plans.update', $task) }}"
                                            data-status="pending"
                                            class="plan-status-button inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                                        Reopen
                                    </button>
                                @endif

                                @if($task->status !== 'skipped')
                                    <button type="button"
                                            data-url="{{ route('plans.update', $task) }}"
                                            data-status="skipped"
                                            class="plan-status-button inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                                        Skip
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-[#D8C3A6] bg-white/70 px-4 py-6 text-sm text-[#8B6B47]">
                            No tasks planned for this date yet. Use the form below to add one.
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-black text-[#4A2C2A]">Quick Add Plan</h2>
                    </div>
                    <a href="{{ route('favorites.plan') }}"
                       class="text-xs font-semibold text-[#6B3D2E] hover:text-[#4A2C2A]">
                        Favorites
                    </a>
                </div>

                <form method="POST" action="{{ route('plans.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="dashboard-plan-date" class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[#8B6B47]">
                            Plan Date
                        </label>
                        <input id="dashboard-plan-date"
                               type="date"
                               name="plan_date"
                               value="{{ old('plan_date', $selectedDateValue->toDateString()) }}"
                               class="w-full rounded-xl border border-[#D8C3A6] bg-white px-4 py-3 text-sm text-[#4A2C2A] focus:border-[#A58A6A] focus:outline-none focus:ring-2 focus:ring-[#E8D9C7]">
                    </div>

                    <div>
                        <label for="dashboard-plan-kind" class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[#8B6B47]">
                            Plan Type
                        </label>
                        <select id="dashboard-plan-kind"
                                name="plan_kind"
                                class="w-full rounded-xl border border-[#D8C3A6] bg-white px-4 py-3 text-sm text-[#4A2C2A] focus:border-[#A58A6A] focus:outline-none focus:ring-2 focus:ring-[#E8D9C7]">
                            <option value="skill" @selected(old('plan_kind', 'skill') === 'skill')>Skill training target</option>
                            <option value="custom" @selected(old('plan_kind') === 'custom')>Custom task</option>
                            <option value="article" @selected(old('plan_kind') === 'article')>Specific article</option>
                        </select>
                    </div>

                    <div data-plan-fields="skill" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="dashboard-skill-type" class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[#8B6B47]">
                                    Skill
                                </label>
                                <select id="dashboard-skill-type"
                                        name="skill_type"
                                        class="w-full rounded-xl border border-[#D8C3A6] bg-white px-4 py-3 text-sm text-[#4A2C2A] focus:border-[#A58A6A] focus:outline-none focus:ring-2 focus:ring-[#E8D9C7]">
                                    <option value="listening" @selected(old('skill_type', 'listening') === 'listening')>Listening</option>
                                    <option value="speaking" @selected(old('skill_type') === 'speaking')>Speaking</option>
                                </select>
                            </div>

                            <div>
                                <label for="dashboard-target-count" class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[#8B6B47]">
                                    Target Count
                                </label>
                                <select id="dashboard-target-count"
                                        name="target_count"
                                        class="w-full rounded-xl border border-[#D8C3A6] bg-white px-4 py-3 text-sm text-[#4A2C2A] focus:border-[#A58A6A] focus:outline-none focus:ring-2 focus:ring-[#E8D9C7]">
                                    @foreach([1, 2, 3, 4, 5] as $count)
                                        <option value="{{ $count }}" @selected((int) old('target_count', 3) === $count)>{{ $count }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('skill_type')
                            <div class="text-sm text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div data-plan-fields="custom" class="space-y-2">
                        <label for="dashboard-custom-title" class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[#8B6B47]">
                            Custom Task
                        </label>
                        <input id="dashboard-custom-title"
                               type="text"
                               name="custom_title"
                               value="{{ old('custom_title') }}"
                               placeholder="Example: Review speaking mistakes and record one new answer"
                               class="w-full rounded-xl border border-[#D8C3A6] bg-white px-4 py-3 text-sm text-[#4A2C2A] focus:border-[#A58A6A] focus:outline-none focus:ring-2 focus:ring-[#E8D9C7]">
                        @error('custom_title')
                            <div class="text-sm text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <div data-plan-fields="article" class="space-y-2">
                        <label for="dashboard-article-id" class="mb-2 block text-xs font-semibold uppercase tracking-[0.12em] text-[#8B6B47]">
                            Article
                        </label>
                        <select id="dashboard-article-id"
                                name="article_id"
                                class="w-full rounded-xl border border-[#D8C3A6] bg-white px-4 py-3 text-sm text-[#4A2C2A] focus:border-[#A58A6A] focus:outline-none focus:ring-2 focus:ring-[#E8D9C7]">
                            @if($favoritePlanArticles->count() > 0)
                                <optgroup label="Favorites">
                                    @foreach($favoritePlanArticles as $article)
                                        <option value="{{ $article->id }}">{{ $article->title }}</option>
                                    @endforeach
                                </optgroup>
                            @endif

                            <optgroup label="All Articles">
                                @foreach($otherArticles as $article)
                                    <option value="{{ $article->id }}">{{ $article->title }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('article_id')
                            <div class="text-sm text-red-700">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-[#4A2C2A] px-4 py-3 text-sm font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        Add Study Plan
                    </button>
                </form>

                @if($errors->any())
                    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif
            </article>

            @if($overdueTasks->count() > 0)
                <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 sm:p-6 shadow-sm">
                    <h2 class="text-xl font-black text-[#4A2C2A]">Overdue Tasks</h2>

                    <div class="mt-4 space-y-3">
                        @foreach($overdueTasks as $task)
                            <div class="rounded-2xl border border-[#E8D9C7] bg-white/80 px-4 py-3">
                                <div class="text-sm font-semibold text-[#4A2C2A]">{{ $task->displayTitle() }}</div>
                                <div class="mt-1 text-xs text-[#8B6B47]">
                                    {{ $task->plan_kind === 'skill' ? ucfirst((string) $task->skill_type).' training' : ($task->plan_kind === 'custom' ? 'Custom task' : 'Article task') }}
                                    璺?{{ $task->plan_date?->format('M j, Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endif
        </div>
    </section>

    <section class="space-y-4">
        <div class="grid gap-4 lg:grid-cols-1 lg:items-end">
            <div>
                <h2 class="text-2xl font-black text-[#4A2C2A]">Support Tools</h2>
            </div>

        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
            <article class="flex h-full flex-col rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-[#FBF6EF]">{!! $toolIcon('favorites') !!}</div>
                    <h3 class="text-lg font-black text-[#4A2C2A]">Favorites</h3>
                </div>

                <div class="mt-5 min-h-[8.5rem] space-y-2">
                    @forelse($favoritesSummary['recent'] as $article)
                        <div class="rounded-xl bg-white/80 px-3 py-2 text-sm text-[#6B3D2E] truncate">
                            {{ $article->title }}
                        </div>
                    @empty
                        <div class="flex min-h-[8.5rem] items-center rounded-xl border border-dashed border-[#D8C3A6] bg-white/60 px-3 py-4 text-sm text-[#8B6B47]">
                            No favorites yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-auto pt-4 flex flex-wrap gap-2">
                    <a href="{{ route('favorites.index') }}"
                       class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        Open Favorites
                    </a>
                    <a href="{{ route('favorites.plan') }}"
                       class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                        Generate Plans
                    </a>
                </div>
            </article>

            <article class="flex h-full flex-col rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-[#FBF6EF]">{!! $toolIcon('history') !!}</div>
                    <h3 class="text-lg font-black text-[#4A2C2A]">Reading History</h3>
                </div>

                <div class="mt-5 min-h-[8.5rem] space-y-2">
                    @forelse($historySummary['recent'] as $item)
                        <div class="rounded-xl bg-white/80 px-3 py-3">
                            <div class="text-sm font-semibold text-[#4A2C2A] truncate">{{ $item['title'] }}</div>
                            <div class="mt-1 text-xs text-[#8B6B47]">{{ $item['last_viewed_at'] ?? 'Recently viewed' }}</div>
                        </div>
                    @empty
                        <div class="flex min-h-[8.5rem] items-center rounded-xl border border-dashed border-[#D8C3A6] bg-white/60 px-3 py-4 text-sm text-[#8B6B47]">
                            No reading history yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-auto pt-4 flex flex-wrap gap-2">
                    <a href="{{ $historySummary['continue_url'] }}"
                       class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        Continue Reading
                    </a>
                    <a href="{{ route('history.index') }}"
                       class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                        Full History
                    </a>
                </div>
            </article>

            <article class="flex h-full flex-col rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-[#FBF6EF]">{!! $toolIcon('notebook') !!}</div>
                    <h3 class="text-lg font-black text-[#4A2C2A]">Notebook</h3>
                </div>

                <div class="mt-5 min-h-[8.5rem] space-y-2">
                    @forelse($notebookSummary['recent'] as $item)
                        <div class="rounded-xl bg-white/80 px-3 py-3">
                            <div class="text-sm text-[#4A2C2A] truncate">{{ $item['text'] }}</div>
                            <div class="mt-1 text-xs text-[#8B6B47] truncate">{{ $item['article_title'] }}</div>
                        </div>
                    @empty
                        <div class="flex min-h-[8.5rem] items-center rounded-xl border border-dashed border-[#D8C3A6] bg-white/60 px-3 py-4 text-sm text-[#8B6B47]">
                            No notebook entries yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-auto pt-4 flex flex-wrap gap-2">
                    <a href="{{ route('notebook.review') }}"
                       class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        Start Review
                    </a>
                    <a href="{{ url('/notebook') }}"
                       class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                        Open Notebook
                    </a>
                </div>
            </article>

            <article class="flex h-full flex-col rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-[#FBF6EF]">{!! $toolIcon('analysis') !!}</div>
                    <h3 class="text-lg font-black text-[#4A2C2A]">Analysis</h3>
                </div>

                <div class="mt-5 min-h-[8.5rem] rounded-[1.5rem] bg-white/80 px-3 py-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div class="rounded-xl bg-[#FFF8F0] px-2.5 py-2.5">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.08em] text-[#A58A6A]">7D Hours</div>
                            <div class="mt-1 text-xl font-black text-[#4A2C2A]">{{ number_format($analysisSummary['study_hours_7d'] ?? 0, 1) }}</div>
                        </div>
                        <div class="rounded-xl bg-[#FFF8F0] px-2.5 py-2.5">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.08em] text-[#A58A6A]">Accuracy</div>
                            <div class="mt-1 text-xl font-black text-[#4A2C2A]">{{ number_format($analysisSummary['accuracy_7d'] ?? 0, 1) }}%</div>
                        </div>
                        <div class="rounded-xl bg-[#FFF8F0] px-2.5 py-2.5">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.08em] text-[#A58A6A]">Listening</div>
                            <div class="mt-1 text-xl font-black text-[#4A2C2A]">{{ $analysisSummary['listening_count_7d'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl bg-[#FFF8F0] px-2.5 py-2.5">
                            <div class="text-[10px] font-semibold uppercase tracking-[0.08em] text-[#A58A6A]">Speaking</div>
                            <div class="mt-1 text-xl font-black text-[#4A2C2A]">{{ $analysisSummary['speaking_count_7d'] ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-[10px] font-semibold uppercase tracking-[0.08em] text-[#A58A6A]">
                            <span>Plan Completion</span>
                            <span>{{ $analysisSummary['plan_completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-1.5 h-2 rounded-full bg-[#EFE2D3]">
                            <div class="h-2 rounded-full bg-[#4A2C2A]" style="width: {{ $analysisSummary['plan_completion_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-4 flex flex-wrap gap-2">
                    <a href="{{ route('study.analysis') }}"
                       class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        Open Analysis
                    </a>
                </div>
            </article>

            <article class="flex h-full flex-col rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="inline-flex h-14 w-14 items-center justify-center rounded-[1.2rem] bg-[#FBF6EF]">{!! $toolIcon('community') !!}</div>
                    <h3 class="text-lg font-black text-[#4A2C2A]">Community</h3>
                </div>

                <div class="mt-5 min-h-[8.5rem] space-y-2 text-sm text-[#6B3D2E]">
                    <div class="rounded-xl bg-white/80 px-3 py-3">My posts: {{ $communitySummary['my_posts_count'] }}</div>
                    <div class="rounded-xl bg-white/80 px-3 py-3">Saved posts: {{ $communitySummary['saved_posts_count'] }}</div>
                </div>

                <div class="mt-auto pt-4 flex flex-wrap gap-2">
                    <a href="{{ route('forum.my') }}"
                       class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        My Forum
                    </a>
                    <a href="{{ route('forum.saved') }}"
                       class="inline-flex items-center rounded-xl border border-[#D8C3A6] px-3 py-2 text-xs font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                        Saved Posts
                    </a>
                </div>
            </article>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const planKindSelect = document.getElementById('dashboard-plan-kind');

    function syncPlanFields() {
        if (!planKindSelect) {
            return;
        }

        const activeKind = planKindSelect.value;

        document.querySelectorAll('[data-plan-fields]').forEach((element) => {
            element.classList.toggle('hidden', element.getAttribute('data-plan-fields') !== activeKind);
        });
    }

    syncPlanFields();
    planKindSelect?.addEventListener('change', syncPlanFields);

    document.querySelectorAll('.plan-status-button').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!csrfToken) {
                return;
            }

            const url = button.dataset.url;
            const status = button.dataset.status;
            const originalText = button.textContent;

            button.disabled = true;
            button.textContent = 'Saving...';

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ status }),
                });

                if (!response.ok) {
                    throw new Error('Unable to update the task right now.');
                }

                window.location.reload();
            } catch (error) {
                window.alert(error.message || 'Unable to update the task right now.');
                button.disabled = false;
                button.textContent = originalText;
            }
        });
    });
})();
</script>
@endpush

