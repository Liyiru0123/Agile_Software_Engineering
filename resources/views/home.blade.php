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
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    @if(session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <section class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                    Dashboard
                </div>
                <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-[#4A2C2A]">
                    Plan the week, finish today, and keep the next task obvious.
                </h1>
                <p class="mt-3 text-sm sm:text-base leading-7 text-[#8B6B47]">
                    This page now focuses on execution first: weekly and monthly completion, the selected day's tasks, and a quick way to create new plans.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('favorites.plan') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-[#4A2C2A] px-4 py-3 text-sm font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                    Plan From Favorites
                </a>
                <a href="{{ route('study.analysis') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-[#C9A961] px-4 py-3 text-sm font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                    View Analysis
                </a>
                <a href="{{ route('articles.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-[#C9A961] px-4 py-3 text-sm font-semibold text-[#6B3D2E] hover:bg-[#F9EFE2] transition">
                    Browse Articles
                </a>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">This Week</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $weeklySummary['completion_rate'] }}%</div>
                <div class="mt-1 text-sm text-[#8B6B47]">{{ $weeklySummary['completed'] }} of {{ $weeklySummary['total'] }} tasks completed</div>
            </article>

            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">This Month</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $monthlySummary['completion_rate'] }}%</div>
                <div class="mt-1 text-sm text-[#8B6B47]">{{ $monthlySummary['completed'] }} of {{ $monthlySummary['total'] }} tasks completed</div>
            </article>

            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
                <div class="text-xs font-semibold uppercase tracking-[0.14em] text-[#A58A6A]">Today</div>
                <div class="mt-2 text-3xl font-black text-[#4A2C2A]">{{ $todaySummary['pending'] }}</div>
                <div class="mt-1 text-sm text-[#8B6B47]">{{ $todaySummary['completed'] }} done, {{ $todaySummary['skipped'] }} skipped</div>
            </article>

            <article class="rounded-2xl border border-[#E8D9C7] bg-white/80 p-4">
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
                                <div class="text-[11px] opacity-80">{{ $dayMeta['pending'] }} pending</div>
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
                                                · {{ $task->target_count }} sets
                                            @endif
                                        @elseif($task->plan_kind === 'custom')
                                            Custom study task
                                        @else
                                            Article study task
                                        @endif
                                        · {{ $task->plan_date?->format('M j, Y') }}
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
                        <p class="mt-1 text-sm text-[#8B6B47]">Use training targets first, then add article-specific or custom tasks only when needed.</p>
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
                    <p class="mt-1 text-sm text-[#8B6B47]">These still need attention from earlier dates.</p>

                    <div class="mt-4 space-y-3">
                        @foreach($overdueTasks as $task)
                            <div class="rounded-2xl border border-[#E8D9C7] bg-white/80 px-4 py-3">
                                <div class="text-sm font-semibold text-[#4A2C2A]">{{ $task->displayTitle() }}</div>
                                <div class="mt-1 text-xs text-[#8B6B47]">
                                    {{ $task->plan_kind === 'skill' ? ucfirst((string) $task->skill_type).' training' : ($task->plan_kind === 'custom' ? 'Custom task' : 'Article task') }}
                                    · {{ $task->plan_date?->format('M j, Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endif
        </div>
    </section>

    <section class="space-y-4">
        <div>
            <h2 class="text-2xl font-black text-[#4A2C2A]">Support Tools</h2>
            <p class="mt-1 text-sm text-[#8B6B47]">Useful resources stay available, but they no longer compete with your daily plan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-black text-[#4A2C2A]">Favorites</h3>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $favoritesSummary['total'] }}</span>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse($favoritesSummary['recent'] as $article)
                        <div class="rounded-xl bg-white/80 px-3 py-2 text-sm text-[#6B3D2E] truncate">
                            {{ $article->title }}
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#D8C3A6] bg-white/60 px-3 py-4 text-sm text-[#8B6B47]">
                            No favorites yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
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

            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-black text-[#4A2C2A]">Reading History</h3>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $historySummary['active_days_7d'] }} active days</span>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse($historySummary['recent'] as $item)
                        <div class="rounded-xl bg-white/80 px-3 py-3">
                            <div class="text-sm font-semibold text-[#4A2C2A] truncate">{{ $item['title'] }}</div>
                            <div class="mt-1 text-xs text-[#8B6B47]">{{ $item['last_viewed_at'] ?? 'Recently viewed' }}</div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#D8C3A6] bg-white/60 px-3 py-4 text-sm text-[#8B6B47]">
                            No reading history yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
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

            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-black text-[#4A2C2A]">Notebook</h3>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $notebookSummary['review_pending'] }} saved</span>
                </div>

                <div class="mt-4 space-y-2">
                    @forelse($notebookSummary['recent'] as $item)
                        <div class="rounded-xl bg-white/80 px-3 py-3">
                            <div class="text-sm text-[#4A2C2A] truncate">{{ $item['text'] }}</div>
                            <div class="mt-1 text-xs text-[#8B6B47] truncate">{{ $item['article_title'] }}</div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-[#D8C3A6] bg-white/60 px-3 py-4 text-sm text-[#8B6B47]">
                            No notebook entries yet.
                        </div>
                    @endforelse
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
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

            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-black text-[#4A2C2A]">Analysis</h3>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">Insights</span>
                </div>

                <div class="mt-4 space-y-2 text-sm leading-7 text-[#6B3D2E]">
                    <p>Review your study effort, outcomes, and weak-skill diagnosis in one place.</p>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('study.analysis') }}"
                       class="inline-flex items-center rounded-xl bg-[#4A2C2A] px-3 py-2 text-xs font-semibold text-[#F5E6D3] hover:bg-[#6B3D2E] transition">
                        Open Analysis
                    </a>
                </div>
            </article>

            <article class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-black text-[#4A2C2A]">Community</h3>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $communitySummary['my_posts_count'] }} posts</span>
                </div>

                <div class="mt-4 space-y-2 text-sm text-[#6B3D2E]">
                    <div class="rounded-xl bg-white/80 px-3 py-3">My posts: {{ $communitySummary['my_posts_count'] }}</div>
                    <div class="rounded-xl bg-white/80 px-3 py-3">Saved posts: {{ $communitySummary['saved_posts_count'] }}</div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
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
