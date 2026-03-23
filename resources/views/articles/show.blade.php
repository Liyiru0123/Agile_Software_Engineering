@extends('layouts.app')

@section('content')

<h1>{{ $article->title }}</h1>

<p>
Author: {{ $article->author }} |
Level: {{ $article->level }}
</p>

@if($article->audio_url)
<div class="mb-3">
    <audio id="audio-player" src="{{ $article->audio_url }}" controls class="w-100"></audio>
    <div class="mt-2">
        <button id="btn-loop" class="btn btn-outline-primary btn-sm">单句循环: 关</button>
        <button id="btn-prev" class="btn btn-outline-secondary btn-sm">上一句</button>
        <button id="btn-next" class="btn btn-outline-secondary btn-sm">下一句</button>
        <select id="playback-rate" class="form-select form-select-sm d-inline-block w-auto">
            <option value="0.75">0.75x</option>
            <option value="1.0" selected>1.0x</option>
            <option value="1.25">1.25x</option>
            <option value="1.5">1.5x</option>
        </select>
    </div>
</div>
@endif

<hr>

<div id="article-content">
    @php
        $currentParagraph = -1;
    @endphp
    @foreach($article->segments as $segment)
        @if($currentParagraph !== $segment->paragraph_index)
            @if($currentParagraph !== -1)
                </p>
            @endif
            <p>
            @php $currentParagraph = $segment->paragraph_index; @endphp
        @endif
        <span class="sentence" data-start="{{ $segment->start_time }}" data-end="{{ $segment->end_time }}" style="cursor: pointer;">
            {{ $segment->content_en }}
        </span>
    @endforeach
    @if($currentParagraph !== -1)
        </p>
    @endif
</div>

<a href="/articles" class="btn btn-secondary mt-3">
Back
</a>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('audio-player');
    if (!audio) return;

    const sentences = Array.from(document.querySelectorAll('.sentence'));
    const btnLoop = document.getElementById('btn-loop');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const playbackRate = document.getElementById('playback-rate');

    let isLooping = false;
    let currentLoopSegment = null;

    // Click to Seek
    sentences.forEach(sentence => {
        sentence.addEventListener('click', function() {
            const start = parseFloat(this.dataset.start);
            if (!isNaN(start)) {
                audio.currentTime = start;
                audio.play();
                currentLoopSegment = this;
            }
        });
    });

    // Time Update & Loop Logic
    audio.addEventListener('timeupdate', function() {
        const currentTime = audio.currentTime;
        let activeSentence = null;

        sentences.forEach(sentence => {
            const start = parseFloat(sentence.dataset.start);
            const end = parseFloat(sentence.dataset.end);
            
            if (!isNaN(start) && !isNaN(end)) {
                if (currentTime >= start && currentTime <= end) {
                    sentence.classList.add('bg-warning'); // Highlight
                    activeSentence = sentence;
                    if (!currentLoopSegment || !isLooping) {
                        currentLoopSegment = sentence;
                    }
                } else {
                    sentence.classList.remove('bg-warning');
                }
            }
        });

        // Loop Logic
        if (isLooping && currentLoopSegment) {
            const end = parseFloat(currentLoopSegment.dataset.end);
            const start = parseFloat(currentLoopSegment.dataset.start);
            if (!isNaN(end) && currentTime >= end) {
                audio.currentTime = start;
            }
        }
    });

    // Toggle Loop
    btnLoop.addEventListener('click', function() {
        isLooping = !isLooping;
        this.textContent = '单句循环: ' + (isLooping ? '开' : '关');
        this.classList.toggle('btn-primary', isLooping);
        this.classList.toggle('btn-outline-primary', !isLooping);
    });

    // Prev/Next Sentence
    btnPrev.addEventListener('click', function() {
        if (!currentLoopSegment) return;
        const index = sentences.indexOf(currentLoopSegment);
        if (index > 0) {
            const prev = sentences[index - 1];
            const start = parseFloat(prev.dataset.start);
            if (!isNaN(start)) {
                audio.currentTime = start;
                currentLoopSegment = prev;
            }
        }
    });

    btnNext.addEventListener('click', function() {
        if (!currentLoopSegment) return;
        const index = sentences.indexOf(currentLoopSegment);
        if (index < sentences.length - 1) {
            const next = sentences[index + 1];
            const start = parseFloat(next.dataset.start);
            if (!isNaN(start)) {
                audio.currentTime = start;
                currentLoopSegment = next;
            }
        }
    });

    // Playback Rate
    playbackRate.addEventListener('change', function() {
        audio.playbackRate = parseFloat(this.value);
    });
});
</script>
@endsection