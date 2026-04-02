@extends('layouts.app')

@section('title', $article->title . ' - Reading Training')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-8">
    <div class="max-w-7xl mx-auto px-4 md:px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            &larr; Back to Article
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.65fr)_420px] gap-6 items-start">
            <section class="space-y-6 min-w-0">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm overflow-hidden">
                    <div class="px-6 md:px-8 pt-6 md:pt-8 pb-5 border-b border-[#E8D9C9]">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">
                                {{ $difficultyLabel }}
                            </span>
                            <span class="px-3 py-1 rounded-full bg-[#C9A961]/15 text-[#6B3D2E] text-xs font-semibold">
                                {{ number_format($article->word_count) }} words
                            </span>
                            <span class="px-3 py-1 rounded-full bg-[#4A2C2A]/10 text-[#4A2C2A] text-xs font-semibold">
                                {{ $estimatedMinutes }} min read
                            </span>
                        </div>

                        <h1 class="text-2xl md:text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
                        <p class="text-[#6B3D2E] leading-7 text-sm md:text-base">
                            Keep the article open on the left while answering the questions on the right. Detail-question analysis will point you back to the exact supporting sentence.
                        </p>
                    </div>

                    <div class="px-6 md:px-8 py-5 border-b border-[#E8D9C9] bg-[#FCF8F2]">
                        <div class="flex flex-wrap items-center gap-3">
                            <button id="toggle-draw-btn"
                                    type="button"
                                    class="px-4 py-2 rounded-xl bg-[#6B3D2E] text-white text-sm font-semibold hover:bg-[#4A2C2A] transition">
                                Enable Markup
                            </button>
                            <button id="pen-tool-btn"
                                    type="button"
                                    class="px-4 py-2 rounded-xl border border-[#D9C7B3] text-[#4A2C2A] text-sm font-semibold bg-white hover:bg-[#F7EFE4] transition">
                                Pen
                            </button>
                            <button id="eraser-tool-btn"
                                    type="button"
                                    class="px-4 py-2 rounded-xl border border-[#D9C7B3] text-[#4A2C2A] text-sm font-semibold bg-white hover:bg-[#F7EFE4] transition">
                                Eraser
                            </button>
                            <button id="clear-drawing-btn"
                                    type="button"
                                    class="px-4 py-2 rounded-xl border border-[#D9C7B3] text-[#4A2C2A] text-sm font-semibold bg-white hover:bg-[#F7EFE4] transition">
                                Clear Board
                            </button>
                            <span id="drawing-status" class="text-sm text-[#7A4E40]">
                                Markup is off. Turn it on if you want to underline or circle parts of the text.
                            </span>
                        </div>
                    </div>

                    <div id="reader-surface"
                         class="relative px-6 md:px-8 py-8"
                         data-translate-scope="true"
                         data-article-id="{{ $article->id }}"
                         data-source-language="en"
                         data-target-language="zh-CN">
                        <div class="space-y-6 text-[#3A2A22] leading-8 text-[17px] relative z-[1]">
                            @foreach($articleSentenceMap as $paragraph)
                                <p class="leading-9"
                                   data-article-id="{{ $article->id }}"
                                   data-paragraph-index="{{ $loop->index }}">
                                    @foreach($paragraph['sentences'] as $sentence)
                                        <span id="sentence-{{ $sentence['anchor'] }}"
                                              data-anchor="{{ $sentence['anchor'] }}"
                                              class="article-sentence">
                                            {{ $sentence['text'] }}
                                        </span>
                                    @endforeach
                                </p>
                            @endforeach
                        </div>

                        <canvas id="annotation-layer" class="pointer-events-none opacity-0"></canvas>
                    </div>
                </div>
            </section>

            <aside class="space-y-6 lg:sticky lg:top-20 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:pr-2 training-sidebar-scroll">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h2 class="text-2xl font-bold text-[#4A2C2A] mb-3">Reading Questions</h2>
                    <p class="text-sm text-[#6B3D2E] leading-6">
                        Answer the questions in the sidebar, then open the analysis to check the explanation and jump back to the exact source sentence.
                    </p>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-xl font-bold text-[#4A2C2A] mb-4">Reading Timer</h3>

                    <label class="text-sm text-[#6B3D2E] block mb-2">Custom Duration (minutes)</label>
                    <div class="flex items-center gap-2 mb-4">
                        <input id="minutes-input"
                               type="number"
                               min="1"
                               value="15"
                               class="w-28 rounded-xl border border-[#D9C7B3] px-3 py-2 text-[#4A2C2A] focus:outline-none focus:ring-2 focus:ring-[#C89B72]">
                        <button id="start-timer-btn"
                                class="px-4 py-2 rounded-xl bg-[#2F6B5A] text-white text-sm font-semibold hover:bg-[#255648] transition">
                            Start
                        </button>
                        <button id="reset-timer-btn"
                                class="px-4 py-2 rounded-xl bg-[#8E7D6B] text-white text-sm font-semibold hover:bg-[#766654] transition">
                            Reset
                        </button>
                    </div>

                    <div id="timer-display" class="text-3xl font-extrabold text-[#4A2C2A] tracking-wider">15:00</div>
                    <div id="timer-message" class="mt-3 text-sm text-[#6B3D2E]"></div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <div id="question-list" class="space-y-4"></div>

                    <div class="mt-6 pt-5 border-t border-[#E8D9C9]">
                        <button id="submit-all-btn"
                                class="w-full px-6 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold hover:bg-[#5A3125] transition">
                            Submit All Answers
                        </button>
                        <p id="submit-tip" class="text-sm text-[#7A4E40] mt-3">
                            Please answer every question before submitting.
                        </p>
                    </div>

                    <div id="score-panel" class="hidden mt-5 rounded-2xl p-4 border bg-[#F8F5F0] border-[#DCCBB8]"></div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-xl font-bold text-[#4A2C2A] mb-4">Key Vocabulary</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($keywords as $keyword)
                            <span class="px-3 py-1.5 rounded-full bg-[#F3E7D8] text-[#4A2C2A] text-sm font-medium">{{ $keyword }}</span>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<style>
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

#annotation-layer {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 2;
    cursor: crosshair;
}

.question-card {
    border: 1px solid #E8D9C9;
    border-radius: 18px;
    padding: 18px;
    transition: all .2s ease;
    background: #fff;
}

.question-card.wrong {
    border-color: #DC2626;
    background: #FEF2F2;
}

.question-card.correct {
    border-color: #059669;
    background: #ECFDF5;
}

.option-label {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    border: 1px solid #EADCCF;
    border-radius: 14px;
    padding: 11px 12px;
    cursor: pointer;
    transition: all .15s ease;
    background: #FFFCF8;
}

.option-label:hover {
    background: #FAF4ED;
}

.analysis-box {
    margin-top: 12px;
    border-radius: 16px;
    padding: 14px;
    background: #FFFDFC;
    border: 1px solid #E7D7C7;
    color: #5C4032;
}

.source-box {
    margin-top: 12px;
    border-radius: 14px;
    padding: 12px 14px;
    background: #F5EAD9;
    border: 1px solid #D8C2A0;
    color: #5B4431;
}

.warn-5-min {
    color: #92400E !important;
    background: #FEF3C7;
    border: 1px solid #F59E0B;
    border-radius: 10px;
    padding: 8px 10px;
    display: inline-block;
}

.time-up {
    color: #991B1B !important;
    background: #FEE2E2;
    border: 1px solid #EF4444;
    border-radius: 10px;
    padding: 8px 10px;
    display: inline-block;
}

.tool-active {
    background: #4A2C2A !important;
    color: white !important;
    border-color: #4A2C2A !important;
}

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
@endsection

@push('scripts')
<script>
(() => {
    const questions = @json($readingQuestions);
    const submitUrl = @json(route('articles.reading.submit', $article));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const questionListEl = document.getElementById('question-list');
    const submitBtn = document.getElementById('submit-all-btn');
    const submitTipEl = document.getElementById('submit-tip');
    const scorePanelEl = document.getElementById('score-panel');

    const minutesInput = document.getElementById('minutes-input');
    const startTimerBtn = document.getElementById('start-timer-btn');
    const resetTimerBtn = document.getElementById('reset-timer-btn');
    const timerDisplay = document.getElementById('timer-display');
    const timerMessage = document.getElementById('timer-message');

    const readerSurface = document.getElementById('reader-surface');
    const canvas = document.getElementById('annotation-layer');
    const drawingStatus = document.getElementById('drawing-status');
    const toggleDrawBtn = document.getElementById('toggle-draw-btn');
    const penToolBtn = document.getElementById('pen-tool-btn');
    const eraserToolBtn = document.getElementById('eraser-tool-btn');
    const clearDrawingBtn = document.getElementById('clear-drawing-btn');

    let submitted = false;
    let timerId = null;
    let totalSeconds = 15 * 60;
    let remainingSeconds = totalSeconds;
    let warnedFiveMin = false;

    let drawEnabled = false;
    let drawMode = 'pen';
    let drawing = false;
    let ctx = null;
    let lastPoint = null;

    function formatTime(sec) {
        const m = Math.floor(sec / 60).toString().padStart(2, '0');
        const s = (sec % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    function renderTimer() {
        timerDisplay.textContent = formatTime(remainingSeconds);
    }

    function stopTimer() {
        if (timerId) {
            clearInterval(timerId);
            timerId = null;
        }
    }

    function startTimer() {
        const m = Number(minutesInput.value);
        if (!Number.isInteger(m) || m <= 0) {
            alert('Please enter a valid number of minutes.');
            return;
        }

        stopTimer();
        totalSeconds = m * 60;
        remainingSeconds = totalSeconds;
        warnedFiveMin = false;
        timerMessage.className = 'mt-3 text-sm text-[#6B3D2E]';
        timerMessage.textContent = '';
        renderTimer();

        timerId = setInterval(() => {
            remainingSeconds -= 1;
            if (remainingSeconds < 0) remainingSeconds = 0;
            renderTimer();

            if (remainingSeconds === 300 && !warnedFiveMin) {
                warnedFiveMin = true;
                timerMessage.className = 'mt-3 text-sm warn-5-min';
                timerMessage.textContent = '5 minutes remaining';
            }

            if (remainingSeconds === 0) {
                stopTimer();
                timerMessage.className = 'mt-3 text-sm time-up';
                timerMessage.textContent = 'Time is up. The system will auto-submit your current answers.';
                if (!submitted) {
                    handleSubmit(true);
                }
            }
        }, 1000);
    }

    function resetTimer() {
        stopTimer();
        const m = Number(minutesInput.value) > 0 ? Number(minutesInput.value) : 15;
        totalSeconds = m * 60;
        remainingSeconds = totalSeconds;
        warnedFiveMin = false;
        timerMessage.className = 'mt-3 text-sm text-[#6B3D2E]';
        timerMessage.textContent = '';
        renderTimer();
    }

    function createQuestionCard(q, index) {
        const optionsHtml = (q.options || []).map(opt => `
            <label class="option-label">
                <input type="radio" name="q_${q.id}" value="${opt.key}" />
                <span><strong>${opt.key}.</strong> ${opt.text}</span>
            </label>
        `).join('');

        return `
            <div class="question-card" id="question-card-${q.id}" data-question-id="${q.id}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs px-2 py-1 rounded-full bg-[#F3E7D8] text-[#6B3D2E] font-semibold">Question ${index + 1}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-[#F8F2EA] text-[#7A4E40]">${q.question_type}</span>
                        </div>
                        <h4 class="font-semibold text-[#4A2C2A] leading-7">${q.question_text}</h4>
                    </div>
                    <span id="badge-${q.id}" class="text-xs px-2 py-1 rounded-full bg-[#EFE5D9] text-[#6B3D2E] whitespace-nowrap">Pending</span>
                </div>

                <div class="mt-4 space-y-2">${optionsHtml}</div>

                <div class="mt-3 hidden" id="result-${q.id}"></div>

                <button type="button"
                        class="hidden mt-4 text-sm px-3 py-1.5 rounded-lg bg-[#4A2C2A] text-white hover:bg-[#362018] transition"
                        id="toggle-analysis-${q.id}">
                    Show Answer & Analysis
                </button>

                <div class="analysis-box hidden" id="analysis-${q.id}"></div>
            </div>
        `;
    }

    function renderQuestions() {
        if (!questions.length) {
            questionListEl.innerHTML = `
                <div class="rounded-2xl border border-dashed border-[#D9C7B3] px-4 py-5 text-sm text-[#7A4E40] bg-[#FCF8F2]">
                    No reading questions are configured for this article yet.
                </div>
            `;
            submitBtn.disabled = true;
            submitTipEl.textContent = 'Add reading questions to the database before using this page.';
            return;
        }

        questionListEl.innerHTML = questions.map(createQuestionCard).join('');
    }

    function getAnswersPayload() {
        return questions.map(q => {
            const checked = document.querySelector(`input[name="q_${q.id}"]:checked`);
            return {
                question_id: q.id,
                selected: checked ? checked.value : null,
            };
        });
    }

    function allAnswered() {
        return questions.every(q => document.querySelector(`input[name="q_${q.id}"]:checked`));
    }

    function lockAllOptions() {
        document.querySelectorAll('#question-list input[type="radio"]').forEach(input => {
            input.disabled = true;
        });
    }

    function clearSentenceFocus() {
        document.querySelectorAll('.article-sentence.source-focused').forEach(el => {
            el.classList.remove('source-focused');
        });
    }

    function locateSentence(anchor) {
        if (!anchor) {
            return;
        }

        clearSentenceFocus();

        const sentenceEl = document.getElementById(`sentence-${anchor}`);
        if (!sentenceEl) {
            return;
        }

        sentenceEl.classList.add('source-focused');
        sentenceEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function renderResultUI(result) {
        const card = document.getElementById(`question-card-${result.question_id}`);
        const badge = document.getElementById(`badge-${result.question_id}`);
        const resultEl = document.getElementById(`result-${result.question_id}`);
        const analysisBtn = document.getElementById(`toggle-analysis-${result.question_id}`);
        const analysisEl = document.getElementById(`analysis-${result.question_id}`);

        card.classList.remove('wrong', 'correct');

        if (result.is_correct) {
            card.classList.add('correct');
            badge.textContent = 'Correct';
            badge.className = 'text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 whitespace-nowrap';
            resultEl.className = 'mt-3 text-sm rounded-xl px-3 py-2 bg-emerald-50 text-emerald-700';
            resultEl.textContent = `Your answer: ${result.your_answer ?? 'No answer'} (Correct)`;
        } else {
            card.classList.add('wrong');
            badge.textContent = 'Incorrect';
            badge.className = 'text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 whitespace-nowrap';
            resultEl.className = 'mt-3 text-sm rounded-xl px-3 py-2 bg-red-50 text-red-700';
            resultEl.textContent = `Your answer: ${result.your_answer ?? 'No answer'} (Incorrect)`;
        }

        const sourceHtml = result.source_excerpt ? `
            <div class="source-box">
                <div class="text-xs uppercase tracking-[0.16em] font-semibold text-[#8A684B] mb-2">Source Sentence</div>
                <div class="leading-7">${result.source_excerpt}</div>
                ${result.source_label ? `<div class="mt-2 text-xs text-[#7A5A41]">${result.source_label}</div>` : ''}
                ${result.source_anchor ? `
                    <button type="button"
                            class="mt-3 inline-flex items-center rounded-lg bg-[#6B3D2E] text-white text-sm font-semibold px-3 py-2 hover:bg-[#4A2C2A] transition locate-source-btn"
                            data-anchor="${result.source_anchor}">
                        Locate in Article
                    </button>
                ` : ''}
            </div>
        ` : '';

        analysisEl.innerHTML = `
            <div class="text-sm leading-7">
                <div><strong>Correct Answer:</strong> ${result.correct_answer}. ${result.correct_option_text ?? ''}</div>
                <div class="mt-2"><strong>Analysis:</strong> ${result.explanation ?? 'No explanation available.'}</div>
                ${sourceHtml}
            </div>
        `;

        analysisBtn.classList.remove('hidden');
        analysisBtn.onclick = () => {
            const isHidden = analysisEl.classList.toggle('hidden');
            analysisBtn.textContent = isHidden ? 'Show Answer & Analysis' : 'Hide Answer & Analysis';
        };

        analysisEl.querySelectorAll('.locate-source-btn').forEach(button => {
            button.addEventListener('click', () => locateSentence(button.dataset.anchor));
        });

        resultEl.classList.remove('hidden');
    }

    async function handleSubmit(auto = false) {
        if (!questions.length || submitted) return;

        if (!allAnswered() && !auto) {
            alert('Please answer all questions before submitting.');
            return;
        }

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = auto ? 'Auto-submitting...' : 'Submitting...';

            const res = await fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    answers: getAnswersPayload(),
                }),
            });

            if (!res.ok) {
                throw new Error('Submission failed');
            }

            const data = await res.json();
            submitted = true;
            stopTimer();
            lockAllOptions();

            (data.results || []).forEach(renderResultUI);

            scorePanelEl.classList.remove('hidden');
            scorePanelEl.innerHTML = `
                <div class="text-[#4A2C2A] text-lg font-bold">Score: ${data.score}</div>
                <div class="text-sm text-[#6B3D2E] mt-1">
                    Total ${data.total} questions, Correct ${data.correct_count}, Incorrect ${data.wrong_count}
                </div>
            `;

            submitBtn.textContent = 'Submitted';
            submitTipEl.textContent = auto
                ? 'Time is up. Your current answers were submitted automatically.'
                : 'Submitted successfully. Open each analysis card to review the answer and source sentence.';
        } catch (error) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit All Answers';
            alert('Submission failed. Please try again later.');
        }
    }

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
        drawingStatus.textContent = enabled
            ? 'Markup is on. Draw directly over the article text.'
            : 'Markup is off. Turn it on if you want to underline or circle parts of the text.';
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

    submitBtn.addEventListener('click', () => handleSubmit(false));
    startTimerBtn.addEventListener('click', startTimer);
    resetTimerBtn.addEventListener('click', resetTimer);

    toggleDrawBtn.addEventListener('click', () => setDrawEnabled(!drawEnabled));
    penToolBtn.addEventListener('click', () => setDrawMode('pen'));
    eraserToolBtn.addEventListener('click', () => setDrawMode('eraser'));
    clearDrawingBtn.addEventListener('click', () => {
        if (ctx) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    });

    window.addEventListener('resize', resizeCanvas);

    renderQuestions();
    renderTimer();
    resizeCanvas();
    bindCanvasEvents();
    setDrawMode('pen');
    setDrawEnabled(false);
})();
</script>
@endpush
