@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F3EFE0] py-16">
    <div class="container mx-auto px-6 max-w-7xl">
        
        <!-- 1. 头部：图书馆概览 -->
        <div class="mb-16 border-b-2 border-silkGold pb-10 flex flex-col md:flex-row justify-between items-end gap-6">
            <div>
                <h1 class="text-5xl font-serif font-bold text-darkWood tracking-tight">Academic Library</h1>
                <p class="text-[10px] uppercase tracking-[0.4em] text-mahogany font-bold mt-4 opacity-60">
                    Total Resources: {{ $articles->count() }} @choice('Article|Articles', $articles->count())
                </p>
            </div>
            <div class="text-[10px] text-gray-400 font-serif italic uppercase tracking-widest">
                @php $authorCount = $articles->unique('author')->count(); @endphp
                @if($authorCount === 1)
                    Primary Author: {{ $articles->first()->author }}
                @else
                    Collaboratively Authored by {{ $authorCount }} Experts
                @endif
            </div>
        </div>

        <!-- 2. 文章网格 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @foreach($articles as $article)
            <div class="bg-white rounded-2xl shadow-sm border border-silkGold/20 hover:border-mahogany hover:shadow-2xl transition-all duration-500 group flex flex-col h-full overflow-hidden">
                <div class="p-8 flex-grow">
                    <div class="flex justify-between items-center mb-6">
                        <span class="px-3 py-1 bg-paper text-mahogany text-[9px] font-bold uppercase tracking-widest rounded-full border border-mahogany/10">
                            {{ $article->subject }}
                        </span>
                        <span class="text-[10px] font-serif italic text-gray-400">Level: {{ $article->level }}</span>
                    </div>

                    <h2 class="text-2xl font-serif font-bold text-darkWood group-hover:text-mahogany transition-colors mb-6 leading-tight">
                        {{ $article->title }}
                    </h2>

                    <div class="space-y-3 border-t border-dashed border-silkGold/20 pt-6">
                        <div class="flex items-center gap-3 text-xs text-gray-500 font-serif italic">
                            <i class="fas fa-feather-alt text-silkGold w-4"></i>
                            <span>{{ number_format($article->word_count) }} Academic Words</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-500 font-serif italic">
                            <i class="fas fa-microphone-alt text-silkGold w-4"></i>
                            <span>Accent: {{ $article->accent }}</span>
                        </div>
                    </div>
                </div>

                <!-- 行动按钮：直接进入阅读 -->
                <div class="p-6 pt-0 mt-auto">
                    <a href="{{ route('articles.show', $article->article_id) }}" 
                       class="block w-full text-center py-4 bg-darkWood text-silkGold text-[10px] font-bold uppercase tracking-[0.2em] rounded-xl hover:bg-mahogany transition-all shadow-lg">
                        Start Reading <i class="fas fa-book-open ml-2 text-[8px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>
@endsection