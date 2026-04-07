@extends('layouts.app')

@section('title', $article->title.' - Listening Hub')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-5xl mx-auto px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            Back to Article
        </a>

        <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8 lg:p-10">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">{{ $difficultyLabel }}</span>
                <span class="px-3 py-1 rounded-full bg-[#C9A961]/15 text-[#6B3D2E] text-xs font-semibold">{{ number_format($article->word_count) }} words</span>
                <span class="px-3 py-1 rounded-full {{ $audioUrl ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} text-xs font-semibold">
                    {{ $audioUrl ? 'Audio ready' : 'Text preview only' }}
                </span>
                <span class="px-3 py-1 rounded-full {{ $listeningExercise ? 'bg-sky-50 text-sky-700' : 'bg-slate-100 text-slate-600' }} text-xs font-semibold">
                    {{ $listeningExercise ? 'Database exercise' : 'No exercise configured' }}
                </span>
            </div>

            <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
            <p class="text-[#6B3D2E] leading-7 mb-6">
                Listen to the audio and complete all listening tasks in one flow: dictation, multiple choice, summary, then transcript review.
            </p>



            @if($audioUrl)
                <div class="rounded-2xl border border-[#E8D9C9] bg-[#FAF4EC] p-4 mb-6">
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <div class="text-sm font-semibold text-[#6B3D2E]">Source Audio</div>
                        <button id="toggle-transcript-btn" class="px-4 py-2 rounded-xl bg-[#6B3D2E] text-white text-sm font-semibold">Show Transcript</button>
                    </div>
                    <audio controls class="w-full">
                        <source src="{{ $audioUrl }}">
                        Your browser does not support the audio element.
                    </audio>
                </div>

                <div id="transcript-panel" class="hidden rounded-3xl bg-white border border-[#E8D9C9] p-6 mb-8">
                    <div class="text-sm font-semibold text-[#6B3D2E] mb-3">Listening Transcript</div>

                    <div id="reader-surface" class="relative rounded-2xl border border-[#EEE2D4] bg-[#FCF8F2] p-4">
                        <div id="transcript-reader" class="space-y-5 text-[#3A2A22] leading-8 text-[17px] relative z-[1]">
                            @foreach($articleSentenceMap as $paragraph)
                                <p class="leading-8">
                                    @foreach($paragraph['sentences'] as $sentence)
                                        <span id="sentence-{{ $sentence['anchor'] }}" data-anchor="{{ $sentence['anchor'] }}" class="article-sentence">{{ $sentence['text'] }}</span>
                                    @endforeach
                                </p>
                            @endforeach
                        </div>
                        <canvas id="annotation-layer" class="pointer-events-none opacity-0"></canvas>
                    </div>
                </div>

                <div id="markup-toolbar" class="hidden floating-toolbar">
                    <div id="markup-toolbar-handle" class="toolbar-handle">Markup Tools</div>
                    <button id="toggle-draw-btn" type="button" class="toolbar-btn toolbar-primary">Enable Markup</button>
                    <button id="pen-tool-btn" type="button" class="toolbar-btn">Pen</button>
                    <button id="eraser-tool-btn" type="button" class="toolbar-btn">Eraser</button>
                    <button id="clear-drawing-btn" type="button" class="toolbar-btn">Clear</button>
                </div>
            @else
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 mb-8 text-sm text-amber-800">
                    This article does not yet have source audio. You can still preview the auto-generated blanks, but full listening practice works best after audio is added.
                </div>
            @endif

            <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6 lg:p-8">
                <h2 class="text-2xl font-bold text-[#4A2C2A] mb-4">Fill Blanks</h2>

                @if($listeningExercise)
                    <div class="text-sm text-[#3A2A22] leading-6 mb-6">{{ $listeningExercise['instruction'] }}</div>

                    <div id="listening-passage" class="text-[18px] leading-[3.35rem] text-[#3A2A22]" data-translate-scope="true" data-article-id="{{ $article->id }}" data-source-language="en" data-target-language="zh-CN">
                        @foreach($listeningExercise['items'] as $item)
                            @php
                                [$before, $after] = array_pad(explode('_____', $item['context'], 2), 2, '');
                                $blankChars = max(strlen($item['answer'] ?? ''), 8);
                            @endphp
                            <span class="sentence-block inline">
                                <span>{{ trim($before) }}</span>
                                <span class="blank-cluster">
                                    <input
                                        data-answer-id="{{ $item['id'] }}"
                                        data-blank-chars="{{ $blankChars }}"
                                        style="width: {{ min($blankChars + 1, 18) }}ch;"
                                        class="inline-input mx-1.5 px-2 py-1 rounded-xl border border-[#D9C7B5] bg-white text-center text-[#3A2A22] focus:outline-none focus:border-[#6B3D2E]"
                                        placeholder="..."
                                        autocomplete="off"
                                        value="{{ $listeningExercise['latest_submission']['answers'][$item['id']] ?? '' }}"
                                    >
                                    <span
                                        data-result-id="{{ $item['id'] }}"
                                        class="hidden inline-result px-2 py-1 rounded-lg text-sm"
                                    ></span>
                                </span>
                                <span>{{ trim($after) }}</span>
                            </span>
                        @endforeach
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button id="complete-btn" class="px-5 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold">Complete</button>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        No listening exercise has been configured for this article yet.
                    </div>
                @endif
            </div>

            <div id="summary-panel" class="hidden mt-6 bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-sm">
                <div class="text-sm text-[#D7BE8A] mb-1">Score</div>
                <div id="score-value" class="text-4xl font-bold mb-3">0</div>
                <div id="summary-text" class="text-sm text-[#F5E6D3]/85 leading-6"></div>
            </div>

            <div class="mt-8 rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6 lg:p-8">
                <h2 class="text-2xl font-bold text-[#4A2C2A] mb-4">Dictation</h2>

                @if(!empty($listeningReadingQuestions))
                    <div id="listening-reading-question-list" class="space-y-4"></div>
                    <div class="mt-5 flex justify-end">
                        <button id="listening-reading-submit" class="px-5 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold">Submit</button>
                    </div>
                    <div id="listening-reading-score" class="hidden mt-4 rounded-2xl border border-[#DCCBB8] bg-[#F8F5F0] p-4 text-[#4A2C2A]"></div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        No multiple-choice questions are configured for this article yet.
                    </div>
                @endif
            </div>

            <div class="mt-8 rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6 lg:p-8">
                <h2 class="text-2xl font-bold text-[#4A2C2A] mb-4">Summary</h2>

                @if($listeningSummaryTask)
                    <div class="rounded-2xl bg-white border border-[#E8D9C9] p-5 mb-5">
                        <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Instruction</div>
                        <div class="text-[#3A2A22] leading-7 mb-2">{{ $listeningSummaryTask['instruction'] }}</div>
                        <div class="text-sm text-[#6B3D2E] leading-6">{{ $listeningSummaryTask['requirement'] }}</div>
                        <div class="text-sm text-[#6B3D2E] mt-3">Word target: {{ $listeningSummaryTask['word_limit']['min'] }}-{{ $listeningSummaryTask['word_limit']['max'] }}</div>
                    </div>

                    <textarea id="listening-summary-draft" rows="8" class="w-full rounded-2xl border border-[#D9C7B5] bg-white px-4 py-3 text-[#3A2A22] leading-7 focus:outline-none focus:border-[#6B3D2E]" placeholder="Write your listening summary here..."></textarea>

                    <div class="mt-4 flex items-center justify-between gap-3">
                        <span id="listening-summary-count" class="text-sm text-[#6B3D2E]">Word count: 0</span>
                        <span id="listening-summary-status" class="text-sm text-[#9A7358]"></span>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button id="listening-summary-submit" class="px-5 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold">Submit</button>
                    </div>

                    <div id="listening-summary-result" class="hidden mt-5 rounded-2xl bg-[#4A2C2A] text-white p-5">
                        <div class="text-sm text-[#D7BE8A] mb-1">Summary Score</div>
                        <div id="listening-summary-score" class="text-3xl font-bold mb-2">0</div>
                        <div id="listening-summary-text" class="text-sm text-[#F5E6D3]/90 leading-6"></div>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        No summary writing task is configured for this article yet.
                    </div>
                @endif
            </div>


        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .inline-input {
        min-width: 7rem;
        max-width: 14rem;
        height: 2.25rem;
        line-height: 2.25rem;
        vertical-align: middle;
    }

    .sentence-block {
        margin-right: 0.5rem;
    }

    .blank-cluster {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        vertical-align: middle;
    }

    .inline-result.correct {
        background: #dcfce7;
        color: #166534;
    }

    .inline-result.incorrect {
        background: #fee2e2;
        color: #991b1b;
    }

    .inline-result {
        display: inline-flex;
        align-items: center;
        line-height: 1.1rem;
        vertical-align: middle;
        white-space: nowrap;
    }

    .step-chip {
        border: 1px solid #e1d2c3;
        background: #fff;
        border-radius: 0.9rem;
        padding: 0.65rem 0.8rem;
        color: #6b3d2e;
        font-weight: 600;
    }

    .step-chip.done {
        background: #dcfce7;
        border-color: #86efac;
        color: #166534;
    }

    .mcq-card {
        border: 1px solid #e8d9c9;
        border-radius: 1rem;
        background: white;
        padding: 1rem;
    }

    .mcq-card.correct {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .mcq-card.wrong {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .mcq-option {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        border: 1px solid #eadccf;
        border-radius: 0.75rem;
        padding: 0.6rem 0.7rem;
        background: #fffdf9;
        cursor: pointer;
    }

    .article-sentence {
        display: inline;
        margin-right: 0.35rem;
        padding: 0.15rem 0.2rem;
        border-radius: 10px;
        transition: background-color .25s ease, box-shadow .25s ease, color .25s ease;
    }

    .article-sentence.source-focused {
        background: #F5EAD9;
        box-shadow: 0 0 0 1px #D8C2A0 inset;
        color: #4A2C2A;
    }

    .article-sentence.source-link {
        text-decoration: underline;
        text-underline-offset: 3px;
        text-decoration-thickness: 1.5px;
    }

    .mcq-card.question-linked {
        box-shadow: 0 0 0 2px #6B3D2E inset;
    }

    #annotation-layer {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        cursor: crosshair;
    }

    .floating-toolbar {
        position: fixed;
        left: 1rem;
        top: 45%;
        z-index: 60;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.45rem;
        width: 168px;
        padding: 0.6rem;
        border-radius: 0.9rem;
        border: 1px solid #D9C7B3;
        background: rgba(255, 249, 242, 0.95);
        box-shadow: 0 10px 30px rgba(74, 44, 42, 0.18);
        backdrop-filter: blur(6px);
        user-select: none;
    }

    .toolbar-handle {
        font-size: 0.74rem;
        font-weight: 700;
        color: #7A4E40;
        text-align: center;
        padding: 0.3rem 0.45rem;
        border: 1px dashed #D9C7B3;
        border-radius: 0.55rem;
        cursor: grab;
        background: #FFF6EC;
    }

    .toolbar-handle:active {
        cursor: grabbing;
    }

    .toolbar-btn {
        width: 100%;
        padding: 0.42rem 0.72rem;
        border-radius: 0.65rem;
        border: 1px solid #D9C7B3;
        background: white;
        color: #4A2C2A;
        font-size: 0.78rem;
        font-weight: 700;
        line-height: 1;
    }

    .toolbar-primary {
        background: #6B3D2E;
        color: #fff;
        border-color: #6B3D2E;
    }

    .tool-active {
        background: #4A2C2A !important;
        color: white !important;
        border-color: #4A2C2A !important;
    }

    @media (max-width: 640px) {
        .floating-toolbar {
            left: 0.75rem;
            top: 0.75rem;
            right: auto;
            bottom: auto;
            width: 156px;
            gap: 0.35rem;
            padding: 0.5rem;
        }

        .toolbar-btn {
            font-size: 0.74rem;
            padding: 0.38rem 0.62rem;
        }

        .inline-input {
            min-width: 6rem;
            max-width: 11rem;
            height: 2rem;
            line-height: 2rem;
        }

        .blank-cluster {
            gap: 0.3rem;
        }

        #listening-passage {
            line-height: 3rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
const listeningEvaluateUrl = @json(route('articles.listening.evaluate', $article));
const readingSubmitUrl = @json(route('articles.reading.submit', $article));
const writingEvaluateUrl = @json(route('articles.writing.evaluate', $article));
const listeningExercise = @json($listeningExercise);
const latestSubmission = @json($listeningExercise['latest_submission'] ?? null);
const listeningSummaryTask = @json($listeningSummaryTask);
const listeningReadingQuestions = @json($listeningReadingQuestions ?? []);
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const startedAt = Date.now();
const progressStorageKey = `listening-hub-progress-${@json($article->id)}`;

const progressState = { dictation: false, mcq: false, summary: false, transcript: false };
const sourceQuestionMap = {};
let transcriptMarkupApi = null;

hydrateProgress();
initDictation();
initMcq();
initSummary();
initTranscript();
renderProgress();

function initDictation() {
    const completeBtn = document.getElementById('complete-btn');
    const summaryPanel = document.getElementById('summary-panel');
    const scoreValue = document.getElementById('score-value');
    const summaryText = document.getElementById('summary-text');
    const blankInputs = document.querySelectorAll('[data-answer-id]');

    blankInputs.forEach((input) => {
        syncInputWidth(input);
        input.addEventListener('input', () => syncInputWidth(input));
    });

    if (latestSubmission?.result) {
        renderResults(latestSubmission.result);
    }

    if (!completeBtn || !listeningExercise?.id) {
        return;
    }

    completeBtn.addEventListener('click', async () => {
        const answers = { items: {} };
        blankInputs.forEach((input) => {
            answers.items[input.dataset.answerId] = input.value.trim();
        });

        completeBtn.disabled = true;
        completeBtn.textContent = 'Checking...';

        try {
            const response = await fetch(listeningEvaluateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    exercise_id: listeningExercise.id,
                    answers,
                    time_spent: Math.round((Date.now() - startedAt) / 1000),
                }),
            });

            const data = await response.json();
            renderResults(data.result);
        } catch (error) {
            summaryPanel?.classList.remove('hidden');
            if (summaryText) {
                summaryText.textContent = 'Unable to check answers right now.';
            }
        } finally {
            completeBtn.disabled = false;
            completeBtn.textContent = 'Complete';
        }
    });

    function renderResults(result) {
        if (!result || !scoreValue || !summaryText || !summaryPanel) {
            return;
        }

        scoreValue.textContent = result.score;
        summaryText.textContent = `${result.correct_count} of ${result.total_count} blanks are correct. ${result.summary}`;
        summaryPanel.classList.remove('hidden');

        (result.item_results || []).forEach((item) => {
            const resultBox = document.querySelector(`[data-result-id="${item.id}"]`);
            if (!resultBox) {
                return;
            }

            resultBox.classList.remove('hidden', 'correct', 'incorrect');

            if (item.is_correct) {
                resultBox.classList.add('correct');
                resultBox.textContent = 'Correct';
                return;
            }

            resultBox.classList.add('incorrect');
            resultBox.textContent = `Wrong: ${item.expected}`;
        });

        markProgress('dictation', true);
    }
}

function initMcq() {
    const listEl = document.getElementById('listening-reading-question-list');
    const submitBtn = document.getElementById('listening-reading-submit');
    const scoreEl = document.getElementById('listening-reading-score');

    if (!listEl || !submitBtn || !Array.isArray(listeningReadingQuestions) || listeningReadingQuestions.length === 0) {
        return;
    }

    listEl.innerHTML = listeningReadingQuestions.map((q, index) => {
        const options = (q.options || []).map((opt) => `
            <label class="mcq-option">
                <input type="radio" name="mcq_${q.id}" value="${escapeHtml(opt.key)}" />
                <span><strong>${escapeHtml(opt.key)}.</strong> ${escapeHtml(opt.text)}</span>
            </label>
        `).join('');

        return `
            <div class="mcq-card" id="mcq-card-${q.id}">
                <div class="text-sm text-[#9A7358] font-semibold mb-1">Question ${index + 1}</div>
                <div class="font-semibold text-[#4A2C2A] leading-7 mb-3">${escapeHtml(q.question_text)}</div>
                <div class="space-y-2">${options}</div>
                <div class="hidden mt-3 text-sm" id="mcq-result-${q.id}"></div>
            </div>
        `;
    }).join('');

    submitBtn.addEventListener('click', async () => {
        const payload = listeningReadingQuestions.map((q) => {
            const checked = document.querySelector(`input[name="mcq_${q.id}"]:checked`);
            return { question_id: q.id, selected: checked ? checked.value : null };
        });

        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        try {
            const res = await fetch(readingSubmitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ answers: payload }),
            });

            const data = await res.json();

            (data.results || []).forEach((result) => {
                const card = document.getElementById(`mcq-card-${result.question_id}`);
                const resultEl = document.getElementById(`mcq-result-${result.question_id}`);
                if (!card || !resultEl) {
                    return;
                }

                card.classList.remove('correct', 'wrong');
                card.classList.add(result.is_correct ? 'correct' : 'wrong');

                if (result.source_anchor) {
                    sourceQuestionMap[result.source_anchor] = result.question_id;
                }

                const locateButton = result.source_anchor
                    ? `<a href="#" class="mt-2 inline-block text-[#6B3D2E] underline locate-source-btn" data-anchor="${escapeHtml(result.source_anchor)}">Locate in Transcript</a>`
                    : '';

                resultEl.classList.remove('hidden');
                resultEl.innerHTML = `<strong>Correct:</strong> ${escapeHtml(result.correct_answer)}. ${escapeHtml(result.correct_option_text || '')}<br><strong>Analysis:</strong> ${escapeHtml(result.explanation || '')}${locateButton}`;
            });

            scoreEl.classList.remove('hidden');
            scoreEl.innerHTML = `<div class="text-lg font-bold">Dictation Score: ${data.score}</div><div class="text-sm mt-1">Correct ${data.correct_count} / ${data.total}</div>`;

            document.querySelectorAll('.locate-source-btn').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    locateSentence(button.dataset.anchor);
                });
            });

            listEl.querySelectorAll('input[type="radio"]').forEach((input) => {
                input.disabled = true;
            });

            submitBtn.textContent = 'Submitted';
            markProgress('mcq', true);
        } catch (error) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit';
        }
    });
}

