@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F3EFE0] py-12">
    <div class="container mx-auto px-6 max-w-6xl">
        
        <!-- 1. 返回导航 -->
        <a href="{{ route('articles.index') }}" class="inline-flex items-center text-[10px] font-bold uppercase tracking-widest text-mahogany hover:text-black mb-12 transition-colors group">
            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i> Back to Library
        </a>

        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- A. 左侧：沉浸式阅读区 (Main Content) -->
            <div class="lg:w-2/3">
                <div class="bg-white p-12 rounded-[2.5rem] shadow-xl border border-silkGold/10 relative overflow-hidden">
                    <!-- 背景装饰水印 -->
                    <div class="absolute top-8 right-8 text-silkGold/10 text-6xl opacity-20"><i class="fas fa-quote-right"></i></div>
                    
                    <header class="mb-12 border-b border-silkGold/10 pb-8 relative z-10">
                        <div class="text-[10px] text-mahogany font-bold uppercase tracking-[0.3em] mb-3 opacity-60">Full Text Selection</div>
                        <h1 class="text-4xl font-serif font-bold text-darkWood mb-4 leading-tight">{{ $article->title }}</h1>
                        <div class="flex flex-wrap gap-4 text-xs text-gray-400 font-serif italic">
                            <span>By {{ $article->author }}</span>
                            <span>•</span>
                            <span>{{ $article->subject }}</span>
                            <span>•</span>
                            <span>{{ number_format($article->word_count) }} Words</span>
                        </div>
                    </header>

                    <!-- 文章正文：保留换行并优化排版 -->
                    <div class="font-serif text-lg text-gray-700 leading-loose space-y-8 relative z-10">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </div>

            <!-- B. 右侧：训练模式切换 (Training Sidebar) -->
            <div class="lg:w-1/3 space-y-8">
                <div class="sticky top-12">
                    <h2 class="text-[10px] font-bold uppercase tracking-[0.4em] text-mahogany opacity-40 mb-8 px-2">Choose Training Mode</h2>
                    
                    <!-- 模式 1：口语练习 (Speaking Practice) -->
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-silkGold/10 group mb-8 transition-all hover:-translate-y-1">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="w-14 h-14 bg-mahogany rounded-2xl flex items-center justify-center text-silkGold shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-microphone-alt text-xl"></i>
                            </div>
                            <h3 class="text-xl font-serif font-bold text-darkWood">Speaking Practice</h3>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed mb-8 font-serif italic">
                            Record your voice, analyze sentences, and master the {{ $article->accent }} accent through repetitive practice.
                        </p>
                        <a href="{{ route('articles.speaking', $article->article_id) }}" class="block w-full text-center py-4 bg-mahogany text-silkGold text-[10px] font-bold uppercase tracking-[0.2em] rounded-xl hover:bg-black transition-all shadow-md">
                            Start Speaking Now
                        </a>
                    </div>

                    <!-- 模式 2：听力训练 (Listening Training) - 强制开启版 -->
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-silkGold/10 group mb-8 transition-all hover:-translate-y-1">
                        <div class="flex items-center gap-5 mb-6">
                            <div class="w-14 h-14 bg-mahogany rounded-2xl flex items-center justify-center text-silkGold shadow-lg group-hover:scale-110 transition-transform">
                                <i class="fas fa-headphones-alt text-xl"></i>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-xl font-serif font-bold text-darkWood">Listening Training</h3>
                                @if(!$article->audio_url)
                                    <span class="text-[8px] uppercase font-bold text-mahogany tracking-widest block mt-1 opacity-40">Preview Mode</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed mb-8 font-serif italic">
                            Focus on phonetics and rhythm. Follow the original source to improve your comprehension and speed in a seamless reading flow.
                        </p>

                        <!-- 不论有没有音频，按钮永远是激活状态，允许进入 preview -->
                        <a href="{{ route('articles.listening', $article->article_id) }}" class="block w-full text-center py-4 bg-mahogany text-silkGold text-[10px] font-bold uppercase tracking-[0.2em] rounded-xl hover:bg-black transition-all shadow-md">
                            Enter Listening Mode
                        </a>
                    </div>

                    <!-- 底部规格卡片 (Academic Specs) -->
                    <div class="p-8 bg-darkWood rounded-[2.5rem] text-silkGold shadow-2xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent"></div>
                        <div class="relative z-10">
                            <div class="text-[9px] uppercase font-bold tracking-[0.3em] opacity-40 mb-6 italic">Academic Specs</div>
                            <div class="flex justify-between items-end">
                                <div>
                                    <div class="text-3xl font-bold font-serif leading-none">{{ $article->accent }}</div>
                                    <div class="text-[8px] uppercase opacity-40 font-bold mt-2 tracking-widest">Target Accent</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold font-serif leading-none text-mahogany/80">{{ $article->level }}</div>
                                    <div class="text-[8px] uppercase opacity-40 font-bold mt-2 tracking-widest">Proficiency</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection