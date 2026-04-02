@extends('layouts.app')

@section('title', $article->title . ' - Reading Training')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-8">
    <div class="max-w-6xl mx-auto px-4 md:px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            â†?Back to Article
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.35fr)_360px] gap-6 items-start">
            {{-- Left side: article + questions --}}
            <section class="space-y-6">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6 md:p-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-[#4A2C2A] mb-4">{{ $article->title }}</h1>
                    <div class="space-y-5 text-[#3A2A22] leading-8 text-[17px]" data-translate-scope="true" data-article-id="{{ $article->id }}" data-source-language="en" data-target-language="zh-CN">
                        @foreach($paragraphs as $paragraph)
                            <p data-article-id="{{ $article->id }}" data-paragraph-index="{{ $loop->index }}">{{ $paragraph }}</p>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-[#4A2C2A] mb-4">Reading Questions</h2>

                    <div id="question-loading" class="text-sm text-[#6B3D2E] mb-4">Loading questions...</div>
                    <div id="question-list" class="space-y-5"></div>

                    {{-- Bottom submit area --}}
                    <div class="mt-8 pt-6 border-t border-[#E8D9C9]">
                        <button id="submit-all-btn"
                                class="w-full md:w-auto px-6 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold hover:bg-[#5A3125] transition">
                            Submit All
                        </button>
                        <p id="submit-tip" class="text-sm text-[#7A4E40] mt-3"></p>
                    </div>

                    {{-- Score panel --}}
                    <div id="score-panel" class="hidden mt-6 rounded-2xl p-4 border"></div>
                </div>
            </section>

            {{-- Right side: timer + keywords --}}
            <aside class="space-y-6 lg:sticky lg:top-20">
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
.question-card {
    border: 1px solid #E8D9C9;
    border-radius: 16px;
    padding: 16px;
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
    border-radius: 12px;
    padding: 10px 12px;
    cursor: pointer;
    transition: all .15s ease;
}
.option-label:hover { background: #FAF4ED; }
.explain-box {
    margin-top: 12px;
    border-radius: 12px;
    padding: 12px;
    background: #FFF7ED;
    border: 1px solid #F5D0A9;
    color: #7C2D12;
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
</style>
@endsection

@push('scripts')
<script>
(() => {
    const articleId = @json($article->id);

    const questionListEl = document.getElementById('question-list');
    const loadingEl = document.getElementById('question-loading');
    const submitBtn = document.getElementById('submit-all-btn');
    const submitTipEl = document.getElementById('submit-tip');
    const scorePanelEl = document.getElementById('score-panel');

    const minutesInput = document.getElementById('minutes-input');
    const startTimerBtn = document.getElementById('start-timer-btn');
    const resetTimerBtn = document.getElementById('reset-timer-btn');
    const timerDisplay = document.getElementById('timer-display');
    const timerMessage = document.getElementById('timer-message');

    let questions = [];
    let submitted = false;

    let timerId = null;
    let totalSeconds = 15 * 60;
    let remainingSeconds = totalSeconds;
    let warnedFiveMin = false;

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
            alert('Please enter a valid number of minutes (greater than 0).');
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
                timerMessage.textContent = 'Time is up. The system will auto-submit.';
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
                    <h4 class="font-semibold text-[#4A2C2A] leading-7">Q${index + 1}. ${q.question_text}</h4>
                    <span id="badge-${q.id}" class="text-xs px-2 py-1 rounded-full bg-[#EFE5D9] text-[#6B3D2E]">Not Submitted</span>
                </div>

                <div class="mt-3 space-y-2">${optionsHtml}</div>

                <div class="mt-3 hidden" id="result-${q.id}"></div>

                <div class="mt-3">
                    <button class="hidden text-sm px-3 py-1.5 rounded-lg bg-[#3B82F6] text-white"
                            id="toggle-explain-${q.id}">
                        View Answer & Explanation
                    </button>
                    <div class="explain-box hidden" id="explain-${q.id}"></div>
                </div>
            </div>
        `;
    }

    function getAnswersPayload() {
        return questions.map(q => {
            const checked = document.querySelector(`input[name="q_${q.id}"]:checked`);
            return {
                question_id: q.id,
                selected: checked ? checked.value : null
            };
        });
    }

    function allAnswered() {
        return questions.every(q => document.querySelector(`input[name="q_${q.id}"]:checked`));
    }

    function lockAllOptions() {
        document.querySelectorAll('#question-list input[type="radio"]').forEach(i => i.disabled = true);
    }

    function renderResultUI(result) {
        const card = document.getElementById(`question-card-${result.question_id}`);
        const badge = document.getElementById(`badge-${result.question_id}`);
        const resultEl = document.getElementById(`result-${result.question_id}`);
        const explainBtn = document.getElementById(`toggle-explain-${result.question_id}`);
        const explainEl = document.getElementById(`explain-${result.question_id}`);

        card.classList.remove('wrong', 'correct');
        if (result.is_correct) {
            card.classList.add('correct');
            badge.textContent = 'Correct';
            badge.className = 'text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-700';
            resultEl.className = 'mt-3 text-sm rounded-lg px-3 py-2 bg-emerald-50 text-emerald-700';
            resultEl.textContent = `Your answer: ${result.your_answer ?? 'No Answer'} (Correct)`;
        } else {
            card.classList.add('wrong');
            badge.textContent = 'Incorrect';
            badge.className = 'text-xs px-2 py-1 rounded-full bg-red-100 text-red-700';
            resultEl.className = 'mt-3 text-sm rounded-lg px-3 py-2 bg-red-50 text-red-700';
            resultEl.textContent = `Your answer: ${result.your_answer ?? 'No Answer'} (Incorrect)`;
        }
        resultEl.classList.remove('hidden');

        explainEl.innerHTML = `
            <div><strong>Correct Answer:</strong>${result.correct_answer ?? '-'}</div>
            <div class="mt-1"><strong>Explanation:</strong>${result.explanation ?? 'No explanation available'}</div>
        `;

        explainBtn.classList.remove('hidden');
        explainBtn.addEventListener('click', () => {
            explainEl.classList.toggle('hidden');
            explainBtn.textContent = explainEl.classList.contains('hidden')
                ? 'View Answer & Explanation'
                : 'Hide Answer & Explanation';
        });
    }

    async function loadQuestions() {
        try {
            const res = await fetch(`/api/articles/${articleId}/quiz/questions`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) throw new Error('Failed to load questions');

            const data = await res.json();
            questions = data.readingQuestions || [];

            if (!questions.length) {
                loadingEl.textContent = 'No multiple-choice questions for this article yet.';
                return;
            }

            loadingEl.classList.add('hidden');
            questionListEl.innerHTML = questions.map(createQuestionCard).join('');
            submitTipEl.textContent = 'Please answer all questions before clicking Submit All.';
        } catch (e) {
            loadingEl.textContent = 'Failed to load questions. Please refresh and try again.';
        }
    }

    async function handleSubmit(auto = false) {
        if (!questions.length || submitted) return;

        if (!allAnswered() && !auto) {
            alert('Please answer all questions before submitting.');
            return;
        }

        const answers = getAnswersPayload();

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = auto ? 'Auto-submitting...' : 'Submitting...';

            const res = await fetch(`/api/articles/${articleId}/quiz/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ answers })
            });

            if (!res.ok) throw new Error('Submission failed');

            const data = await res.json();
            submitted = true;
            stopTimer();
            lockAllOptions();

            (data.results || []).forEach(renderResultUI);

            scorePanelEl.classList.remove('hidden');
            scorePanelEl.className = 'mt-6 rounded-2xl p-4 border bg-[#F8F5F0] border-[#DCCBB8]';
            scorePanelEl.innerHTML = `
                <div class="text-[#4A2C2A] text-lg font-bold">Score: ${data.score}</div>
                <div class="text-sm text-[#6B3D2E] mt-1">
                    Total ${data.total} questions, Correct ${data.correct_count}, Incorrect ${data.wrong_count}
                </div>
            `;

            submitBtn.textContent = 'Submitted';
            submitTipEl.textContent = auto ? 'Time is up. Auto-submitted.' : 'Submitted successfully. You can now view answers and explanations.';
        } catch (e) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit All';
            alert('Submission failed. Please try again later.');
        }
    }

    submitBtn.addEventListener('click', () => handleSubmit(false));
    startTimerBtn.addEventListener('click', startTimer);
    resetTimerBtn.addEventListener('click', resetTimer);

    renderTimer();
    loadQuestions();
})();
</script>
@endpush