function initSummary() {
    const textarea = document.getElementById('listening-summary-draft');
    const submitBtn = document.getElementById('listening-summary-submit');
    const countEl = document.getElementById('listening-summary-count');
    const statusEl = document.getElementById('listening-summary-status');
    const resultEl = document.getElementById('listening-summary-result');
    const resultScoreEl = document.getElementById('listening-summary-score');
    const resultTextEl = document.getElementById('listening-summary-text');

    if (!textarea || !submitBtn || !listeningSummaryTask?.id) {
        return;
    }

    textarea.addEventListener('input', updateWordCount);
    updateWordCount();

    submitBtn.addEventListener('click', async () => {
        const draft = textarea.value.trim();

        if (countWords(draft) < 10) {
            statusEl.textContent = 'Write a little more before submitting.';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Reviewing...';

        try {
            const res = await fetch(writingEvaluateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    exercise_id: listeningSummaryTask.id,
                    draft,
                    time_spent: Math.round((Date.now() - startedAt) / 1000),
                }),
            });

            const data = await res.json();
            if (!res.ok || !data.result) {
                throw new Error('Summary submit failed');
            }

            resultEl.classList.remove('hidden');
            resultScoreEl.textContent = Number(data.result.score).toFixed(1);
            resultTextEl.textContent = data.result.summary || 'Summary evaluated.';
            statusEl.textContent = 'Summary submitted successfully.';
            markProgress('summary', true);
        } catch (error) {
            statusEl.textContent = 'Unable to submit summary right now.';
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit';
        }
    });

    function updateWordCount() {
        const words = countWords(textarea.value);
        countEl.textContent = `Word count: ${words}`;
    }
}

