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

<div class="container mx-auto py-10 max-w-5xl px-4">
    <!-- 顶部标题栏：使用你 app.blade.php 定义的 darkWood 和 silkGold -->
    <div class="bg-darkWood text-silkGold p-8 rounded-t-[2rem] shadow-2xl border-b-4 border-mahogany">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-serif font-bold tracking-tight">{{ $article->title }}</h1>
                <p class="text-[10px] opacity-60 mt-2 italic font-serif uppercase tracking-[0.3em]">Academic Oral Studio</p>
            </div>
            <div class="text-right border-l border-silkGold/20 pl-6 shrink-0 font-serif">
                <div class="text-3xl leading-none font-bold">{{ count($article->segments) }}</div>
                <div class="text-[10px] opacity-50 uppercase tracking-widest mt-1">Total Units</div>
            </div>
        </div>
        <audio id="main-audio" src="{{ $article->audio_url ?? '#' }}"></audio>
    </div>

    <!-- 练习区：使用你定义的 paper 背景色 -->
    <div class="bg-paper p-6 md:p-10 shadow-inner border-x-2 border-silkGold/20 min-h-[500px]">
        @foreach($article->segments as $index => $segment)
        <div class="mb-6 p-6 bg-white rounded-2xl shadow-md flex flex-col md:flex-row md:items-center gap-8 border-l-8 border-silkGold hover:border-mahogany transition-all group" id="card-{{ $index }}">
            
            <div class="flex-grow">
                <p class="text-xl leading-relaxed font-serif text-darkWood/90">
                    {{ $segment->content_en }}
                </p>
            </div>

            <div class="flex items-center gap-4 shrink-0 relative">
                <!-- 1. 原音播放 -->
                <button onclick="playNative({{ $segment->start_time ?? 0 }}, {{ $segment->end_time ?? 10 }})" 
                        class="w-14 h-14 flex items-center justify-center bg-mahogany text-silkGold rounded-full hover:scale-110 transition active:scale-95 shadow-lg">
                    <i class="fas fa-play text-sm ml-1"></i>
                </button>

                <!-- 2. 录音胶囊 -->
                <div id="capsule-{{ $index }}" class="recording-capsule flex items-center bg-[#F3EFE0] rounded-full border-2 border-silkGold/30 px-4 py-2 shadow-sm min-w-[180px] justify-between">
                    
                    <div class="flex items-center ml-1 min-w-[70px]">
                        <span id="timer-{{ $index }}" class="text-sm font-mono font-bold text-gray-400 tracking-tighter">00:00</span>
                        <div id="wave-{{ $index }}" class="hidden items-center ml-3 recording">
                            <div class="wave-bar"></div><div class="wave-bar"></div><div class="wave-bar"></div>
                        </div>
                    </div>

                    <button id="btn-{{ $index }}" onclick="handleAction({{ $index }})" 
                            class="btn-main w-10 h-10 flex items-center justify-center rounded-full bg-mahogany text-silkGold hover:opacity-90 shadow-md relative group-hover:rotate-12 transition-transform">
                        <i id="icon-{{ $index }}" class="fas fa-microphone text-sm"></i>
                    </button>
                </div>

                <!-- 3. 重置叉号 -->
                <button onclick="resetRecord({{ $index }})" 
                        id="reset-{{ $index }}"
                        class="hidden absolute -top-2 -right-2 w-6 h-6 bg-white border-2 border-red-100 text-red-500 rounded-full items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-md z-10"
                        title="Reset recording">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 底部状态栏 -->
    <div class="bg-silkGold/30 p-8 rounded-b-[2rem] flex justify-between items-center border-t-4 border-silkGold/50 backdrop-blur-sm">
        <div class="flex items-center gap-10">
            <a href="{{ route('articles.index') }}" class="text-[10px] font-bold font-serif text-mahogany flex items-center gap-3 uppercase tracking-[0.2em] hover:text-black transition-colors">
                <i class="fas fa-chevron-left"></i> Exit to Library
            </a>
            <div class="flex items-center gap-5">
                <span class="text-[10px] uppercase font-bold text-mahogany opacity-60 tracking-widest">Training Progress</span>
                <div class="w-72 h-3 bg-white/60 rounded-full overflow-hidden border-2 border-mahogany/10 shadow-inner">
                    <div id="overall-progress" class="bg-mahogany h-full" style="width: 0%"></div>
                </div>
            </div>
        </div>
        <button onclick="submitFinal()" class="bg-darkWood text-silkGold px-12 py-4 rounded-full font-serif text-sm font-bold shadow-2xl hover:bg-black transition-all transform hover:-translate-y-1 active:translate-y-0">
            Complete Session <i class="fas fa-check-double ml-2 text-xs opacity-70"></i>
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
                
                // --- 🚀 修复计时器逻辑 ---
                recorder.start();
                let seconds = 0;
                timerIntervals[index] = setInterval(() => {
                    seconds++;
                    const min = Math.floor(seconds / 60).toString().padStart(2, '0');
                    const sec = (seconds % 60).toString().padStart(2, '0');
                    timerEl.innerText = `${min}:${sec}`;
                }, 1000);

                icon.className = 'fas fa-stop text-sm';
                btn.className = 'btn-main w-10 h-10 flex items-center justify-center rounded-full bg-red-600 text-white animate-pulse shadow-md';
                wave.classList.remove('hidden');
                timerEl.classList.replace('text-gray-400', 'text-red-600');
                capsule.classList.replace('border-silkGold/30', 'border-red-300');
            } catch(e) { alert('Mic access required'); }
        } 
        else if (icon.classList.contains('fa-stop')) {
            // --- 🚀 停止并清除计时器 ---
            mediaRecorders[index].stop();
            clearInterval(timerIntervals[index]);

            mediaRecorders[index].stream.getTracks().forEach(t => t.stop());
            icon.className = 'fas fa-play text-xs ml-0.5'; 
            btn.className = 'btn-main w-10 h-10 flex items-center justify-center rounded-full bg-green-600 text-white shadow-lg hover:scale-110';
            wave.classList.add('hidden');
            timerEl.classList.replace('text-red-600', 'text-green-600');
            capsule.classList.replace('border-red-300', 'border-green-400');
            capsule.classList.add('bg-green-50');
            resetBtn.classList.replace('hidden', 'flex'); 
            
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

    function resetRecord(index) {
        clearInterval(timerIntervals[index]);

        if(audioBlobs[index]) URL.revokeObjectURL(audioBlobs[index]);
        delete audioBlobs[index];
        
        finishedSet.delete(index);
        updateProgressBar();

        const capsule = document.getElementById(`capsule-${index}`);
        const btn = document.getElementById(`btn-${index}`);
        const icon = document.getElementById(`icon-${index}`);
        const timerEl = document.getElementById(`timer-${index}`);
        const resetBtn = document.getElementById(`reset-${index}`);

        icon.className = 'fas fa-microphone text-sm';
        btn.className = 'btn-main w-10 h-10 flex items-center justify-center rounded-full bg-mahogany text-silkGold';
        timerEl.innerText = '00:00';
        timerEl.classList.remove('text-green-600', 'text-red-600');
        timerEl.classList.add('text-gray-400');
        
        capsule.className = 'recording-capsule flex items-center bg-[#F3EFE0] rounded-full border-2 border-silkGold/30 px-4 py-2 shadow-sm min-w-[180px] justify-between';
        resetBtn.classList.replace('flex', 'hidden');
    }

    function updateProgressBar() {
        const progress = (finishedSet.size / totalCount) * 100;
        document.getElementById('overall-progress').style.width = `${progress}%`;
    }

    function playNative(s, e) {
        const audio = document.getElementById('main-audio');
        if (!audio.src || audio.src.endsWith('#')) return alert('Native audio source is being prepared...');
        audio.currentTime = s;
        audio.play();
        setTimeout(() => audio.pause(), (e - s) * 1000);
    }

    function submitFinal() {
        if(finishedSet.size < totalCount) {
            if(!confirm('Your academic session is incomplete. Submit anyway?')) return;
        }
        alert('Academic Session Completed Successfully!');
    }
</script>
@endsection