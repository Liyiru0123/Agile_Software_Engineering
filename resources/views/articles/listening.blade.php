@extends('layouts.app')

@section('content')
<style>
    /* 1. 核心高亮样式：阅读时的视觉焦点 */
    .listening-segment.active {
        color: #800000; /* Mahogany */
        background-color: rgba(128, 0, 0, 0.05);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(128, 0, 0, 0.1);
        border-radius: 4px;
    }
    
    /* 2. 平滑滚动效果 */
    .scrolling-container { scroll-behavior: smooth; }
    
    /* 3. 进度条平滑过渡 */
    #progress-bar { transition: width 0.2s linear; }
</style>

<div class="min-h-screen bg-[#F3EFE0] py-12 pb-40">
    <div class="container mx-auto px-6 max-w-4xl">
        
        <!-- 顶部导航与文章信息 -->
        <div class="flex justify-between items-center mb-12">
            <a href="{{ route('articles.show', $article->article_id) }}" class="text-[10px] font-bold uppercase tracking-widest text-mahogany hover:text-black transition-all">
                <i class="fas fa-times mr-2"></i> Exit Training
            </a>
            <div class="text-center">
                <h1 class="text-3xl font-serif font-bold text-darkWood tracking-tight">{{ $article->title }}</h1>
                <p class="text-[9px] uppercase tracking-[0.4em] text-mahogany font-bold mt-2 opacity-60">Academic Listening Studio</p>
            </div>
            <div class="w-24 text-right border-l border-mahogany/20 pl-4">
                <div class="text-xl font-serif text-darkWood">{{ count($article->segments) }}</div>
                <div class="text-[8px] uppercase opacity-40 font-bold tracking-tighter">Segments</div>
            </div>
        </div>

        <!-- 主阅读区：自然段接续 -->
        <div class="bg-white p-16 rounded-[3rem] shadow-2xl border border-silkGold/10 relative overflow-hidden min-h-[600px]">
            <!-- 装饰性背景水印 -->
            <div class="absolute top-10 right-10 opacity-[0.03] text-9xl text-mahogany pointer-events-none">
                <i class="fas fa-headphones"></i>
            </div>

            <div id="article-content" class="relative z-10 space-y-12">
                @php
                    // 按段落对句子进行分组
                    $paragraphs = $article->segments->groupBy('paragraph_index');
                @endphp

                @forelse($paragraphs as $pIndex => $segments)
                    <p class="font-serif text-xl leading-[2.4] text-gray-800 text-justify transition-all duration-500">
                        @foreach($segments as $segment)
                            <span 
                                id="segment-{{ $segment->segment_id }}"
                                data-start="{{ $segment->start_time ?? 0 }}"
                                data-end="{{ $segment->end_time ?? 0 }}"
                                class="listening-segment cursor-pointer px-1 transition-all duration-300 hover:bg-silkGold/10"
                                onclick="seekTo({{ $segment->start_time ?? 0 }})"
                            >
                                {{ $segment->content_en }}
                            </span>
                        @endforeach
                    </p>
                @empty
                    <!-- 空状态提示 -->
                    <div class="py-32 text-center">
                        <i class="fas fa-feather-alt text-5xl text-silkGold/20 mb-6"></i>
                        <p class="text-gray-400 font-serif italic tracking-widest uppercase text-xs">Waiting for textual data synchronization...</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 底部浮动控制栏 -->
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 w-full max-w-2xl px-6 z-50">
            @if($article->audio_url)
                <!-- A. 正常播放器界面 -->
                <div class="bg-darkWood rounded-2xl shadow-2xl p-6 border border-white/10 flex items-center gap-8 backdrop-blur-md">
                    <button onclick="togglePlay()" id="play-btn" class="w-14 h-14 bg-mahogany rounded-xl flex items-center justify-center text-silkGold text-xl hover:scale-105 transition-all shadow-lg active:scale-95">
                        <i class="fas fa-play" id="play-icon"></i>
                    </button>

                    <div class="flex-grow">
                        <div class="flex justify-between text-[10px] text-silkGold/40 uppercase tracking-widest mb-2 font-bold font-sans">
                            <span id="current-time">00:00</span>
                            <span id="duration-time">--:--</span>
                        </div>
                        <div class="h-1.5 bg-white/10 rounded-full overflow-hidden cursor-pointer relative" onclick="seekByBar(event)" id="progress-container">
                            <div id="progress-bar" class="absolute top-0 left-0 h-full bg-mahogany w-0"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-silkGold/40">
                        <button onclick="changeSpeed()" id="speed-btn" class="text-[10px] font-bold border border-white/10 px-2 py-1 rounded hover:bg-white/5">1.0x</button>
                    </div>
                    
                    <audio id="main-audio" src="{{ asset($article->audio_url) }}"></audio>
                </div>
            @else
                <!-- B. 本界面提示：无数据状态 (Visual Only) -->
                <div class="bg-darkWood rounded-2xl shadow-2xl p-6 border border-white/10 flex items-center justify-between border-l-4 border-l-mahogany">
                    <div class="flex items-center gap-5">
                        <div class="w-12 h-12 bg-mahogany/20 rounded-xl flex items-center justify-center text-mahogany">
                            <i class="fas fa-microphone-slash text-lg"></i>
                        </div>
                        <div>
                            <div class="text-silkGold text-[11px] font-bold uppercase tracking-widest">Audio Stream Offline</div>
                            <div class="text-white/30 text-[9px] uppercase mt-1 tracking-tighter">Waiting for teammate's audio synchronization</div>
                        </div>
                    </div>
                    <div class="px-5 py-2 bg-white/5 rounded-lg text-[9px] text-silkGold/50 font-bold uppercase tracking-widest border border-white/10 italic">
                        Visual Only Mode
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    @if($article->audio_url)
    const audio = document.getElementById('main-audio');
    const playIcon = document.getElementById('play-icon');
    const segments = document.querySelectorAll('.listening-segment');
    const progressBar = document.getElementById('progress-bar');
    const speedBtn = document.getElementById('speed-btn');

    // 1. 播放/暂停
    function togglePlay() {
        if (audio.paused) { audio.play(); playIcon.className = 'fas fa-pause'; }
        else { audio.pause(); playIcon.className = 'fas fa-play'; }
    }

    // 2. 实时高亮同步
    audio.addEventListener('timeupdate', () => {
        const currentTime = audio.currentTime;
        
        // 更新进度条
        if (audio.duration) {
            progressBar.style.width = (currentTime / audio.duration * 100) + '%';
            document.getElementById('current-time').innerText = formatTime(currentTime);
        }

        // 寻找并高亮句子
        segments.forEach(segment => {
            const start = parseFloat(segment.dataset.start);
            const end = parseFloat(segment.dataset.end);

            if (currentTime >= start && currentTime < end) {
                segment.classList.add('active');
            } else {
                segment.classList.remove('active');
            }
        });
    });

    // 3. 点击跳转
    function seekTo(time) {
        audio.currentTime = time;
        audio.play();
        playIcon.className = 'fas fa-pause';
    }

    // 4. 进度条点击跳转
    function seekByBar(e) {
        const container = document.getElementById('progress-container');
        const rect = container.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        audio.currentTime = pos * audio.duration;
    }

    // 5. 语速调节
    let currentSpeed = 1.0;
    function changeSpeed() {
        const speeds = [1.0, 1.25, 1.5, 0.75];
        currentSpeed = speeds[(speeds.indexOf(currentSpeed) + 1) % speeds.length];
        audio.playbackRate = currentSpeed;
        speedBtn.innerText = currentSpeed + 'x';
    }

    function formatTime(s) {
        return Math.floor(s/60).toString().padStart(2,'0') + ':' + Math.floor(s%60).toString().padStart(2,'0');
    }

    audio.onloadedmetadata = () => {
        document.getElementById('duration-time').innerText = formatTime(audio.duration);
    };
    @endif
</script>
@endsection