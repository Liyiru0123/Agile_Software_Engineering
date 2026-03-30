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
                    @foreach($speakingPrompts as $prompt)
                        <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-5">
                            <div class="text-xs font-semibold uppercase tracking-[0.15em] text-[#6B3D2E] mb-2">{{ $prompt['title'] }}</div>
                            <div class="text-[#3A2A22] leading-7">{{ $prompt['instruction'] }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            <aside class="space-y-6 sticky top-24">
                <div class="bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-lg">
                    <h2 class="text-2xl font-bold mb-3">Browser Recorder</h2>
                    <p class="text-sm text-[#F5E6D3]/80 leading-6 mb-5">
                        Recordings stay in the current browser session only. This is useful for self-review and shadowing, and it can later be extended with Gemini-based scoring.
                    </p>
                    <div class="flex gap-3">
                        <button id="start-speaking" class="flex-1 rounded-2xl bg-[#C9A961] hover:bg-[#D8B777] text-[#4A2C2A] px-4 py-3 font-semibold">
                            Start
                        </button>
                        <button id="stop-speaking" class="flex-1 rounded-2xl bg-white/10 text-white px-4 py-3 font-semibold" disabled>
                            Stop
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h3 class="text-lg font-bold text-[#4A2C2A] mb-3">Speaking Checklist</h3>
                    <ul class="space-y-3 text-sm text-[#6B3D2E] leading-6">
                        <li>1. Start with one sentence that states the article's main idea.</li>
                        <li>2. Include at least one supporting detail and one logical connector.</li>
                        <li>3. End with your own judgment, application, or reflection.</li>
                    </ul>
                    <audio id="speaking-preview" controls class="w-full mt-5 hidden"></audio>
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

const startSpeaking = document.getElementById('start-speaking');
const stopSpeaking = document.getElementById('stop-speaking');
const speakingPreview = document.getElementById('speaking-preview');

startSpeaking.addEventListener('click', async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        speakingChunks = [];
        speakingRecorder = new MediaRecorder(stream);

        speakingRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                speakingChunks.push(event.data);
            }
        };

        speakingRecorder.onstop = () => {
            const blob = new Blob(speakingChunks, { type: 'audio/webm' });
            speakingPreview.src = URL.createObjectURL(blob);
            speakingPreview.classList.remove('hidden');
            stream.getTracks().forEach((track) => track.stop());
        };

        speakingRecorder.start();
        startSpeaking.disabled = true;
        stopSpeaking.disabled = false;
        startSpeaking.textContent = 'Recording...';
    } catch (error) {
        console.error(error);
    }
});

stopSpeaking.addEventListener('click', () => {
    if (speakingRecorder && speakingRecorder.state !== 'inactive') {
        speakingRecorder.stop();
        startSpeaking.disabled = false;
        stopSpeaking.disabled = true;
        startSpeaking.textContent = 'Start';
    }
});
</script>
@endpush