function initTranscript() {
    const toggleBtn = document.getElementById('toggle-transcript-btn');
    const panel = document.getElementById('transcript-panel');
    const toolbar = document.getElementById('markup-toolbar');

    if (!toggleBtn || !panel) {
        return;
    }

    toggleBtn.addEventListener('click', () => {
        const hidden = panel.classList.toggle('hidden');
        toggleBtn.textContent = hidden ? 'Show Transcript' : 'Hide Transcript';
        if (toolbar) {
            toolbar.classList.toggle('hidden', hidden);
        }

        if (!hidden) {
            markProgress('transcript', true);
            if (!transcriptMarkupApi) {
                transcriptMarkupApi = initTranscriptMarkup();
            } else {
                transcriptMarkupApi.resize();
            }
        }
    });
}

function initTranscriptMarkup() {
    const readerSurface = document.getElementById('reader-surface');
    const canvas = document.getElementById('annotation-layer');
    const toolbar = document.getElementById('markup-toolbar');
    const toolbarHandle = document.getElementById('markup-toolbar-handle');
    const toggleDrawBtn = document.getElementById('toggle-draw-btn');
    const penToolBtn = document.getElementById('pen-tool-btn');
    const eraserToolBtn = document.getElementById('eraser-tool-btn');
    const clearDrawingBtn = document.getElementById('clear-drawing-btn');

    if (!readerSurface || !canvas || !toolbar || !toolbarHandle || !toggleDrawBtn || !penToolBtn || !eraserToolBtn || !clearDrawingBtn) {
        return null;
    }

    let drawEnabled = false;
    let drawMode = 'pen';
    let drawing = false;
    let ctx = null;
    let lastPoint = null;
    let draggingToolbar = false;
    let dragOffsetX = 0;
    let dragOffsetY = 0;

    function resizeCanvas() {
        const rect = readerSurface.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        canvas.width = Math.max(1, Math.floor(rect.width * dpr));
        canvas.height = Math.max(1, Math.floor(rect.height * dpr));
        canvas.style.width = `${rect.width}px`;
        canvas.style.height = `${rect.height}px`;

        ctx = canvas.getContext('2d');
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(dpr, dpr);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#B45309';
    }

    function setDrawMode(mode) {
        drawMode = mode;
        penToolBtn.classList.toggle('tool-active', mode === 'pen');
        eraserToolBtn.classList.toggle('tool-active', mode === 'eraser');
    }

    function setDrawEnabled(enabled) {
        drawEnabled = enabled;
        canvas.classList.toggle('pointer-events-none', !enabled);
        canvas.classList.toggle('opacity-0', !enabled);
        toggleDrawBtn.textContent = enabled ? 'Disable Markup' : 'Enable Markup';
    }

    function clampToolbarPosition(left, top) {
        const rect = toolbar.getBoundingClientRect();
        const maxLeft = Math.max(0, window.innerWidth - rect.width - 8);
        const maxTop = Math.max(0, window.innerHeight - rect.height - 8);

        return {
            left: Math.min(Math.max(8, left), maxLeft),
            top: Math.min(Math.max(8, top), maxTop),
        };
    }

    function moveToolbar(left, top) {
        const position = clampToolbarPosition(left, top);
        toolbar.style.left = `${position.left}px`;
        toolbar.style.top = `${position.top}px`;
        toolbar.style.right = 'auto';
        toolbar.style.bottom = 'auto';
    }

    function startToolbarDrag(event) {
        event.preventDefault();
        draggingToolbar = true;
        const toolbarRect = toolbar.getBoundingClientRect();
        dragOffsetX = event.clientX - toolbarRect.left;
        dragOffsetY = event.clientY - toolbarRect.top;
        window.addEventListener('mousemove', onToolbarDrag);
        window.addEventListener('mouseup', stopToolbarDrag);
    }

    function onToolbarDrag(event) {
        if (!draggingToolbar) {
            return;
        }

        moveToolbar(event.clientX - dragOffsetX, event.clientY - dragOffsetY);
    }

    function stopToolbarDrag() {
        draggingToolbar = false;
        window.removeEventListener('mousemove', onToolbarDrag);
        window.removeEventListener('mouseup', stopToolbarDrag);
    }

    function pointFromEvent(event) {
        const rect = canvas.getBoundingClientRect();
        const source = event.touches ? event.touches[0] : event;

        return {
            x: source.clientX - rect.left,
            y: source.clientY - rect.top,
        };
    }

    function startDrawing(event) {
        if (!drawEnabled || !ctx) return;
        event.preventDefault();
        drawing = true;
        lastPoint = pointFromEvent(event);
    }

    function draw(event) {
        if (!drawing || !ctx || !lastPoint) return;
        event.preventDefault();

        const nextPoint = pointFromEvent(event);
        ctx.save();
        ctx.globalCompositeOperation = drawMode === 'eraser' ? 'destination-out' : 'source-over';
        ctx.strokeStyle = drawMode === 'eraser' ? 'rgba(0,0,0,1)' : '#B45309';
        ctx.lineWidth = drawMode === 'eraser' ? 18 : 3;
        ctx.beginPath();
        ctx.moveTo(lastPoint.x, lastPoint.y);
        ctx.lineTo(nextPoint.x, nextPoint.y);
        ctx.stroke();
        ctx.restore();
        lastPoint = nextPoint;
    }

    function stopDrawing() {
        drawing = false;
        lastPoint = null;
    }

    function bindCanvasEvents() {
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);

        canvas.addEventListener('touchstart', startDrawing, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stopDrawing);
    }

    toggleDrawBtn.addEventListener('click', () => setDrawEnabled(!drawEnabled));
    penToolBtn.addEventListener('click', () => setDrawMode('pen'));
    eraserToolBtn.addEventListener('click', () => setDrawMode('eraser'));
    clearDrawingBtn.addEventListener('click', () => {
        if (ctx) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    });

    toolbarHandle.addEventListener('mousedown', startToolbarDrag);

    window.addEventListener('resize', () => {
        resizeCanvas();
        const current = toolbar.getBoundingClientRect();
        moveToolbar(current.left, current.top);
    });

    resizeCanvas();
    bindCanvasEvents();
    setDrawMode('pen');
    setDrawEnabled(false);

    return {
        resize: resizeCanvas,
    };
}

