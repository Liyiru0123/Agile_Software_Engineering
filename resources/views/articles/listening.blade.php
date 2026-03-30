@extends('layouts.app')

@section('title', $article->title.' - Listening Training')

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
                <span class="px-3 py-1 rounded-full {{ $geminiReady ? 'bg-emerald-50 text-emerald-700' : 'bg-[#F3E7D8] text-[#6B3D2E]' }} text-xs font-semibold">
                    {{ $geminiReady ? 'Gemini-generated' : 'Fallback-generated' }}
                </span>
            </div>

            <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
            <p class="text-[#6B3D2E] leading-7 mb-6">
                Listen to the audio and type the missing words directly into the passage. When you finish, click Complete to see which answers are correct.
            </p>

            @if(!empty($listeningExercise['note']))
                <div class="rounded-2xl {{ $geminiReady ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-[#F5EEE6] text-[#6B3D2E] border-[#E0D2C2]' }} border px-4 py-3 text-sm mb-6">
                    {{ $listeningExercise['note'] }}
                </div>
            @endif

            @if($audioUrl)
                <div class="rounded-2xl border border-[#E8D9C9] bg-[#FAF4EC] p-4 mb-8">
                    <div class="text-sm font-semibold text-[#6B3D2E] mb-2">Source Audio</div>
                    <audio controls class="w-full">
                        <source src="{{ $audioUrl }}">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            @else
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 mb-8 text-sm text-amber-800">
                    This article does not yet have source audio. You can still preview the auto-generated blanks, but full listening practice works best after audio is added.
                </div>
            @endif

            <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6 lg:p-8">
                <div class="flex items-center justify-between gap-3 mb-6">
                    <div>
                        <div class="text-xs uppercase tracking-[0.15em] text-[#6B3D2E] font-semibold mb-2">Instruction</div>
                        <div class="text-sm text-[#3A2A22] leading-6">{{ $listeningExercise['instruction'] }}</div>
                    </div>
                    <button id="complete-btn" class="shrink-0 px-5 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold">
                        Complete
                    </button>
                </div>

                <div id="listening-passage" class="text-[18px] leading-[3.35rem] text-[#3A2A22]">
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
            </div>

            <div id="summary-panel" class="hidden mt-6 bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-sm">
                <div class="text-sm text-[#D7BE8A] mb-1">Score</div>
                <div id="score-value" class="text-4xl font-bold mb-3">0</div>
                <div id="summary-text" class="text-sm text-[#F5E6D3]/85 leading-6"></div>
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

    @media (max-width: 640px) {
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
const evaluateUrl = @json(route('articles.listening.evaluate', $article));
const exerciseId = @json($listeningExercise['id']);
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const completeBtn = document.getElementById('complete-btn');
const summaryPanel = document.getElementById('summary-panel');
const scoreValue = document.getElementById('score-value');
const summaryText = document.getElementById('summary-text');
const startedAt = Date.now();
const blankInputs = document.querySelectorAll('[data-answer-id]');

blankInputs.forEach((input) => {
    syncInputWidth(input);
    input.addEventListener('input', () => syncInputWidth(input));
});

completeBtn.addEventListener('click', async () => {
    const answers = { items: {} };

    blankInputs.forEach((input) => {
        answers.items[input.dataset.answerId] = input.value.trim();
    });

    completeBtn.disabled = true;
    completeBtn.textContent = 'Checking...';

    try {
        const response = await fetch(evaluateUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                exercise_id: exerciseId,
                answers,
                time_spent: Math.round((Date.now() - startedAt) / 1000),
            }),
        });

        const data = await response.json();
        renderResults(data.result);
    } catch (error) {
        summaryPanel.classList.remove('hidden');
        summaryText.textContent = 'Unable to check answers right now.';
    } finally {
        completeBtn.disabled = false;
        completeBtn.textContent = 'Complete';
    }
});

function renderResults(result) {
    scoreValue.textContent = result.score;
    summaryText.textContent = `${result.correct_count} of ${result.total_count} blanks are correct. ${result.summary}`;
    summaryPanel.classList.remove('hidden');

    result.item_results.forEach((item) => {
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
}

function syncInputWidth(input) {
    const baseChars = Number(input.dataset.blankChars || 8);
    const typedChars = input.value.trim().length + 1;
    const widthChars = Math.max(baseChars + 1, typedChars, 8);
    const maxChars = window.innerWidth <= 640 ? 14 : 18;

    input.style.width = `${Math.min(widthChars, maxChars)}ch`;
}
</script>
@endpush
