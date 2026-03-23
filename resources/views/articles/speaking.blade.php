@extends('layouts.app')

@section('content')
<style>
    /* 1. 录音波形动画 */
    .wave-bar { display: inline-block; width: 3px; height: 8px; background-color: #ef4444; margin: 0 1px; border-radius: 2px; }
    .recording .wave-bar { animation: wave-jump 0.6s infinite ease-in-out; }
    @keyframes wave-jump { 0%, 100% { height: 4px; opacity: 0.5; } 50% { height: 14px; opacity: 1; } }
    
    /* 2. 胶囊状态过渡 */
    .recording-capsule { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .btn-main { transition: all 0.3s ease; }
    
    /* 3. 进度条平滑过渡 */
    #overall-progress { transition: width 0.6s cubic-bezier(0.65, 0, 0.35, 1); }
</style>

<div class="container mx-auto py-6 max-w-5xl px-4">
    <!-- 顶部标题栏 -->
    <div class="bg-darkWood text-silkGold p-6 rounded-t-xl shadow-lg border-b-2 border-mahogany">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-serif font-bold tracking-tight">{{ $article->title }}</h1>
                <p class="text-xs opacity-60 mt-1 italic font-serif uppercase tracking-widest">Academic Oral Studio</p>
            </div>
            <div class="text-right border-l border-silkGold/20 pl-4 shrink-0 font-serif">
                <div class="text-2xl leading-none">{{ count($article->segments) }}</div>
                <div class="text-[10px] opacity-50 uppercase tracking-widest">Units</div>
            </div>
        </div>
        <audio id="main-audio" src="{{ $article->audio_url ?? '#' }}"></audio>
    </div>

    <!-- 练习区 -->
    <div class="bg-paper p-4 md:p-6 shadow-inner border-x-2 border-silkGold/30 min-h-[500px]">
        @foreach($article->segments as $index => $segment)
        <div class="mb-4 p-5 bg-white/40 rounded-lg shadow-sm flex flex-col md:flex-row md:items-center gap-6 border-l-4 border-silkGold hover:border-mahogany transition-all" id="card-{{ $index }}">
            
            <div class="flex-grow">
                <p class="text-xl leading-relaxed font-serif text-[#2C1810]">
                    {{ $segment->content_en }}
                </p>
            </div>

            <div class="flex items-center gap-4 shrink-0 relative">
                <!-- 1. 原音播放 -->
                <button onclick="playNative({{ $segment->start_time ?? 0 }}, {{ $segment->end_time ?? 10 }})" 
                        class="w-12 h-12 flex items-center justify-center bg-mahogany text-silkGold rounded-full hover:scale-105 transition active:scale-95 shadow-md">
                    <i class="fas fa-play text-xs ml-1"></i>
                </button>

                <!-- 2. 录音胶囊 -->
                <div id="capsule-{{ $index }}" class="recording-capsule flex items-center bg-white/90 rounded-full border border-mahogany/20 px-3 py-1.5 shadow-sm min-w-[160px] justify-between">
                    
                    <div class="flex items-center ml-1 min-w-[70px]">
                        <span id="timer-{{ $index }}" class="text-xs font-mono font-bold text-gray-400">00:00</span>
                        <div id="wave-{{ $index }}" class="hidden items-center ml-2 recording">
                            <div class="wave-bar"></div><div class="wave-bar"></div><div class="wave-bar"></div>
                        </div>
                    </div>

                    <button id="btn-{{ $index }}" onclick="handleAction({{ $index }})" 
                            class="btn-main w-9 h-9 flex items-center justify-center rounded-full bg-mahogany text-silkGold hover:opacity-90 shadow-sm relative">
                        <i id="icon-{{ $index }}" class="fas fa-microphone text-xs"></i>
                    </button>
                </div>

                <!-- 3. 重置叉号 (独立出胶囊，避免影响胶囊宽度) -->
                <button onclick="resetRecord({{ $index }})" 
                        id="reset-{{ $index }}"
                        class="hidden absolute -top-1 -right-1 w-5 h-5 bg-white border border-red-100 text-red-400 rounded-full items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm z-10"
                        title="Reset recording">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 底部状态栏 -->
    <div class="bg-silkGold p-5 rounded-b-xl flex justify-between items-center border-t-2 border-silkGold/50">
        <div class="flex items-center gap-6">
            <a href="/articles" class="text-xs font-bold font-serif text-mahogany opacity-60 flex items-center gap-2 uppercase tracking-widest hover:underline">
                <i class="fas fa-chevron-left"></i> Library
            </a>
            <div class="flex items-center gap-3">
                <span class="text-[10px] uppercase font-bold text-mahogany opacity-40">Progress</span>
                <div class="w-64 h-2 bg-white/40 rounded-full overflow-hidden border border-mahogany/5 shadow-inner">
                    <div id="overall-progress" class="bg-mahogany h-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
        <button onclick="submitFinal()" class="bg-darkWood text-silkGold px-10 py-3 rounded-full font-serif text-sm font-bold shadow-xl hover:bg-black transition-all transform hover:-translate-y-0.5">
            Complete Session <i class="fas fa-check-double ml-2 text-xs"></i>
        </button>
    </div>
</div>

<script>
    let mediaRecorders = {};
    let audioBlobs = {};
    let timerIntervals = {};
    let finishedSet = new Set();
    const totalCount = {{ count($article->segments) }};

    async function handleAction(index) {
        const btn = document.getElementById(`btn-${index}`);
        const icon = document.getElementById(`icon-${index}`);
        const wave = document.getElementById(`wave-${index}`);
        const timerEl = document.getElementById(`timer-${index}`);
        const capsule = document.getElementById(`capsule-${index}`);
        const resetBtn = document.getElementById(`reset-${index}`);

        if (icon.classList.contains('fa-microphone')) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const recorder = new MediaRecorder(stream);
                mediaRecorders[index] = recorder;
                const chunks = [];
                recorder.ondataavailable = (e) => chunks.push(e.data);
                recorder.onstop = () => {
                    audioBlobs[index] = URL.createObjectURL(new Blob(chunks, { type: 'audio/ogg; codecs=opus' }));
                };
                recorder.start();

                icon.className = 'fas fa-stop text-xs';
                btn.className = 'btn-main w-9 h-9 flex items-center justify-center rounded-full bg-red-600 text-white animate-pulse shadow-md';
                wave.classList.remove('hidden');
                timerEl.classList.replace('text-gray-400', 'text-red-600');
                capsule.classList.add('border-red-200');
            } catch(e) { alert('Mic access required'); }
        } 
        else if (icon.classList.contains('fa-stop')) {
            mediaRecorders[index].stop();
            mediaRecorders[index].stream.getTracks().forEach(t => t.stop());
            icon.className = 'fas fa-play text-[10px] ml-0.5'; 
            btn.className = 'btn-main w-9 h-9 flex items-center justify-center rounded-full bg-green-600 text-white shadow-lg hover:scale-110';
            wave.classList.add('hidden');
            timerEl.classList.replace('text-red-600', 'text-green-600');
            capsule.classList.replace('border-red-200', 'border-green-300');
            capsule.classList.add('bg-green-50/50');
            resetBtn.classList.replace('hidden', 'flex'); // 核心：显示重置叉号
            
            finishedSet.add(index);
            updateProgressBar();
        }
        else if (icon.classList.contains('fa-play')) {
            if (audioBlobs[index]) {
                const p = new Audio(audioBlobs[index]);
                p.play();
                btn.classList.add('ring-4', 'ring-green-100');
                p.onended = () => btn.classList.remove('ring-4', 'ring-green-100');
            }
        }
    }

    // --- 核心修复：重置录音并更新进度条 ---
    function resetRecord(index) {
        // 1. 释放录音资源
        if(audioBlobs[index]) URL.revokeObjectURL(audioBlobs[index]);
        delete audioBlobs[index];
        
        // 2. 状态逻辑：从已完成集合中踢出
        finishedSet.delete(index);
        updateProgressBar(); // 重新计算进度条

        // 3. UI 还原
        const capsule = document.getElementById(`capsule-${index}`);
        const btn = document.getElementById(`btn-${index}`);
        const icon = document.getElementById(`icon-${index}`);
        const timerEl = document.getElementById(`timer-${index}`);
        const resetBtn = document.getElementById(`reset-${index}`);

        icon.className = 'fas fa-microphone text-xs';
        btn.className = 'btn-main w-9 h-9 flex items-center justify-center rounded-full bg-mahogany text-silkGold';
        timerEl.innerText = '00:00';
        timerEl.classList.remove('text-green-600', 'text-red-600');
        timerEl.classList.add('text-gray-400');
        
        capsule.className = 'recording-capsule flex items-center bg-white/90 rounded-full border border-mahogany/20 px-3 py-1.5 shadow-sm min-w-[160px] justify-between';
        resetBtn.classList.replace('flex', 'hidden');
    }

    function updateProgressBar() {
        const progress = (finishedSet.size / totalCount) * 100;
        document.getElementById('overall-progress').style.width = `${progress}%`;
    }

    function playNative(s, e) {
        const audio = document.getElementById('main-audio');
        if (!audio.src || audio.src.endsWith('#')) return alert('Audio missing');
        audio.currentTime = s;
        audio.play();
        setTimeout(() => audio.pause(), (e - s) * 1000);
    }

    function submitFinal() {
        if(finishedSet.size < totalCount) {
            if(!confirm('You haven\'t finished all. Submit anyway?')) return;
        }
        alert('Congratulations! Practice session completed.');
    }
</script>
@endsection