function locateQuestion(questionId) {
    if (!questionId) {
        return;
    }

    const card = document.getElementById(`mcq-card-${questionId}`);
    if (!card) {
        return;
    }

    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    card.classList.add('question-linked');
    setTimeout(() => card.classList.remove('question-linked'), 1600);
}

function locateSentence(anchor) {
    if (!anchor) {
        return;
    }

    const panel = document.getElementById('transcript-panel');
    const toggleBtn = document.getElementById('toggle-transcript-btn');
    const toolbar = document.getElementById('markup-toolbar');

    if (panel?.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        if (toggleBtn) {
            toggleBtn.textContent = 'Hide Transcript';
        }
        if (toolbar) {
            toolbar.classList.remove('hidden');
        }
        markProgress('transcript', true);
        if (!transcriptMarkupApi) {
            transcriptMarkupApi = initTranscriptMarkup();
        } else {
            transcriptMarkupApi.resize();
        }
    }

    document.querySelectorAll('.article-sentence.source-focused').forEach((el) => {
        el.classList.remove('source-focused');
    });

    const sentenceEl = document.getElementById(`sentence-${anchor}`);
    if (!sentenceEl) {
        return;
    }

    sentenceEl.classList.add('source-focused');
    const questionId = sourceQuestionMap[anchor];
    if (questionId) {
        sentenceEl.dataset.questionId = questionId;
        sentenceEl.classList.add('source-link');
        sentenceEl.title = `Go to Question ${questionId}`;
        sentenceEl.style.cursor = 'pointer';
        sentenceEl.onclick = (event) => {
            event.preventDefault();
            locateQuestion(questionId);
        };
    }
    sentenceEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function syncInputWidth(input) {
    const baseChars = Number(input.dataset.blankChars || 8);
    const typedChars = input.value.trim().length + 1;
    const widthChars = Math.max(baseChars + 1, typedChars, 8);
    const maxChars = window.innerWidth <= 640 ? 14 : 18;

    input.style.width = `${Math.min(widthChars, maxChars)}ch`;
}

function countWords(text) {
    const matches = text.match(/[A-Za-z][A-Za-z0-9'-]*/g) || [];
    return matches.length;
}

function markProgress(key, value) {
    progressState[key] = value;
    persistProgress();
    renderProgress();
}

function renderProgress() {
    const steps = ['dictation', 'mcq', 'summary', 'transcript'];
    const doneCount = steps.filter((key) => progressState[key]).length;
    const percent = Math.round((doneCount / steps.length) * 100);

    const percentEl = document.getElementById('hub-progress-percent');
    const barEl = document.getElementById('hub-progress-bar');

    if (percentEl) {
        percentEl.textContent = `${percent}%`;
    }

    if (barEl) {
        barEl.style.width = `${percent}%`;
    }

    toggleStepClass('step-dictation', progressState.dictation);
    toggleStepClass('step-mcq', progressState.mcq);
    toggleStepClass('step-summary', progressState.summary);
    toggleStepClass('step-transcript', progressState.transcript);
}

function toggleStepClass(elementId, done) {
    const el = document.getElementById(elementId);
    if (!el) {
        return;
    }

    el.classList.toggle('done', !!done);
}

function hydrateProgress() {
    try {
        const raw = localStorage.getItem(progressStorageKey);
        if (!raw) {
            return;
        }

        const parsed = JSON.parse(raw);
        ['dictation', 'mcq', 'summary', 'transcript'].forEach((key) => {
            progressState[key] = Boolean(parsed[key]);
        });
    } catch (error) {
    }
}

function persistProgress() {
    localStorage.setItem(progressStorageKey, JSON.stringify(progressState));
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

