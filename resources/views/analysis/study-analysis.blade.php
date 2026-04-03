@extends('layouts.app')

@section('title', 'Personal Learning Analysis Details')

@section('content')
@php
    $timeRange = request('range', '7d');
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <section class="rounded-2xl border border-[#E6D3BC] bg-[#FDF7EE] shadow-sm p-5 sm:p-6">
        <form method="GET" action="{{ route('study.analysis') }}" class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-semibold text-[#8B6B47]">Time Range</span>

            @foreach([
                '7d' => 'Last 7 Days',
                '30d' => 'Last 30 Days',
                '90d' => 'Last 90 Days',
                '1y' => 'Last 1 Year',
            ] as $value => $label)
                <button
                    type="submit"
                    name="range"
                    value="{{ $value }}"
                    class="px-4 py-2 rounded-lg border text-sm font-semibold transition {{ $timeRange === $value ? 'bg-[#4A2C2A] border-[#4A2C2A] text-[#F5E6D3]' : 'bg-white border-[#D9C6A8] text-[#6B4E3A] hover:border-[#A58A6A]' }}">
                    {{ $label }}
                </button>
            @endforeach
        </form>
    </section>

    <section class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-3xl sm:text-4xl font-black text-[#4A2C2A] tracking-tight">Personal Learning Analysis Details</h1>
            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-emerald-700">
                Learning Status · {{ $overview['learning_status'] ?? 'Unknown' }}
            </span>
        </div>

        <div class="rounded-2xl border border-[#E6D3BC] bg-white shadow-sm p-5 sm:p-6 space-y-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-[#4A2C2A]">Overview</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">Snapshot</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#A58A6A]">Study Time</p>
                    <p class="mt-2 text-3xl font-black text-[#4A2C2A]">
                        {{ number_format($overview['total_study_hours'] ?? 0, 1) }}h
                    </p>
                </article>

                <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#A58A6A]">Active Days</p>
                    <p class="mt-2 text-3xl font-black text-[#4A2C2A]">
                        {{ $overview['study_days'] ?? 0 }}
                    </p>
                </article>

                <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#A58A6A]">Completed Plans</p>
                    <p class="mt-2 text-3xl font-black text-[#4A2C2A]">
                        {{ $overview['completion_rate'] ?? 0 }}%
                    </p>
                </article>

                <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#A58A6A]">Overall Accuracy</p>
                    <p class="mt-2 text-3xl font-black text-[#4A2C2A]">
                        {{ $overview['overall_accuracy'] ?? 0 }}%
                    </p>
                </article>

                <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#A58A6A]">Ability Score</p>
                    <p class="mt-2 text-3xl font-black text-[#4A2C2A]">
                        {{ $overview['ability_score'] ?? 0 }}
                    </p>
                </article>

                <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[#A58A6A]">Focus Period</p>
                    <p class="mt-2 text-3xl font-black text-[#4A2C2A]">
                        {{ $overview['focus_period'] ?? 'N/A' }}
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-[#E6D3BC] bg-white shadow-sm p-5 sm:p-6 space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-2xl font-black text-[#4A2C2A]">Learning Effort Analysis</h2>
            <span class="text-sm font-semibold text-[#8B6B47]">Engagement Overview</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Daily Study Duration Trend</h3>
                <div class="h-52 rounded-lg border border-dashed border-[#D8C3A6] bg-white/70 p-3">
                    <canvas id="daily-duration-chart"></canvas>
                </div>
            </article>

            <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Daily Session Count Trend</h3>
                <div class="h-52 rounded-lg border border-dashed border-[#D8C3A6] bg-white/70 p-3">
                    <canvas id="daily-count-chart"></canvas>
                </div>
            </article>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <article class="xl:col-span-5 rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Study Frequency Distribution</h3>

                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs text-[#8B6B47]">
                            <span>1 Session / Day</span>
                            <span>{{ $effort['frequency_distribution']['one']['percent'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#F0E2D0]">
                            <div class="h-2 rounded-full bg-[#8B6B47]" style="width: {{ $effort['frequency_distribution']['one']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-[#8B6B47]">
                            <span>2 Sessions / Day</span>
                            <span>{{ $effort['frequency_distribution']['two']['percent'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#F0E2D0]">
                            <div class="h-2 rounded-full bg-[#8B6B47]" style="width: {{ $effort['frequency_distribution']['two']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-[#8B6B47]">
                            <span>3+ Sessions / Day</span>
                            <span>{{ $effort['frequency_distribution']['three_plus']['percent'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#F0E2D0]">
                            <div class="h-2 rounded-full bg-[#8B6B47]" style="width: {{ $effort['frequency_distribution']['three_plus']['percent'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="xl:col-span-3 rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Plan Execution Health</h3>

                <div class="rounded-lg bg-white border border-[#EADBC8] p-3">
                    <p class="text-xs text-[#A58A6A]">Plan Execution Rate</p>
                    <p class="mt-1 text-3xl font-black text-[#4A2C2A]">
                        {{ $effort['plan_execution_rate'] ?? 0 }}%
                    </p>
                </div>

                <div class="rounded-lg bg-white border border-[#EADBC8] p-3">
                    <p class="text-xs text-[#A58A6A]">Overdue Plans</p>
                    <p class="mt-1 text-3xl font-black text-[#4A2C2A]">
                        {{ $effort['overdue_plan_count'] ?? 0 }}
                    </p>
                </div>
            </article>

            <article class="xl:col-span-4 rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Learning Effort Insights</h3>
                <div class="mt-3 rounded-lg border border-[#EADBC8] bg-white p-3 text-sm text-[#6B4E3A] leading-7 space-y-1">
                    @forelse($effort['insights'] ?? [] as $insight)
                        <p>• {{ $insight }}</p>
                    @empty
                        <p>• No effort insights yet. Complete a few study sessions to generate analysis.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>

    <section class="rounded-2xl border border-[#E6D3BC] bg-white shadow-sm p-5 sm:p-6 space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-2xl font-black text-[#4A2C2A]">Learning Outcomes Analysis</h2>
            <span class="text-sm font-semibold text-[#8B6B47]">Performance Overview</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Overall Accuracy Trend</h3>
                <div class="h-52 rounded-lg border border-dashed border-[#D8C3A6] bg-white/70 p-3">
                    <canvas id="overall-accuracy-chart"></canvas>
                </div>
            </article>

            <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Listening vs Reading Accuracy Trend</h3>
                <div class="h-52 rounded-lg border border-dashed border-[#D8C3A6] bg-white/70 p-3">
                    <canvas id="listening-reading-chart"></canvas>
                </div>
            </article>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">
            <article class="xl:col-span-4 rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Outcome Metrics</h3>
                <div class="rounded-lg bg-white border border-[#EADBC8] p-3">
                    <p class="text-xs text-[#A58A6A]">Total Completed Exercises</p>
                    <p class="mt-1 text-3xl font-black text-[#4A2C2A]">
                        {{ $outcomes['completed_exercises'] ?? 0 }}
                    </p>
                </div>
                <div class="rounded-lg bg-white border border-[#EADBC8] p-3">
                    <p class="text-xs text-[#A58A6A]">Learning Efficiency Index</p>
                    <p class="mt-1 text-3xl font-black text-[#4A2C2A]">
                        {{ $outcomes['efficiency_index'] ?? 0 }}
                    </p>
                </div>
            </article>

            <article class="xl:col-span-8 rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Learning Outcomes Insights</h3>
                <div class="mt-3 rounded-lg border border-[#EADBC8] bg-white p-4 text-sm text-[#6B4E3A] leading-7 space-y-1">
                    @forelse($outcomes['insights'] ?? [] as $insight)
                        <p>• {{ $insight }}</p>
                    @empty
                        <p>• No outcome insights yet. Complete a few study sessions to generate analysis.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>

    <section class="rounded-2xl border border-[#E6D3BC] bg-white shadow-sm p-5 sm:p-6 space-y-6">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-2xl font-black text-[#4A2C2A]">Capability Diagnosis Analysis</h2>
            <span class="text-sm font-semibold text-[#8B6B47]">Listening & Reading Weakness Mapping</span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Listening Diagnosis</h3>

                <div class="rounded-lg border border-[#EADBC8] bg-white p-3 space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-[#A58A6A]">Error Rate</p>
                    @php
                        $listeningErrorRate = $capabilityDiagnosis['listening']['error_rate'] ?? 0;
                    @endphp
                    <div class="flex items-center justify-between text-xs text-[#6B4E3A]">
                        <span>{{ $listeningErrorRate }}%</span>
                        <span>0% · 100%</span>
                    </div>
                    <div class="h-2 rounded-full bg-[#F0E2D0]">
                        <div class="h-2 rounded-full bg-[#8B6B47]" style="width: {{ min(100, max(0, $listeningErrorRate)) }}%"></div>
                    </div>
                </div>

                <div class="rounded-lg border border-[#EADBC8] bg-white p-3 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-[#A58A6A]">Error Type Distribution</p>
                    <div class="space-y-2 text-xs text-[#6B4E3A]">
                        @forelse($capabilityDiagnosis['listening']['error_type_distribution'] ?? [] as $item)
                            <div>
                                <div class="flex items-center justify-between">
                                    <span>{{ $item['type'] }}</span>
                                    <span>{{ $item['percent'] }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-[#F0E2D0]">
                                    <div class="h-2 rounded-full bg-[#8B6B47]" style="width: {{ $item['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-[#A58A6A]">Not enough listening mistakes yet to analyze error types.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-[#EADBC8] bg-white p-3">
                    <p class="text-xs text-[#A58A6A]">Efficiency Index</p>
                    <p class="mt-1 text-2xl font-black text-[#4A2C2A]">
                        {{ $capabilityDiagnosis['listening']['efficiency_index'] ?? 0 }}
                    </p>
                </div>
            </article>

            <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Reading Diagnosis</h3>

                <div class="rounded-lg border border-[#EADBC8] bg-white p-3 space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-[#A58A6A]">Error Rate</p>
                    @php
                        $readingErrorRate = $capabilityDiagnosis['reading']['error_rate'] ?? 0;
                    @endphp
                    <div class="flex items-center justify-between text-xs text-[#6B4E3A]">
                        <span>{{ $readingErrorRate }}%</span>
                        <span>0% · 100%</span>
                    </div>
                    <div class="h-2 rounded-full bg-[#F0E2D0]">
                        <div class="h-2 rounded-full bg-[#C9A961]" style="width: {{ min(100, max(0, $readingErrorRate)) }}%"></div>
                    </div>
                </div>

                <div class="rounded-lg border border-[#EADBC8] bg-white p-3 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.1em] text-[#A58A6A]">Error Type Distribution</p>
                    <div class="space-y-2 text-xs text-[#6B4E3A]">
                        @forelse($capabilityDiagnosis['reading']['error_type_distribution'] ?? [] as $item)
                            <div>
                                <div class="flex items-center justify-between">
                                    <span>{{ $item['type'] }}</span>
                                    <span>{{ $item['percent'] }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-[#F0E2D0]">
                                    <div class="h-2 rounded-full bg-[#C9A961]" style="width: {{ $item['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-[#A58A6A]">Not enough reading mistakes yet to analyze error types.</div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-[#EADBC8] bg-white p-3">
                    <p class="text-xs text-[#A58A6A]">Efficiency Index</p>
                    <p class="mt-1 text-2xl font-black text-[#4A2C2A]">
                        {{ $capabilityDiagnosis['reading']['efficiency_index'] ?? 0 }}
                    </p>
                </div>
            </article>
        </div>

        <article class="rounded-xl border border-[#E9DDCC] bg-[#FFF9F2] p-4">
            <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Top 3 Issues & Suggested Actions</h3>
            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                @forelse($capabilityDiagnosis['top_issues'] ?? [] as $index => $issue)
                    <div class="rounded-lg border border-[#EADBC8] bg-white p-3 space-y-2">
                        <p class="font-semibold text-[#4A2C2A]">
                            Issue {{ $index + 1 }} · {{ $issue['module'] }}
                        </p>
                        <p class="text-[#6B4E3A]">{{ $issue['issue'] }}</p>
                        <p class="text-xs text-[#8B6B47]">Suggested Action: {{ $issue['suggested_action'] }}</p>
                    </div>
                @empty
                    <div class="rounded-lg border border-[#EADBC8] bg-white p-3 space-y-2 md:col-span-3">
                        <p class="font-semibold text-[#4A2C2A]">Not enough data to identify key issues yet.</p>
                        <p class="text-xs text-[#8B6B47]">Once you complete more listening and reading practice, we will surface the top 3 issues with concrete actions.</p>
                    </div>
                @endforelse
            </div>
        </article>
    </section>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const effort = @json($effort ?? []);
            const outcomes = @json($outcomes ?? []);

            // Daily study duration (minutes)
            if (effort.daily_study_duration_trend && document.getElementById('daily-duration-chart')) {
                const ctx = document.getElementById('daily-duration-chart').getContext('2d');
                const labels = effort.daily_study_duration_trend.map(item => item.date);
                const data = effort.daily_study_duration_trend.map(item => item.minutes);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Minutes',
                            data,
                            borderColor: '#8B6B47',
                            backgroundColor: 'rgba(139, 107, 71, 0.08)',
                            tension: 0.3,
                            pointRadius: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {legend: {display: false}},
                        scales: {
                            x: {ticks: {autoSkip: true, maxTicksLimit: 7}},
                            y: {beginAtZero: true},
                        },
                    },
                });
            }

            // Daily session count
            if (effort.daily_study_count_trend && document.getElementById('daily-count-chart')) {
                const ctx = document.getElementById('daily-count-chart').getContext('2d');
                const labels = effort.daily_study_count_trend.map(item => item.date);
                const data = effort.daily_study_count_trend.map(item => item.count);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Sessions',
                            data,
                            backgroundColor: '#C9A961',
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {legend: {display: false}},
                        scales: {
                            x: {ticks: {autoSkip: true, maxTicksLimit: 7}},
                            y: {beginAtZero: true, precision: 0},
                        },
                    },
                });
            }

            // Overall accuracy trend
            if (outcomes.overall_accuracy_trend && document.getElementById('overall-accuracy-chart')) {
                const ctx = document.getElementById('overall-accuracy-chart').getContext('2d');
                const labels = outcomes.overall_accuracy_trend.map(item => item.date);
                const data = outcomes.overall_accuracy_trend.map(item => item.accuracy);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Accuracy (%)',
                            data,
                            borderColor: '#4A2C2A',
                            backgroundColor: 'rgba(74, 44, 42, 0.08)',
                            tension: 0.3,
                            pointRadius: 3,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {legend: {display: false}},
                        scales: {
                            x: {ticks: {autoSkip: true, maxTicksLimit: 7}},
                            y: {beginAtZero: true, max: 100},
                        },
                    },
                });
            }

            // Listening vs Reading accuracy trend
            if (outcomes.listening_accuracy_trend && outcomes.reading_accuracy_trend && document.getElementById('listening-reading-chart')) {
                const ctx = document.getElementById('listening-reading-chart').getContext('2d');
                const labels = outcomes.listening_accuracy_trend.map(item => item.date);
                const listeningData = outcomes.listening_accuracy_trend.map(item => item.accuracy);
                const readingData = outcomes.reading_accuracy_trend.map(item => item.accuracy);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Listening',
                                data: listeningData,
                                borderColor: '#8B6B47',
                                backgroundColor: 'rgba(139, 107, 71, 0.08)',
                                tension: 0.3,
                                pointRadius: 3,
                            },
                            {
                                label: 'Reading',
                                data: readingData,
                                borderColor: '#C9A961',
                                backgroundColor: 'rgba(201, 169, 97, 0.08)',
                                tension: 0.3,
                                pointRadius: 3,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {legend: {display: true}},
                        scales: {
                            x: {ticks: {autoSkip: true, maxTicksLimit: 7}},
                            y: {beginAtZero: true, max: 100},
                        },
                    },
                });
            }
        })();
    </script>
@endpush

@endsection
