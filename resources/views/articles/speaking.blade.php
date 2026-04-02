@extends('layouts.app')

@section('title', $article->title.' - Speaking Training')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            Back to Article
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.1fr)_360px] gap-8 items-start">
            <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
                <p class="text-[#6B3D2E] leading-7 mb-6">
                    This page focuses on retelling and opinion-based speaking practice. You can replay the original audio, record your response, and compare it for completeness and natural delivery.
                </p>

                @if($audioUrl)
                    <div class="rounded-2xl border border-[#E8D9C9] bg-[#FAF4EC] p-4 mb-8">
                        <div class="text-sm font-semibold text-[#6B3D2E] mb-2">Replay the source audio before speaking</div>
                        <audio controls class="w-full">
                            <source src="{{ $audioUrl }}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @endif

                <div class="space-y-4">
                    @forelse($speakingExercises as $exercise)
                        <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-5 cursor-pointer hover:border-[#C9A961] transition-colors exercise-item"
                             data-exercise-id="{{ $exercise->id }}"
                             onclick="selectExercise(this)">
                            <div class="text-xs font-semibold uppercase tracking-[0.15em] text-[#6B3D2E] mb-2">
                                {{ $exercise->question_data['title'] ?? 'Speaking Task' }}
                            </div>
                            <div class="text-[#3A2A22] leading-7">
                                {{ $exercise->question_data['instruction'] ?? ($exercise->question_data['topic'] ?? 'No instruction provided.') }}
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-8 text-center text-[#6B3D2E]">
                            Currently there are no speaking exercises for this article.
                        </div>
                    @endforelse
                </div>
            </section>

            <aside class="space-y-6 sticky top-24">
                <div id="recorder-box" class="bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-lg {{ $speakingExercises->isEmpty() ? 'opacity-50 pointer-events-none' : '' }}">
                    <h2 class="text-2xl font-bold mb-3">Browser Recorder</h2>
                    <p class="text-sm text-[#F5E6D3]/80 leading-6 mb-5">
                        Select a task above, then record your response. Your recording will be submitted for review.
                    </p>
                    <div class="mb-4 hidden" id="selected-task-indicator">
                        <span class="text-xs font-semibold uppercase text-[#C9A961]">Selected:</span>
                        <div id="selected-task-title" class="text-sm font-medium"></div>
                    </div>
                    <div class="flex flex-col gap-3">
                        <button id="start-speaking" class="w-full rounded-2xl bg-[#C9A961] hover:bg-[#D8B777] text-[#4A2C2A] px-4 py-3 font-semibold disabled:opacity-50" disabled>
                            Start Recording
                        </button>
                        <button id="stop-speaking" class="w-full rounded-2xl bg-white/10 text-white px-4 py-3 font-semibold hidden">
                            Stop Recording
                        </button>
                        <button id="submit-recording" class="w-full rounded-2xl bg-green-600 hover:bg-green-700 text-white px-4 py-3 font-semibold hidden">
                            Submit Recording
                        </button>
                        <button id="retry-speaking" class="w-full rounded-2xl bg-[#E8D9C9] hover:bg-[#EEDFCF] text-[#4A2C2A] px-4 py-3 font-semibold hidden">
                            Record Again
                        </button>
                    </div>
                    <div id="submission-message" class="mt-3 rounded-xl border px-3 py-2 text-sm hidden"></div>
                    <div id="recording-status" class="mt-3 text-center text-xs text-[#F5E6D3]/60 hidden">
                        Recording... <span id="recording-timer">00:00</span>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-lg font-bold text-[#4A2C2A] mb-3">Audio Preview</h3>
                    <audio id="speaking-preview" controls class="w-full mt-2 hidden"></audio>
                    <div id="no-preview" class="text-sm text-[#6B3D2E]/60 italic">Record something to preview it here.</div>
                </div>

                <!-- AI Evaluation Results -->
                <div id="evaluation-box" class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6 hidden">
                    <h3 class="text-lg font-bold text-[#4A2C2A] mb-4">AI Evaluation</h3>
                    
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-[#F6F0E8]">
                        <span class="text-[#6B3D2E] font-medium text-sm">Overall Score</span>
                        <div class="text-2xl font-bold text-[#C9A961]" id="eval-score">0 / 100</div>
                    </div>

                    <div class="space-y-4">
                        <div class="eval-metric-item">
                            <div class="flex justify-between text-xs font-semibold uppercase text-[#6B3D2E] mb-1">
                                <span>Fluency</span>
                                <span id="eval-fluency-score">0 / 10</span>
                            </div>
                            <p class="text-xs text-[#6B3D2E]/80 italic" id="eval-fluency-comment"></p>
                        </div>

                        <div class="eval-metric-item">
                            <div class="flex justify-between text-xs font-semibold uppercase text-[#6B3D2E] mb-1">
                                <span>Relevance</span>
                                <span id="eval-relevance-score">0 / 10</span>
                            </div>
                            <p class="text-xs text-[#6B3D2E]/80 italic" id="eval-relevance-comment"></p>
                        </div>

                        <div class="eval-metric-item">
                            <div class="flex justify-between text-xs font-semibold uppercase text-[#6B3D2E] mb-1">
                                <span>Pronunciation</span>
                                <span id="eval-pronunciation-score">0 / 10</span>
                            </div>
                            <p class="text-xs text-[#6B3D2E]/80 italic" id="eval-pronunciation-comment"></p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-[#F6F0E8]">
                            <span class="text-xs font-semibold uppercase text-[#6B3D2E] block mb-2">Detailed Feedback</span>
                            <div class="text-sm text-[#4A2C2A] leading-6 bg-[#FAF4EC] rounded-xl p-3" id="eval-feedback"></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let speakingRecorder = null;
let speakingChunks = [];
let selectedExerciseId = null;
let recordingStartTime = null;
let timerInterval = null;
let currentBlob = null;
const pageOpenedAt = Date.now();

const startSpeaking = document.getElementById('start-speaking');
const stopSpeaking = document.getElementById('stop-speaking');
const submitRecording = document.getElementById('submit-recording');
const retrySpeaking = document.getElementById('retry-speaking');
const speakingPreview = document.getElementById('speaking-preview');
const noPreview = document.getElementById('no-preview');
const statusDiv = document.getElementById('recording-status');
const timerSpan = document.getElementById('recording-timer');
const selectedTaskIndicator = document.getElementById('selected-task-indicator');
const selectedTaskTitle = document.getElementById('selected-task-title');
const submissionMessage = document.getElementById('submission-message');

function showSubmissionMessage(message, type = 'info') {
    const typeClasses = {
        info: 'border-[#E8D9C9] bg-[#FAF4EC] text-[#3A2A22]',
        success: 'border-green-200 bg-green-50 text-green-700',
        error: 'border-red-200 bg-red-50 text-red-700',
    };

    submissionMessage.className = `mt-3 rounded-xl border px-3 py-2 text-sm ${typeClasses[type] || typeClasses.info}`;
    submissionMessage.textContent = message;
    submissionMessage.classList.remove('hidden');
}

function hideSubmissionMessage() {
    submissionMessage.textContent = '';
    submissionMessage.className = 'mt-3 rounded-xl border px-3 py-2 text-sm hidden';
}

function selectExercise(element) {
    // UI update
    document.querySelectorAll('.exercise-item').forEach(el => {
        el.classList.remove('border-[#C9A961]', 'bg-[#FFF9F2]');
        el.classList.add('bg-[#FBF7F1]');
    });
    element.classList.add('border-[#C9A961]', 'bg-[#FFF9F2]');
    element.classList.remove('bg-[#FBF7F1]');

    selectedExerciseId = element.dataset.exerciseId;
    const title = element.querySelector('.text-xs').textContent.trim();
    
    selectedTaskTitle.textContent = title;
    selectedTaskIndicator.classList.remove('hidden');
    startSpeaking.disabled = false;
    
    // Reset buttons if they were in a different state
    resetRecorderUI();
}

function resetRecorderUI() {
    startSpeaking.classList.remove('hidden');
    stopSpeaking.classList.add('hidden');
    submitRecording.classList.add('hidden');
    retrySpeaking.classList.add('hidden');
    statusDiv.classList.add('hidden');
    speakingPreview.classList.add('hidden');
    noPreview.classList.remove('hidden');
    currentBlob = null;
}

function updateTimer() {
    const now = new Date();
    const diff = Math.floor((now - recordingStartTime) / 1000);
    const mins = Math.floor(diff / 60).toString().padStart(2, '0');
    const secs = (diff % 60).toString().padStart(2, '0');
    timerSpan.textContent = `${mins}:${secs}`;
}

startSpeaking.addEventListener('click', async () => {
    try {
        hideSubmissionMessage();
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        speakingChunks = [];
        speakingRecorder = new MediaRecorder(stream);

        speakingRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                speakingChunks.push(event.data);
            }
        };

        speakingRecorder.onstop = () => {
            currentBlob = new Blob(speakingChunks, { type: 'audio/webm' });
            speakingPreview.src = URL.createObjectURL(currentBlob);
            speakingPreview.classList.remove('hidden');
            noPreview.classList.add('hidden');
            submitRecording.classList.remove('hidden');
            stream.getTracks().forEach((track) => track.stop());
            clearInterval(timerInterval);
        };

        speakingRecorder.start();
        
        recordingStartTime = new Date();
        timerSpan.textContent = "00:00";
        timerInterval = setInterval(updateTimer, 1000);

        startSpeaking.classList.add('hidden');
        stopSpeaking.classList.remove('hidden');
        statusDiv.classList.remove('hidden');
        
    } catch (error) {
        console.error('Error accessing microphone:', error);
        showSubmissionMessage('Could not access microphone. Please ensure you have given permission.', 'error');
    }
});

stopSpeaking.addEventListener('click', () => {
    if (speakingRecorder && speakingRecorder.state !== 'inactive') {
        speakingRecorder.stop();
        stopSpeaking.classList.add('hidden');
    }
});

submitRecording.addEventListener('click', async () => {
    if (!currentBlob || !selectedExerciseId) return;

    hideSubmissionMessage();
    submitRecording.disabled = true;
    submitRecording.textContent = 'Submitting...';

    const formData = new FormData();
    formData.append('audio', currentBlob, 'recording.webm');
    formData.append('exercise_id', selectedExerciseId);
    formData.append('page_opened_at', String(pageOpenedAt));
    formData.append('_token', '{{ csrf_token() }}');

    try {
        const response = await fetch("{{ route('articles.speaking.submit', $article) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();
        if (result.success) {
            showSubmissionMessage('Submitted successfully. You can review your score below.', 'success');
            
            // Display AI Evaluation Results
            if (result.evaluation) {
                const eval = result.evaluation;
                const normalizedScore = Number(eval.score ?? 0);
                document.getElementById('eval-score').textContent = `${Number.isFinite(normalizedScore) ? normalizedScore : 0} / 100`;

                const metricFields = [
                    ['fluency', 'eval-fluency-score', 'eval-fluency-comment'],
                    ['relevance', 'eval-relevance-score', 'eval-relevance-comment'],
                    ['pronunciation', 'eval-pronunciation-score', 'eval-pronunciation-comment'],
                ];

                metricFields.forEach(([metricName, scoreId, commentId]) => {
                    const metric = eval[metricName];
                    const scoreEl = document.getElementById(scoreId);
                    const commentEl = document.getElementById(commentId);

                    let score = 0;
                    let comment = '';

                    if (typeof metric === 'number') {
                        score = metric;
                    } else if (typeof metric === 'string') {
                        comment = metric;
                        const extracted = metric.match(/\d+(\.\d+)?/);
                        if (extracted) {
                            score = Number(extracted[0]);
                        }
                    } else if (metric && typeof metric === 'object') {
                        score = Number(metric.score ?? 0);
                        comment = metric.comment ?? metric.feedback ?? '';
                    }

                    scoreEl.textContent = `${Number.isFinite(score) ? score : 0} / 10`;
                    commentEl.textContent = comment;
                });

                document.getElementById('eval-feedback').textContent = eval.feedback || 'No feedback provided.';
                
                document.getElementById('evaluation-box').classList.remove('hidden');
                document.getElementById('evaluation-box').scrollIntoView({ behavior: 'smooth' });
            }

            // Update UI
            submitRecording.textContent = 'Submitted';
            submitRecording.classList.remove('bg-green-600', 'hover:bg-green-700');
            submitRecording.classList.add('bg-gray-400', 'cursor-not-allowed');
            retrySpeaking.classList.remove('hidden');
            
        } else {
            showSubmissionMessage('Submission failed: ' + (result.message || 'Unknown error'), 'error');
            submitRecording.disabled = false;
            submitRecording.textContent = 'Submit Recording';
            retrySpeaking.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Submission error:', error);
        showSubmissionMessage('An error occurred during submission. Please try recording again.', 'error');
        submitRecording.disabled = false;
        submitRecording.textContent = 'Submit Recording';
        retrySpeaking.classList.remove('hidden');
    }
});

retrySpeaking.addEventListener('click', () => {
    resetRecorderUI();
    hideSubmissionMessage();
    startSpeaking.disabled = !selectedExerciseId;
    showSubmissionMessage('Ready. Click "Start Recording" to try again.', 'info');
});

// Auto-select the first exercise on page load
document.addEventListener('DOMContentLoaded', () => {
    const firstExercise = document.querySelector('.exercise-item');
    if (firstExercise) {
        selectExercise(firstExercise);
    }
});
</script>
@endpush
