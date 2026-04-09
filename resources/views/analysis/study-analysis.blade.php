@extends('layouts.app')

@section('title', 'Learning Analysis')

@section('content')
@php
    $timeRange = request('range', '7d');
    $listeningCompleted = (int) ($capabilityDiagnosis['listening']['completed_count'] ?? 0);
    $speakingCompleted = (int) ($capabilityDiagnosis['speaking']['completed_count'] ?? 0);
    $listeningErrorRate = (float) ($capabilityDiagnosis['listening']['error_rate'] ?? 0);
    $speakingErrorRate = (float) ($capabilityDiagnosis['speaking']['error_rate'] ?? 0);
    $topIssues = collect($capabilityDiagnosis['top_issues'] ?? [])->take(3)->values();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <section class="rounded-[2rem] border border-[#E6D3BC] bg-[#FDF7EE] p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <span class="inline-flex items-center rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold uppercase tracking-[0.16em] text-[#8B6B47]">
                    {{ strtoupper($timeRange) }} window
                </span>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-[#4A2C2A] sm:text-4xl">Learning Analysis</h1>
                    <p class="mt-2 text-sm text-[#7A5A45]">
                        Visual summary of pace, completion, and weak spots.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('study.analysis') }}" class="flex flex-wrap gap-2">
                @foreach([
                    '7d' => '7D',
                    '30d' => '30D',
                    '90d' => '90D',
                    '1y' => '1Y',
                ] as $value => $label)
                    <button
                        type="submit"
                        name="range"
                        value="{{ $value }}"
                        class="rounded-full border px-4 py-2 text-sm font-semibold transition {{ $timeRange === $value ? 'border-[#4A2C2A] bg-[#4A2C2A] text-[#F5E6D3]' : 'border-[#D9C6A8] bg-white text-[#6B4E3A] hover:border-[#A58A6A]' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </form>
        </div>
    </section>

    <section class="grid grid-cols-2 gap-4 xl:grid-cols-4">
        <article class="rounded-[1.75rem] border border-[#E7D8C5] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Study Time</p>
            <p class="mt-3 text-3xl font-black text-[#4A2C2A]">{{ number_format($overview['total_study_hours'] ?? 0, 1) }}h</p>
        </article>
        <article class="rounded-[1.75rem] border border-[#E7D8C5] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Active Days</p>
            <p class="mt-3 text-3xl font-black text-[#4A2C2A]">{{ $overview['study_days'] ?? 0 }}</p>
        </article>
        <article class="rounded-[1.75rem] border border-[#E7D8C5] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Exercises</p>
            <p class="mt-3 text-3xl font-black text-[#4A2C2A]">{{ $outcomes['completed_exercises'] ?? 0 }}</p>
        </article>
        <article class="rounded-[1.75rem] border border-[#E7D8C5] bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Ability Score</p>
            <p class="mt-3 text-3xl font-black text-[#4A2C2A]">{{ $overview['ability_score'] ?? 0 }}</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-12">
        <article class="xl:col-span-4 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Snapshot</h2>
                <span class="rounded-full bg-[#EEF6F1] px-3 py-1 text-xs font-bold uppercase tracking-[0.12em] text-emerald-700">
                    {{ $overview['learning_status'] ?? 'Unknown' }}
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3 xl:grid-cols-1">
                <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Accuracy</p>
                        <p class="text-sm font-semibold text-[#6B4E3A]">{{ $overview['overall_accuracy'] ?? 0 }}%</p>
                    </div>
                    <div class="mt-3 h-36"><canvas id="accuracy-gauge-chart"></canvas></div>
                </div>

                <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Plan Done</p>
                        <p class="text-sm font-semibold text-[#6B4E3A]">{{ $overview['completion_rate'] ?? 0 }}%</p>
                    </div>
                    <div class="mt-3 h-36"><canvas id="plan-gauge-chart"></canvas></div>
                </div>

                <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-[#A58A6A]">Practice Mix</p>
                        <p class="text-sm font-semibold text-[#6B4E3A]">{{ $listeningCompleted + $speakingCompleted }}</p>
                    </div>
                    <div class="mt-3 h-36"><canvas id="skill-mix-chart"></canvas></div>
                    <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold text-[#6B4E3A]">
                        <span class="rounded-full bg-white px-3 py-1">Listening {{ $listeningCompleted }}</span>
                        <span class="rounded-full bg-white px-3 py-1">Speaking {{ $speakingCompleted }}</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="xl:col-span-8 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Study Pace</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">{{ $overview['focus_period'] ?? 'N/A' }}</span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FFF9F2] p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Minutes</h3>
                        <span class="text-xs font-semibold text-[#A58A6A]">Daily</span>
                    </div>
                    <div class="mt-4 h-64"><canvas id="daily-duration-chart"></canvas></div>
                </div>

                <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FFF9F2] p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-[0.12em] text-[#8B6B47]">Sessions</h3>
                        <span class="text-xs font-semibold text-[#A58A6A]">Daily</span>
                    </div>
                    <div class="mt-4 h-64"><canvas id="daily-count-chart"></canvas></div>
                </div>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-12">
        <article class="xl:col-span-4 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Habit Split</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">Frequency</span>
            </div>
            <div class="mt-4 h-64"><canvas id="frequency-mix-chart"></canvas></div>
            <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs font-semibold text-[#6B4E3A]">
                <div class="rounded-2xl bg-[#FFF8F0] px-3 py-3">1/day<br>{{ $effort['frequency_distribution']['one']['percent'] ?? 0 }}%</div>
                <div class="rounded-2xl bg-[#FFF8F0] px-3 py-3">2/day<br>{{ $effort['frequency_distribution']['two']['percent'] ?? 0 }}%</div>
                <div class="rounded-2xl bg-[#FFF8F0] px-3 py-3">3+/day<br>{{ $effort['frequency_distribution']['three_plus']['percent'] ?? 0 }}%</div>
            </div>
        </article>

        <article class="xl:col-span-4 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Performance</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">Accuracy</span>
            </div>
            <div class="mt-5 space-y-4">
                <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-[#6B4E3A]">Overall</span>
                        <span class="font-black text-[#4A2C2A]">{{ $overview['overall_accuracy'] ?? 0 }}%</span>
                    </div>
                </div>
                <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-[#6B4E3A]">Efficiency</span>
                        <span class="font-black text-[#4A2C2A]">{{ $outcomes['efficiency_index'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-[#6B4E3A]">Overdue Plans</span>
                        <span class="font-black text-[#4A2C2A]">{{ $effort['overdue_plan_count'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="xl:col-span-4 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Top Actions</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">Short list</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse($topIssues as $issue)
                    <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4">
                        <p class="text-xs font-bold uppercase tracking-[0.12em] text-[#A58A6A]">{{ $issue['module'] ?? 'Issue' }}</p>
                        <p class="mt-2 text-sm font-semibold text-[#4A2C2A]">{{ $issue['issue'] ?? 'Keep practicing.' }}</p>
                        <p class="mt-2 text-xs text-[#8B6B47]">{{ $issue['suggested_action'] ?? 'Add one focused review block.' }}</p>
                    </div>
                @empty
                    <div class="rounded-[1.5rem] bg-[#FFF8F0] p-4 text-sm text-[#6B4E3A]">
                        More practice is needed before issues can be ranked.
                    </div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-12">
        <article class="xl:col-span-7 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Accuracy Trend</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">Over time</span>
            </div>
            <div class="mt-4 h-72"><canvas id="overall-accuracy-chart"></canvas></div>
        </article>

        <article class="xl:col-span-5 rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Skill Comparison</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">Listening vs Speaking</span>
            </div>
            <div class="mt-4 h-72"><canvas id="listening-speaking-chart"></canvas></div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-2">
        <article class="rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Listening Weak Spots</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">{{ $listeningErrorRate }}% error</span>
            </div>
            <div class="mt-4 h-72"><canvas id="listening-errors-chart"></canvas></div>
        </article>

        <article class="rounded-[2rem] border border-[#E7D8C5] bg-white p-5 shadow-sm sm:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-black text-[#4A2C2A]">Speaking Weak Spots</h2>
                <span class="text-sm font-semibold text-[#8B6B47]">{{ $speakingErrorRate }}% error</span>
            </div>
            <div class="mt-4 h-72"><canvas id="speaking-errors-chart"></canvas></div>
        </article>
    </section>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const effort = @json($effort ?? []);
            const outcomes = @json($outcomes ?? []);
            const overview = @json($overview ?? []);
            const capabilityDiagnosis = @json($capabilityDiagnosis ?? []);

            const palette = {
                ink: '#4A2C2A',
                cocoa: '#8B6B47',
                gold: '#C9A961',
                peach: '#E7B787',
                cream: '#F0E2D0',
                mint: '#6F9A8D',
                rose: '#D68A7C',
                sand: '#D8C3A6',
            };

            function shortLabels(items) {
                return items.map(item => item.date.slice(5));
            }

            function buildMetricDoughnut(id, value, color) {
                const canvas = document.getElementById(id);

                if (!canvas) {
                    return;
                }

                const safeValue = Math.max(0, Math.min(100, Number(value || 0)));

                new Chart(canvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Value', 'Remaining'],
                        datasets: [{
                            data: [safeValue, Math.max(0, 100 - safeValue)],
                            backgroundColor: [color, '#F0E2D0'],
                            borderWidth: 0,
                            cutout: '72%',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false },
                        },
                    },
                    plugins: [{
                        id: `${id}-label`,
                        afterDraw(chart) {
                            const meta = chart.getDatasetMeta(0).data[0];

                            if (!meta) {
                                return;
                            }

                            const {ctx} = chart;
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillStyle = palette.ink;
                            ctx.font = '900 28px sans-serif';
                            ctx.fillText(`${safeValue}%`, meta.x, meta.y - 2);
                            ctx.font = '600 11px sans-serif';
                            ctx.fillStyle = '#8B6B47';
                            ctx.fillText('score', meta.x, meta.y + 18);
                            ctx.restore();
                        },
                    }],
                });
            }

            function buildDoughnut(id, labels, values, colors, emptyLabel = 'No data') {
                const canvas = document.getElementById(id);

                if (!canvas) {
                    return;
                }

                const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
                const chartLabels = total > 0 ? labels : [emptyLabel];
                const chartValues = total > 0 ? values : [1];
                const chartColors = total > 0 ? colors : ['#E9DDCC'];

                new Chart(canvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: chartLabels,
                        datasets: [{
                            data: chartValues,
                            backgroundColor: chartColors,
                            borderWidth: 0,
                            cutout: '62%',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true,
                                    padding: 16,
                                    color: '#6B4E3A',
                                },
                            },
                        },
                    },
                });
            }

            function buildLine(id, labels, datasets, maxY = null) {
                const canvas = document.getElementById(id);

                if (!canvas) {
                    return;
                }

                new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: { labels, datasets },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: datasets.length > 1,
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 10,
                                    color: '#6B4E3A',
                                },
                            },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#8B6B47',
                                    maxTicksLimit: 7,
                                },
                                grid: {
                                    color: 'rgba(216, 195, 166, 0.25)',
                                },
                            },
                            y: {
                                beginAtZero: true,
                                max: maxY,
                                ticks: {
                                    color: '#8B6B47',
                                },
                                grid: {
                                    color: 'rgba(216, 195, 166, 0.25)',
                                },
                            },
                        },
                    },
                });
            }

            function buildBar(id, labels, dataset) {
                const canvas = document.getElementById(id);

                if (!canvas) {
                    return;
                }

                new Chart(canvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [dataset],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#8B6B47',
                                    maxTicksLimit: 7,
                                },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: '#8B6B47',
                                    precision: 0,
                                },
                                grid: {
                                    color: 'rgba(216, 195, 166, 0.25)',
                                },
                            },
                        },
                    },
                });
            }

            buildMetricDoughnut('accuracy-gauge-chart', overview.overall_accuracy || 0, palette.ink);
            buildMetricDoughnut('plan-gauge-chart', overview.completion_rate || 0, palette.gold);

            buildDoughnut(
                'skill-mix-chart',
                ['Listening', 'Speaking'],
                [
                    capabilityDiagnosis.listening?.completed_count || 0,
                    capabilityDiagnosis.speaking?.completed_count || 0,
                ],
                [palette.cocoa, palette.gold]
            );

            buildDoughnut(
                'frequency-mix-chart',
                ['1 session', '2 sessions', '3+ sessions'],
                [
                    effort.frequency_distribution?.one?.percent || 0,
                    effort.frequency_distribution?.two?.percent || 0,
                    effort.frequency_distribution?.three_plus?.percent || 0,
                ],
                [palette.cocoa, palette.gold, palette.mint]
            );

            buildLine(
                'daily-duration-chart',
                shortLabels(effort.daily_study_duration_trend || []),
                [{
                    label: 'Minutes',
                    data: (effort.daily_study_duration_trend || []).map(item => item.minutes),
                    borderColor: palette.cocoa,
                    backgroundColor: 'rgba(139, 107, 71, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: palette.cocoa,
                }]
            );

            buildBar(
                'daily-count-chart',
                shortLabels(effort.daily_study_count_trend || []),
                {
                    label: 'Sessions',
                    data: (effort.daily_study_count_trend || []).map(item => item.count),
                    backgroundColor: '#C9A961',
                    borderRadius: 999,
                    borderSkipped: false,
                }
            );

            buildLine(
                'overall-accuracy-chart',
                shortLabels(outcomes.overall_accuracy_trend || []),
                [{
                    label: 'Accuracy',
                    data: (outcomes.overall_accuracy_trend || []).map(item => item.accuracy),
                    borderColor: palette.ink,
                    backgroundColor: 'rgba(74, 44, 42, 0.10)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: palette.ink,
                }],
                100
            );

            buildLine(
                'listening-speaking-chart',
                shortLabels(outcomes.listening_accuracy_trend || []),
                [
                    {
                        label: 'Listening',
                        data: (outcomes.listening_accuracy_trend || []).map(item => item.accuracy),
                        borderColor: palette.cocoa,
                        backgroundColor: 'rgba(139, 107, 71, 0.10)',
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: palette.cocoa,
                    },
                    {
                        label: 'Speaking',
                        data: (outcomes.speaking_accuracy_trend || []).map(item => item.accuracy),
                        borderColor: palette.gold,
                        backgroundColor: 'rgba(201, 169, 97, 0.10)',
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: palette.gold,
                    },
                ],
                100
            );

            buildDoughnut(
                'listening-errors-chart',
                (capabilityDiagnosis.listening?.error_type_distribution || []).map(item => item.type),
                (capabilityDiagnosis.listening?.error_type_distribution || []).map(item => item.count),
                [palette.cocoa, palette.gold, palette.peach, palette.mint, palette.rose]
            );

            buildDoughnut(
                'speaking-errors-chart',
                (capabilityDiagnosis.speaking?.error_type_distribution || []).map(item => item.type),
                (capabilityDiagnosis.speaking?.error_type_distribution || []).map(item => item.count),
                [palette.gold, palette.ink, palette.peach, palette.mint, palette.rose]
            );
        })();
    </script>
@endpush

@endsection
