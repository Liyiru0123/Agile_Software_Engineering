@extends('layouts.app')

@section('title', $article->title.' - Speaking Training')

@php
    $firstExercise = $speakingExercises->first();
    $firstShadowingClip = $shadowingClips[0] ?? null;
    $hasRecordableTask = $speakingExercises->isNotEmpty() || !empty($shadowingClips);
@endphp

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">Back to Article</a>

        <div class="grid lg:grid-cols-[minmax(0,1.1fr)_360px] gap-8 items-start">
            <section class="space-y-6">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="px-3 py-1 rounded-full bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs font-semibold">{{ $difficultyLabel }}</span>
                        <span class="px-3 py-1 rounded-full bg-[#C9A961]/15 text-[#6B3D2E] text-xs font-semibold">{{ number_format($article->word_count) }} words</span>
                        <span class="px-3 py-1 rounded-full bg-[#4A2C2A]/10 text-[#4A2C2A] text-xs font-semibold">{{ count($shadowingClips) }} paragraph clips</span>
                    </div>
                    <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
                    <p class="text-[#6B3D2E] leading-7">Speaking now supports paragraph-level shadowing clips and longer open-response prompts. Each paragraph clip jumps to its own audio window, so learners do not need to scrub through one long file every time.</p>
                </div>

                @if($audioUrl)
                    <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                        <h2 class="text-2xl font-bold text-[#4A2C2A] mb-2">Source Audio</h2>
                        <p class="text-[#6B3D2E] leading-6 mb-4">Use the full player for overview listening. Use the paragraph clip cards below for focused follow-and-repeat practice.</p>
                        <audio id="full-article-audio" controls preload="metadata" class="w-full">
                            <source src="{{ $audioUrl }}">
                        </audio>
                        <audio id="shadowing-audio-player" preload="metadata" class="hidden">
                            <source src="{{ $audioUrl }}">
                        </audio>
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-[#4A2C2A] mb-2">Short Shadowing Clips</h2>
                    <p class="text-[#6B3D2E] leading-6 mb-6">Repeat one paragraph clip at a time. Scoring focuses on accuracy, fluency, and pronunciation.</p>

                    <div class="space-y-4">
                        @forelse($shadowingClips as $clip)
                            <div class="shadowing-clip-item rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-5 cursor-pointer hover:border-[#C9A961] transition-colors" data-clip-id="{{ $clip['id'] }}">
                                <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                    <div>
                                        <div class="text-xs font-semibold uppercase tracking-[0.15em] text-[#6B3D2E] mb-2">{{ $clip['title'] }}</div>
                                        <div class="text-sm text-[#8A654E]">
                                            {{ $clip['word_count'] }} words | about {{ $clip['duration_hint_seconds'] }}s
                                            @if(!empty($clip['time_range_label']))
                                                | {{ $clip['time_range_label'] }}
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="play-shadowing-clip px-4 py-2 rounded-2xl bg-[#4A2C2A] text-white text-sm font-semibold" data-clip-id="{{ $clip['id'] }}">Play Clip</button>
                                </div>
                                <div class="rounded-2xl bg-white/80 border border-[#E9DDCF] p-4">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#8A654E] mb-2">Target Transcript</div>
                                    <div class="text-[#3A2A22] leading-7 text-sm">{{ $clip['transcript'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-8 text-center text-[#6B3D2E]">No paragraph shadowing clips are available for this article yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                    <h2 class="text-2xl font-bold text-[#4A2C2A] mb-2">Open Response Tasks</h2>
                    <p class="text-[#6B3D2E] leading-6 mb-6">These prompts keep the longer opinion and retelling practice. Scoring focuses on relevance, fluency, and pronunciation.</p>

                    <div class="space-y-4">
                        @forelse($speakingExercises as $exercise)
                            @php
                                $questionText = $exercise->question_data['question'] ?? $exercise->question_data['instruction'] ?? null;
                                $topicText = $exercise->question_data['topic'] ?? null;
                            @endphp
                            <div class="exercise-item rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-5 cursor-pointer hover:border-[#C9A961] transition-colors" data-exercise-id="{{ $exercise->id }}">
                                <div class="text-xs font-semibold uppercase tracking-[0.15em] text-[#6B3D2E] mb-2">{{ $exercise->question_data['title'] ?? 'Speaking Task' }}</div>
                                <div class="grid md:grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-white/70 border border-[#E9DDCF] p-3">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#8A654E] mb-1">Question</div>
                                        <div class="text-[#3A2A22] leading-7 text-sm">{{ $questionText ?: 'No question provided.' }}</div>
                                    </div>
                                    <div class="rounded-2xl bg-white/70 border border-[#E9DDCF] p-3">
                                        <div class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#8A654E] mb-1">Topic</div>
                                        <div class="text-[#3A2A22] leading-7 text-sm">{{ $topicText ?: 'No topic provided.' }}</div>
                                    </div>
                                </div>
                                @if(isset($exercise->question_data['prep_time']) || isset($exercise->question_data['speak_time']))
                                    <div class="mt-3 text-xs text-[#8A654E]">
                                        @if(isset($exercise->question_data['prep_time']))
                                            <span>Prep: {{ (int) $exercise->question_data['prep_time'] }}s</span>
                                        @endif
                                        @if(isset($exercise->question_data['prep_time']) && isset($exercise->question_data['speak_time']))
                                            <span> | </span>
                                        @endif
                                        @if(isset($exercise->question_data['speak_time']))
                                            <span>Speak: {{ (int) $exercise->question_data['speak_time'] }}s</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-8 text-center text-[#6B3D2E]">Currently there are no speaking exercises for this article.</div>
                        @endforelse
                    </div>
                </div>
            </section>

            <aside class="space-y-6 sticky top-24 lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto lg:pr-2 training-sidebar-scroll">
                <div id="recorder-box" class="bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-lg {{ $hasRecordableTask ? '' : 'opacity-50 pointer-events-none' }}">
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <h2 class="text-2xl font-bold">Browser Recorder</h2>
                        <span id="practice-mode-badge" class="px-3 py-1 rounded-full bg-white/10 text-[#F5E6D3] text-xs font-semibold">Open response</span>
                    </div>
                    <p id="practice-mode-description" class="text-sm text-[#F5E6D3]/80 leading-6 mb-5">{{ $hasRecordableTask ? 'Select a task above, then record your response.' : 'This article does not have a recordable speaking task yet.' }}</p>
                    <div id="selected-task-indicator" class="mb-4 hidden rounded-2xl border-2 border-[#C9A961] bg-[#FFF4D8] px-4 py-3 shadow-[0_0_0_2px_rgba(201,169,97,0.2)]">
                        <div class="text-[11px] font-black uppercase tracking-[0.16em] text-[#5A3527]">Now Selected</div>
                        <div id="selected-task-title" class="mt-1 text-base font-extrabold text-[#4A2C2A]"></div>
                    </div>
                    <div id="selected-target-box" class="hidden rounded-2xl bg-white/10 border border-white/10 px-4 py-3 text-sm text-[#F5E6D3] leading-6 mb-4"></div>
                    <div class="flex flex-col gap-3">
                        <button id="start-speaking" class="w-full rounded-2xl bg-[#C9A961] hover:bg-[#D8B777] text-[#4A2C2A] px-4 py-3 font-semibold disabled:opacity-50" disabled>Start Recording</button>
                        <button id="stop-speaking" class="w-full rounded-2xl bg-white/10 text-white px-4 py-3 font-semibold hidden">Stop Recording</button>
                        <button id="submit-recording" class="w-full rounded-2xl bg-green-600 hover:bg-green-700 text-white px-4 py-3 font-semibold hidden">Submit Recording</button>
                        <button id="retry-speaking" class="w-full rounded-2xl bg-[#E8D9C9] hover:bg-[#EEDFCF] text-[#4A2C2A] px-4 py-3 font-semibold hidden">Record Again</button>
                    </div>
                    <div id="submission-message" class="mt-3 rounded-xl border px-3 py-2 text-sm hidden"></div>
                    <div id="recording-status" class="mt-3 text-center text-xs text-[#F5E6D3]/60 hidden">Recording... <span id="recording-timer">00:00</span></div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-lg font-bold text-[#4A2C2A] mb-3">Audio Preview</h3>
                    <audio id="speaking-preview" controls class="w-full mt-2 hidden"></audio>
                    <div id="no-preview" class="text-sm text-[#6B3D2E]/60 italic">Record something to preview it here.</div>
                </div>

                <div id="evaluation-box" class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6 hidden">
                    <h3 class="text-lg font-bold text-[#4A2C2A] mb-4">AI Evaluation</h3>
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-[#F6F0E8]">
                        <span class="text-[#6B3D2E] font-medium text-sm">Overall Score</span>
                        <div id="eval-score" class="text-2xl font-bold text-[#C9A961]">0 / 100</div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-xs font-semibold uppercase text-[#6B3D2E] mb-1">
                                <span id="eval-fluency-label">Fluency</span>
                                <span id="eval-fluency-score">0 / 10</span>
                            </div>
                            <p id="eval-fluency-comment" class="text-xs text-[#6B3D2E]/80 italic"></p>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-semibold uppercase text-[#6B3D2E] mb-1">
                                <span id="eval-secondary-label">Relevance</span>
                                <span id="eval-relevance-score">0 / 10</span>
                            </div>
                            <p id="eval-relevance-comment" class="text-xs text-[#6B3D2E]/80 italic"></p>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs font-semibold uppercase text-[#6B3D2E] mb-1">
                                <span id="eval-pronunciation-label">Pronunciation</span>
                                <span id="eval-pronunciation-score">0 / 10</span>
                            </div>
                            <p id="eval-pronunciation-comment" class="text-xs text-[#6B3D2E]/80 italic"></p>
                        </div>
                        <div id="eval-target-wrapper" class="hidden mt-4 pt-4 border-t border-[#F6F0E8]">
                            <span class="text-xs font-semibold uppercase text-[#6B3D2E] block mb-2">Target Clip</span>
                            <div id="eval-target-text" class="text-sm text-[#4A2C2A] leading-6 bg-[#FAF4EC] rounded-xl p-3"></div>
                        </div>
                        <div id="eval-transcript-wrapper" class="hidden mt-4 pt-4 border-t border-[#F6F0E8]">
                            <span class="text-xs font-semibold uppercase text-[#6B3D2E] block mb-2">AI Transcript</span>
                            <div id="eval-transcript" class="text-sm text-[#4A2C2A] leading-6 bg-[#FAF4EC] rounded-xl p-3"></div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-[#F6F0E8]">
                            <span class="text-xs font-semibold uppercase text-[#6B3D2E] block mb-2">Detailed Feedback</span>
                            <div id="eval-feedback" class="text-sm text-[#4A2C2A] leading-6 bg-[#FAF4EC] rounded-xl p-3"></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .training-sidebar-scroll { scrollbar-width: thin; scrollbar-color: #c9a961 #f6f0e8; }
    .training-sidebar-scroll::-webkit-scrollbar { width: 10px; }
    .training-sidebar-scroll::-webkit-scrollbar-track { background: #f6f0e8; border-radius: 999px; }
    .training-sidebar-scroll::-webkit-scrollbar-thumb { background: #c9a961; border-radius: 999px; border: 2px solid #f6f0e8; }
</style>
@endpush

@push('scripts')
<script>
let speakingRecorder = null;
let speakingChunks = [];
let recordingStartTime = null;
let timerInterval = null;
let currentBlob = null;
let currentClipEndTime = null;
let currentPreviewUrl = null;
let currentPlayingClipId = null;
let selectedExerciseId = @json($firstExercise?->id);
let selectedClipId = @json($firstShadowingClip['id'] ?? null);
let activePracticeMode = selectedClipId ? 'shadowing' : 'open_response';

const shadowingClips = @json($shadowingClips);
const pageOpenedAt = Date.now();
const clipPlayer = document.getElementById('shadowing-audio-player');
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
const selectedTargetBox = document.getElementById('selected-target-box');
const submissionMessage = document.getElementById('submission-message');
const practiceModeBadge = document.getElementById('practice-mode-badge');
const practiceModeDescription = document.getElementById('practice-mode-description');

function canRecordCurrentTask() {
    return activePracticeMode === 'shadowing'
        ? Boolean(selectedClipId)
        : Boolean(selectedExerciseId);
}

function syncRecorderAvailability() {
    startSpeaking.disabled = !canRecordCurrentTask();
}

function showSubmissionMessage(message, type = 'info') {
    const map = {
        info: 'border-[#E8D9C9] bg-[#FAF4EC] text-[#3A2A22]',
        success: 'border-green-200 bg-green-50 text-green-700',
        error: 'border-red-200 bg-red-50 text-red-700',
    };
    submissionMessage.className = `mt-3 rounded-xl border px-3 py-2 text-sm ${map[type] || map.info}`;
    submissionMessage.textContent = message;
    submissionMessage.classList.remove('hidden');
}

function hideSubmissionMessage() {
    submissionMessage.textContent = '';
    submissionMessage.className = 'mt-3 rounded-xl border px-3 py-2 text-sm hidden';
}

function findClip(clipId) {
    return shadowingClips.find((clip) => clip.id === clipId) || null;
}

function setRecorderContext(title, mode) {
    selectedTaskTitle.textContent = title;
    selectedTaskIndicator.classList.remove('hidden');

    if (mode === 'shadowing') {
        practiceModeBadge.textContent = 'Shadowing';
        practiceModeDescription.textContent = `Selected paragraph: ${title}. Repeat the short clip as closely as you can while reading the target transcript on the left panel. Scoring focuses on accuracy, fluency, and pronunciation.`;
        selectedTargetBox.classList.add('hidden');
    } else {
        practiceModeBadge.textContent = 'Open response';
        practiceModeDescription.textContent = 'Give a longer spoken response to the selected prompt. Scoring focuses on relevance, fluency, and pronunciation.';
        selectedTargetBox.classList.add('hidden');
    }

    syncRecorderAvailability();
}

function highlightSelection() {
    document.querySelectorAll('.exercise-item,.shadowing-clip-item').forEach((el) => {
        el.classList.remove('border-[#C9A961]', 'bg-[#FFF9F2]');
        el.classList.add('bg-[#FBF7F1]');
    });

    if (activePracticeMode === 'open_response' && selectedExerciseId) {
        const node = document.querySelector(`.exercise-item[data-exercise-id="${selectedExerciseId}"]`);
        if (node) {
            node.classList.add('border-[#C9A961]', 'bg-[#FFF9F2]');
            node.classList.remove('bg-[#FBF7F1]');
        }
    }

    if (activePracticeMode === 'shadowing' && selectedClipId) {
        const node = document.querySelector(`.shadowing-clip-item[data-clip-id="${selectedClipId}"]`);
        if (node) {
            node.classList.add('border-[#C9A961]', 'bg-[#FFF9F2]');
            node.classList.remove('bg-[#FBF7F1]');
        }
    }
}

function resetRecorderUI() {
    startSpeaking.classList.remove('hidden');
    stopSpeaking.classList.add('hidden');
    submitRecording.classList.add('hidden');
    retrySpeaking.classList.add('hidden');
    statusDiv.classList.add('hidden');
    clearInterval(timerInterval);
    timerSpan.textContent = '00:00';
    submitRecording.disabled = false;
    submitRecording.textContent = 'Submit Recording';
    submitRecording.classList.remove('bg-gray-400', 'cursor-not-allowed');
    submitRecording.classList.add('bg-green-600', 'hover:bg-green-700');
    speakingPreview.classList.add('hidden');
    speakingPreview.removeAttribute('src');
    speakingPreview.load();
    if (currentPreviewUrl) {
        URL.revokeObjectURL(currentPreviewUrl);
        currentPreviewUrl = null;
    }
    noPreview.classList.remove('hidden');
    currentBlob = null;
    speakingChunks = [];
    syncRecorderAvailability();
}

function selectExercise(element) {
    selectedExerciseId = Number(element.dataset.exerciseId);
    selectedClipId = null;
    activePracticeMode = 'open_response';
    setRecorderContext(element.querySelector('.text-xs').textContent.trim(), 'open_response');
    highlightSelection();
    resetRecorderUI();
    hideSubmissionMessage();
}

function selectShadowingClip(element) {
    const clip = findClip(element.dataset.clipId);
    if (!clip) {
        return;
    }

    selectedClipId = clip.id;
    activePracticeMode = 'shadowing';
    setRecorderContext(clip.title, 'shadowing');
    highlightSelection();
    resetRecorderUI();
    hideSubmissionMessage();
}

function ensureClipPlayerReady() {
    if (!clipPlayer) {
        return Promise.reject(new Error('No clip player.'));
    }

    if (Number.isFinite(clipPlayer.duration) && clipPlayer.duration > 0) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        clipPlayer.addEventListener('loadedmetadata', () => resolve(), { once: true });
        clipPlayer.addEventListener('error', () => reject(new Error('Audio metadata failed to load.')), { once: true });
        clipPlayer.load();
    });
}

function stopClipPlayback() {
    if (!clipPlayer) {
        return;
    }

    clipPlayer.pause();
    currentClipEndTime = null;
    currentPlayingClipId = null;
    resetShadowingPlayButtons();
}

function setShadowingButtonState(clipId, isPlaying) {
    document.querySelectorAll('.play-shadowing-clip').forEach((button) => {
        const matches = button.dataset.clipId === clipId;
        button.textContent = matches && isPlaying ? 'Pause Clip' : 'Play Clip';
        button.classList.toggle('bg-[#8B4D3A]', matches && isPlaying);
        button.classList.toggle('hover:bg-[#A45B45]', matches && isPlaying);
        button.classList.toggle('bg-[#4A2C2A]', !matches || !isPlaying);
    });
}

function resetShadowingPlayButtons() {
    document.querySelectorAll('.play-shadowing-clip').forEach((button) => {
        button.textContent = 'Play Clip';
        button.classList.remove('bg-[#8B4D3A]', 'hover:bg-[#A45B45]');
        button.classList.add('bg-[#4A2C2A]');
    });
}

async function playShadowingClip(clipId) {
    const clip = findClip(clipId);
    if (!clip || !clipPlayer) {
        return;
    }

    try {
        await ensureClipPlayerReady();
        const isSameClip = currentPlayingClipId === clipId;

        if (isSameClip && !clipPlayer.paused) {
            clipPlayer.pause();
            currentPlayingClipId = clipId;
            setShadowingButtonState(clipId, false);
            return;
        }

        const duration = clipPlayer.duration;
        const hasAbsoluteRange = Number.isFinite(Number(clip.start_time)) && Number.isFinite(Number(clip.end_time));
        const startTime = hasAbsoluteRange
            ? Math.max(0, Math.min(duration - 1, Number(clip.start_time)))
            : Math.max(0, Math.min(duration - 1, duration * Number(clip.start_ratio || 0)));
        const endTime = hasAbsoluteRange
            ? Math.max(startTime + 1.2, Math.min(duration, Number(clip.end_time)))
            : Math.max(startTime + 1.2, Math.min(duration, duration * Number(clip.end_ratio || 0)));

        if (!isSameClip || clipPlayer.currentTime < startTime || clipPlayer.currentTime >= endTime) {
            clipPlayer.currentTime = startTime;
        }

        currentClipEndTime = endTime;
        currentPlayingClipId = clipId;
        setShadowingButtonState(clipId, true);
        await clipPlayer.play();
    } catch (error) {
        console.error('Clip playback error:', error);
        currentPlayingClipId = null;
        resetShadowingPlayButtons();
        showSubmissionMessage('The paragraph clip could not be played. Please use the full audio player above.', 'error');
    }
}

function updateTimer() {
    const diff = Math.floor((new Date() - recordingStartTime) / 1000);
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
            if (currentPreviewUrl) {
                URL.revokeObjectURL(currentPreviewUrl);
            }
            currentPreviewUrl = URL.createObjectURL(currentBlob);
            speakingPreview.src = currentPreviewUrl;
            speakingPreview.classList.remove('hidden');
            noPreview.classList.add('hidden');
            submitRecording.classList.remove('hidden');
            stream.getTracks().forEach((track) => track.stop());
            clearInterval(timerInterval);
        };

        speakingRecorder.start();
        recordingStartTime = new Date();
        timerSpan.textContent = '00:00';
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
    if (!currentBlob || !canRecordCurrentTask()) {
        return;
    }

    hideSubmissionMessage();
    submitRecording.disabled = true;
    submitRecording.textContent = 'Submitting...';

    const formData = new FormData();
    formData.append('audio', currentBlob, 'recording.webm');
    if (selectedExerciseId) {
        formData.append('exercise_id', String(selectedExerciseId));
    }
    formData.append('page_opened_at', String(pageOpenedAt));
    formData.append('practice_mode', activePracticeMode);

    if (activePracticeMode === 'shadowing' && selectedClipId) {
        formData.append('shadowing_clip_id', selectedClipId);
    }

    formData.append('_token', '{{ csrf_token() }}');

    try {
        const response = await fetch("{{ route('articles.speaking.submit', $article) }}", {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Unknown error');
        }

        showSubmissionMessage('Submitted successfully. You can review your score below.', 'success');

        const evalResult = result.evaluation || {};
        const labels = evalResult.metric_labels || {};
        const score = Number(evalResult.score ?? 0);
        document.getElementById('eval-score').textContent = `${Number.isFinite(score) ? score : 0} / 100`;
        document.getElementById('eval-fluency-label').textContent = labels.fluency || 'Fluency';
        document.getElementById('eval-secondary-label').textContent = labels.relevance || 'Relevance';
        document.getElementById('eval-pronunciation-label').textContent = labels.pronunciation || 'Pronunciation';

        [
            ['fluency', 'eval-fluency-score', 'eval-fluency-comment'],
            ['relevance', 'eval-relevance-score', 'eval-relevance-comment'],
            ['pronunciation', 'eval-pronunciation-score', 'eval-pronunciation-comment'],
        ].forEach(([name, scoreId, commentId]) => {
            const metric = evalResult[name];
            const scoreEl = document.getElementById(scoreId);
            const commentEl = document.getElementById(commentId);
            let metricScore = 0;
            let comment = '';

            if (typeof metric === 'number') {
                metricScore = metric;
            } else if (typeof metric === 'string') {
                comment = metric;
                const match = metric.match(/\d+(\.\d+)?/);
                if (match) {
                    metricScore = Number(match[0]);
                }
            } else if (metric && typeof metric === 'object') {
                metricScore = Number(metric.score ?? 0);
                comment = metric.comment ?? metric.feedback ?? '';
            }

            scoreEl.textContent = `${Number.isFinite(metricScore) ? metricScore : 0} / 10`;
            commentEl.textContent = comment;
        });

        document.getElementById('eval-target-text').textContent = evalResult.target_text || '';
        document.getElementById('eval-target-wrapper').classList.toggle('hidden', !evalResult.target_text);
        document.getElementById('eval-transcript').textContent = evalResult.transcript || '';
        document.getElementById('eval-transcript-wrapper').classList.toggle('hidden', !evalResult.transcript);
        document.getElementById('eval-feedback').textContent = evalResult.feedback || 'No feedback provided.';
        document.getElementById('evaluation-box').classList.remove('hidden');

        submitRecording.textContent = 'Submitted';
        submitRecording.classList.remove('bg-green-600', 'hover:bg-green-700');
        submitRecording.classList.add('bg-gray-400', 'cursor-not-allowed');
        retrySpeaking.classList.remove('hidden');
    } catch (error) {
        console.error('Submission error:', error);
        showSubmissionMessage(`Submission failed: ${error.message}`, 'error');
        submitRecording.disabled = false;
        submitRecording.textContent = 'Submit Recording';
        retrySpeaking.classList.remove('hidden');
    }
});

retrySpeaking.addEventListener('click', () => {
    resetRecorderUI();
    hideSubmissionMessage();
    showSubmissionMessage('Ready. Click "Start Recording" to try again.', 'info');
});

document.querySelectorAll('.exercise-item').forEach((item) => {
    item.addEventListener('click', () => selectExercise(item));
});

document.querySelectorAll('.shadowing-clip-item').forEach((item) => {
    item.addEventListener('click', () => selectShadowingClip(item));
});

document.querySelectorAll('.play-shadowing-clip').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.stopPropagation();
        playShadowingClip(button.dataset.clipId);
    });
});

if (clipPlayer) {
    clipPlayer.addEventListener('timeupdate', () => {
        if (currentClipEndTime !== null && clipPlayer.currentTime >= currentClipEndTime) {
            stopClipPlayback();
        }
    });
    clipPlayer.addEventListener('pause', () => {
        if (currentPlayingClipId && currentClipEndTime !== null && clipPlayer.currentTime < currentClipEndTime) {
            setShadowingButtonState(currentPlayingClipId, false);
        }
    });
    clipPlayer.addEventListener('play', () => {
        if (currentPlayingClipId) {
            setShadowingButtonState(currentPlayingClipId, true);
        }
    });
    clipPlayer.addEventListener('ended', () => {
        stopClipPlayback();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    if (selectedClipId) {
        const firstClip = document.querySelector(`.shadowing-clip-item[data-clip-id="${selectedClipId}"]`);
        if (firstClip) {
            selectShadowingClip(firstClip);
            return;
        }
    }

    const firstExercise = document.querySelector('.exercise-item');
    if (firstExercise) {
        selectExercise(firstExercise);
        return;
    }

    syncRecorderAvailability();
});

window.addEventListener('beforeunload', () => {
    if (currentPreviewUrl) {
        URL.revokeObjectURL(currentPreviewUrl);
    }
});
</script>
@endpush

