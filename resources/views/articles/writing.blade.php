@extends('layouts.app')

@section('title', $article->title.' - Writing Training')

@php
    $initialTask = $writingTasks[0] ?? null;
@endphp

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-7xl mx-auto px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            Back to Article
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.35fr)_360px] gap-8 items-start">
            <section class="space-y-6">
                <div class="bg-white rounded-[2rem] border border-[#E0D2C2] shadow-sm p-8">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">{{ $difficultyLabel }}</span>
                        <span class="px-3 py-1 rounded-full bg-[#C9A961]/15 text-[#6B3D2E] text-xs font-semibold">{{ number_format($article->word_count) }} words</span>
                        <span class="px-3 py-1 rounded-full {{ $aiReady ? 'bg-emerald-50 text-emerald-700' : 'bg-[#F3E7D8] text-[#6B3D2E]' }} text-xs font-semibold">
                            {{ $aiProviderLabel }}
                        </span>
                        <span class="px-3 py-1 rounded-full bg-[#4A2C2A]/10 text-[#4A2C2A] text-xs font-semibold">
                            {{ count($writingTasks) }} writing tasks
                        </span>
                    </div>

                    <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
                    <p class="text-[#6B3D2E] leading-7 mb-6">
                        This writing hub now uses the project database structure directly: each task comes from `exercises`, each review is stored in `submissions`, and AI review uses the `writing` prompt configuration when a remote AI provider is available.
                    </p>

                    <div class="grid md:grid-cols-3 gap-4" id="task-switcher">
                        @foreach($writingTasks as $index => $task)
                            <button
                                type="button"
                                data-task-index="{{ $index }}"
                                class="writing-task-btn text-left rounded-3xl border px-5 py-4 transition {{ $index === 0 ? 'bg-[#4A2C2A] text-white border-[#4A2C2A] shadow-lg' : 'bg-[#FBF7F1] border-[#E7D8C8] text-[#4A2C2A] hover:border-[#6B3D2E]' }}"
                            >
                                <div class="text-xs uppercase tracking-[0.16em] {{ $index === 0 ? 'text-[#D7BE8A]' : 'text-[#9A7358]' }}" data-role="task-badge">{{ $task['badge'] }}</div>
                                <div class="text-lg font-bold mt-2">{{ $task['title'] }}</div>
                                <div class="text-sm leading-6 mt-2 {{ $index === 0 ? 'text-[#F5E6D3]/85' : 'text-[#6B3D2E]' }}">
                                    {{ $task['word_limit']['min'] }}-{{ $task['word_limit']['max'] }} words
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] border border-[#E0D2C2] shadow-sm p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                        <div>
                            <div id="current-task-badge" class="text-xs uppercase tracking-[0.18em] text-[#9A7358] font-semibold mb-2">{{ $initialTask['badge'] ?? 'Writing task' }}</div>
                            <h2 id="current-task-title" class="text-2xl font-bold text-[#4A2C2A]">{{ $initialTask['title'] ?? 'Writing Task' }}</h2>
                            <p id="current-task-instruction" class="text-[#6B3D2E] leading-7 mt-3">{{ $initialTask['instruction'] ?? '' }}</p>
                        </div>
                        <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] px-5 py-4 min-w-[220px]">
                            <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Task Target</div>
                            <div id="current-word-limit" class="text-[#4A2C2A] font-semibold">
                                {{ $initialTask['word_limit']['min'] ?? 0 }}-{{ $initialTask['word_limit']['max'] ?? 0 }} words
                            </div>
                            <div id="current-task-provider" class="text-sm text-[#6B3D2E] mt-2">
                                Source: {{ ($initialTask['provider'] ?? 'database') === 'generated-fallback' ? 'Auto-generated from article' : 'Database-configured task' }}
                            </div>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-[minmax(0,1fr)_260px] gap-6 mb-6">
                        <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6">
                            <div class="text-xs uppercase tracking-[0.15em] text-[#6B3D2E] font-semibold mb-2">Requirement</div>
                            <div id="current-task-requirement" class="text-[#3A2A22] leading-7">{{ $initialTask['requirement'] ?? '' }}</div>
                        </div>

                        <div class="rounded-3xl bg-[#4A2C2A] text-white p-6">
                            <div class="text-xs uppercase tracking-[0.16em] text-[#D7BE8A] font-semibold mb-3">Checklist</div>
                            <ul id="checkpoints-list" class="space-y-3 text-sm text-[#F5E6D3]/90 leading-6">
                                @foreach($initialTask['checkpoints'] ?? [] as $checkpoint)
                                    <li>{{ $checkpoint }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-[#F8F1E7] border border-[#E8D9C9] p-6 mb-6">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="text-xs uppercase tracking-[0.15em] text-[#6B3D2E] font-semibold">Source Excerpt</div>
                            <div class="text-xs text-[#9A7358]">Use the article ideas, but write in your own words.</div>
                        </div>
                        <div id="current-source-text" class="text-[#3A2A22] leading-7" data-translate-scope="true" data-article-id="{{ $article->id }}" data-source-language="en" data-target-language="zh-CN">{{ $initialTask['source_text'] ?? '' }}</div>
                    </div>

                    <label for="writing-draft" class="block text-sm font-semibold text-[#4A2C2A] mb-3">Your Draft</label>
                    <textarea
                        id="writing-draft"
                        rows="16"
                        class="w-full rounded-[1.75rem] border border-[#D9C7B5] bg-[#FFFDFC] px-5 py-4 text-[#3A2A22] leading-7 focus:outline-none focus:border-[#6B3D2E]"
                        placeholder="Write your response here..."
                    ></textarea>

                    <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <span id="writing-count" class="text-sm text-[#6B3D2E]">Word count: 0</span>
                            <span id="writing-range-status" class="text-sm px-3 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E]">Waiting for draft</span>
                            <span id="writing-save-status" class="text-sm text-[#9A7358]"></span>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button id="clear-writing-draft" type="button" class="px-4 py-3 rounded-2xl border border-[#D9C7B5] text-[#6B3D2E] font-semibold">
                                Clear draft
                            </button>
                            <button id="save-writing-draft" type="button" class="px-4 py-3 rounded-2xl bg-[#C9A961] text-[#4A2C2A] font-semibold">
                                Save draft
                            </button>
                            <button id="submit-writing-draft" type="button" class="px-5 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold shadow-sm">
                                Submit for review
                            </button>
                        </div>
                    </div>
                </div>

                <div id="writing-result-panel" class="hidden bg-[#4A2C2A] text-white rounded-[2rem] p-8 shadow-lg">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                        <div>
                            <div class="text-xs uppercase tracking-[0.15em] text-[#D7BE8A] font-semibold mb-2">Latest Review</div>
                            <div class="flex items-center gap-4">
                                <div id="result-score" class="text-5xl font-bold">0</div>
                                <div>
                                    <div id="result-provider" class="text-sm text-[#F5E6D3]/80">local-rubric</div>
                                    <div id="result-meta" class="text-sm text-[#F5E6D3]/80 mt-1"></div>
                                </div>
                            </div>
                        </div>
                        <div id="result-word-range" class="px-4 py-3 rounded-2xl bg-white/10 text-sm text-[#F5E6D3]/90"></div>
                    </div>

                    <p id="result-summary" class="text-[#F8EEDD] leading-7 mb-6"></p>

                    <div id="result-ai-diagnostics" class="hidden rounded-3xl border border-amber-200/40 bg-amber-50/10 px-5 py-4 text-sm text-[#F8EEDD] leading-6 mb-6"></div>

                    <div class="grid md:grid-cols-2 gap-4 mb-6" id="result-breakdown"></div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="rounded-3xl bg-white/8 p-5">
                            <div class="text-xs uppercase tracking-[0.15em] text-[#D7BE8A] font-semibold mb-3">Strengths</div>
                            <ul id="result-strengths" class="space-y-3 text-sm text-[#F5E6D3]/90 leading-6"></ul>
                        </div>
                        <div class="rounded-3xl bg-white/8 p-5">
                            <div class="text-xs uppercase tracking-[0.15em] text-[#D7BE8A] font-semibold mb-3">Next Fixes</div>
                            <ul id="result-improvements" class="space-y-3 text-sm text-[#F5E6D3]/90 leading-6"></ul>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-[#F8F1E7] text-[#4A2C2A] p-5">
                        <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Revision Coach</div>
                        <div id="result-revision" class="leading-7"></div>
                    </div>
                </div>
            </section>

            <aside class="space-y-6 sticky top-24 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:pr-2 training-sidebar-scroll">
                <div class="bg-white rounded-[2rem] border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-xl font-bold text-[#4A2C2A] mb-4">Rubric Focus</h3>
                    <ul id="rubric-focus-list" class="space-y-3 text-sm text-[#6B3D2E] leading-6">
                        @foreach($initialTask['rubric_focus'] ?? [] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white rounded-[2rem] border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-xl font-bold text-[#4A2C2A] mb-4">Recent Attempts</h3>
                    <div id="recent-attempts-list" class="space-y-4">
                        @forelse($recentWritingSubmissions as $attempt)
                            <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-4">
                                <div class="flex items-center justify-between gap-3 mb-2">
                                    <div class="text-sm font-semibold text-[#4A2C2A]">{{ $attempt['task_title'] }}</div>
                                    <span class="text-xs px-2 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E]">{{ number_format($attempt['score'], 1) }}</span>
                                </div>
                                <div class="text-xs text-[#9A7358] mb-2">{{ $attempt['provider'] }} - Attempt {{ $attempt['attempt_count'] }}</div>
                                <div class="text-sm text-[#6B3D2E] leading-6">{{ $attempt['summary'] }}</div>
                            </div>
                        @empty
                            <div id="recent-attempts-empty" class="rounded-3xl bg-[#FBF7F1] border border-dashed border-[#DDD0C1] p-4 text-sm text-[#8A654E] leading-6">
                                No writing submissions yet. Your reviewed drafts will appear here.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-[#4A2C2A] text-white rounded-[2rem] p-6 shadow-lg">
                    <h3 class="text-2xl font-bold mb-3">Implementation Notes</h3>
                    <p class="text-sm text-[#F5E6D3]/85 leading-6 mb-4">
                        `writing` now supports multiple task types, server-side evaluation, and persistent submission records. If the remote AI provider is unavailable, the page falls back to a local academic-writing rubric so the feature still works offline.
                    </p>
                    <div class="text-sm text-[#D7BE8A] leading-6">
                        Current mode: {{ $aiReady ? 'AI review + database persistence' : 'Local rubric + database persistence' }}
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .training-sidebar-scroll {
        scrollbar-width: thin;
        scrollbar-color: #c9a961 #f6f0e8;
    }

    .training-sidebar-scroll::-webkit-scrollbar {
        width: 10px;
    }

    .training-sidebar-scroll::-webkit-scrollbar-track {
        background: #f6f0e8;
        border-radius: 999px;
    }

    .training-sidebar-scroll::-webkit-scrollbar-thumb {
        background: #c9a961;
        border-radius: 999px;
        border: 2px solid #f6f0e8;
    }
</style>
@endpush

@push('scripts')
<script>
const tasks = @json($writingTasks);
const recentSubmissions = @json($recentWritingSubmissions);
const evaluateUrl = @json(route('articles.writing.evaluate', $article));
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let activeTaskIndex = 0;
let activeTaskStartedAt = Date.now();

const taskButtons = [...document.querySelectorAll('.writing-task-btn')];
const draftTextarea = document.getElementById('writing-draft');
const wordCountEl = document.getElementById('writing-count');
const rangeStatusEl = document.getElementById('writing-range-status');
const saveStatusEl = document.getElementById('writing-save-status');
const submitBtn = document.getElementById('submit-writing-draft');
const saveBtn = document.getElementById('save-writing-draft');
const clearBtn = document.getElementById('clear-writing-draft');
const resultPanel = document.getElementById('writing-result-panel');
const diagnosticsEl = document.getElementById('result-ai-diagnostics');

taskButtons.forEach((button) => {
    button.addEventListener('click', () => renderTask(Number(button.dataset.taskIndex)));
});

draftTextarea.addEventListener('input', () => {
    updateWordCount();
    saveStatusEl.textContent = 'Unsaved changes';
});

saveBtn.addEventListener('click', () => {
    const task = tasks[activeTaskIndex];
    localStorage.setItem(task.draft_key, draftTextarea.value);
    saveStatusEl.textContent = 'Draft saved locally';
});

clearBtn.addEventListener('click', () => {
    const task = tasks[activeTaskIndex];
    localStorage.removeItem(task.draft_key);
    draftTextarea.value = '';
    updateWordCount();
    saveStatusEl.textContent = 'Draft cleared';
});

submitBtn.addEventListener('click', async () => {
    const task = tasks[activeTaskIndex];
    const draft = draftTextarea.value.trim();

    if (draft.length < 10) {
        saveStatusEl.textContent = 'Write a little more before submitting.';
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Reviewing...';

    try {
        const response = await fetch(evaluateUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                exercise_id: task.id,
                draft,
                time_spent: Math.round((Date.now() - activeTaskStartedAt) / 1000),
            }),
        });

        const data = await response.json();

        if (!response.ok || !data.result) {
            throw new Error('Review failed');
        }

        task.latest_result = data.result;
        renderResult(data.result);
        prependRecentAttempt(data.result);
        localStorage.setItem(task.draft_key, draftTextarea.value);
        saveStatusEl.textContent = 'Draft reviewed and saved locally';
    } catch (error) {
        saveStatusEl.textContent = 'Unable to review this draft right now.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit for review';
    }
});

renderTask(0);

function renderTask(index) {
    activeTaskIndex = index;
    activeTaskStartedAt = Date.now();
    const task = tasks[index];

    taskButtons.forEach((button, buttonIndex) => {
        const active = buttonIndex === index;
        button.classList.toggle('bg-[#4A2C2A]', active);
        button.classList.toggle('text-white', active);
        button.classList.toggle('border-[#4A2C2A]', active);
        button.classList.toggle('shadow-lg', active);
        button.classList.toggle('bg-[#FBF7F1]', !active);
        button.classList.toggle('text-[#4A2C2A]', !active);
        button.classList.toggle('border-[#E7D8C8]', !active);
    });

    document.getElementById('current-task-badge').textContent = task.badge;
    document.getElementById('current-task-title').textContent = task.title;
    document.getElementById('current-task-instruction').textContent = task.instruction;
    document.getElementById('current-task-requirement').textContent = task.requirement;
    document.getElementById('current-source-text').textContent = task.source_text;
    document.getElementById('current-word-limit').textContent = `${task.word_limit.min}-${task.word_limit.max} words`;
    document.getElementById('current-task-provider').textContent = `Source: ${task.provider === 'generated-fallback' ? 'Auto-generated from article' : 'Database-configured task'}`;

    renderTextList('checkpoints-list', task.checkpoints);
    renderTextList('rubric-focus-list', task.rubric_focus);

    draftTextarea.value = localStorage.getItem(task.draft_key) || task.latest_result?.submitted_text || '';
    updateWordCount();
    saveStatusEl.textContent = task.latest_result ? 'Latest reviewed draft loaded' : '';

    if (task.latest_result) {
        renderResult(task.latest_result);
    } else {
        resultPanel.classList.add('hidden');
    }
}

function renderResult(result) {
    resultPanel.classList.remove('hidden');
    document.getElementById('result-score').textContent = Number(result.score).toFixed(1);
    document.getElementById('result-provider').textContent = result.provider;

    const meta = [];
    if (result.attempt_count) {
        meta.push(`Attempt ${result.attempt_count}`);
    }
    if (result.submitted_at) {
        meta.push(result.submitted_at);
    }
    document.getElementById('result-meta').textContent = meta.join(' - ');
    document.getElementById('result-summary').textContent = result.summary || 'Feedback saved.';
    renderDiagnostics(result);

    const range = result.word_range;
    document.getElementById('result-word-range').textContent = range
        ? `${range.in_range ? 'Within' : 'Outside'} target range - ${range.min}-${range.max} words`
        : 'Word range unavailable';

    document.getElementById('result-breakdown').innerHTML = (result.breakdown || []).map((item) => `
        <div class="rounded-3xl bg-white/8 p-5">
            <div class="flex items-center justify-between gap-3 mb-2">
                <div class="text-sm font-semibold text-white">${escapeHtml(item.label)}</div>
                <div class="text-sm text-[#D7BE8A]">${Number(item.score).toFixed(1)} / ${Number(item.max).toFixed(0)}</div>
            </div>
            <div class="text-sm text-[#F5E6D3]/85 leading-6">${escapeHtml(item.feedback || '')}</div>
        </div>
    `).join('');

    renderTextList('result-strengths', result.strengths || []);
    renderTextList('result-improvements', result.improvements || []);
    document.getElementById('result-revision').textContent = result.suggested_revision || 'No revision advice provided yet.';
}

function renderDiagnostics(result) {
    const summary = result.ai_diagnostics?.summary || '';
    const attempts = result.ai_diagnostics?.attempts || [];

    if (!summary) {
        diagnosticsEl.classList.add('hidden');
        diagnosticsEl.textContent = '';
        return;
    }

    const details = attempts
        .map((attempt) => {
            const excerpt = attempt.raw_excerpt ? ` Raw: ${attempt.raw_excerpt}` : '';
            return `${attempt.provider}: ${attempt.message}${excerpt}`;
        })
        .join(' | ');

    diagnosticsEl.classList.remove('hidden');
    diagnosticsEl.textContent = details ? `${summary} ${details}` : summary;
}

function prependRecentAttempt(result) {
    const task = tasks[activeTaskIndex];
    recentSubmissions.unshift({
        task_title: task.title,
        score: result.score,
        provider: result.provider,
        attempt_count: result.attempt_count || 1,
        summary: result.summary,
    });

    if (recentSubmissions.length > 6) {
        recentSubmissions.length = 6;
    }

    const container = document.getElementById('recent-attempts-list');
    container.innerHTML = recentSubmissions.map((attempt) => `
        <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-4">
            <div class="flex items-center justify-between gap-3 mb-2">
                <div class="text-sm font-semibold text-[#4A2C2A]">${escapeHtml(attempt.task_title)}</div>
                <span class="text-xs px-2 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E]">${Number(attempt.score).toFixed(1)}</span>
            </div>
            <div class="text-xs text-[#9A7358] mb-2">${escapeHtml(attempt.provider)} - Attempt ${escapeHtml(String(attempt.attempt_count || 1))}</div>
            <div class="text-sm text-[#6B3D2E] leading-6">${escapeHtml(attempt.summary || '')}</div>
        </div>
    `).join('');
}

function updateWordCount() {
    const task = tasks[activeTaskIndex];
    const words = countWords(draftTextarea.value);
    wordCountEl.textContent = `Word count: ${words}`;

    if (words === 0) {
        rangeStatusEl.textContent = 'Waiting for draft';
        rangeStatusEl.className = 'text-sm px-3 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E]';
        return;
    }

    const inRange = words >= task.word_limit.min && words <= task.word_limit.max;

    if (inRange) {
        rangeStatusEl.textContent = 'Within target range';
        rangeStatusEl.className = 'text-sm px-3 py-1 rounded-full bg-emerald-50 text-emerald-700';
        return;
    }

    rangeStatusEl.textContent = words < task.word_limit.min ? 'Below target range' : 'Above target range';
    rangeStatusEl.className = 'text-sm px-3 py-1 rounded-full bg-amber-50 text-amber-700';
}

function renderTextList(elementId, items) {
    const element = document.getElementById(elementId);
    element.innerHTML = (items || []).map((item) => `<li>${escapeHtml(item)}</li>`).join('');
}

function countWords(text) {
    const matches = text.match(/[A-Za-z][A-Za-z0-9'-]*/g) || [];
    return matches.length;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
@endpush

