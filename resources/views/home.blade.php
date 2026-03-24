@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F3EFE0]">
    
    <!-- 1. 英雄区：直接展示推荐文章标题 -->
    @if($featuredArticle)
    <div class="py-24 bg-darkWood border-b-4 border-mahogany relative overflow-hidden">
        <!-- 装饰性纹理 -->
        <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');"></div>
        
        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="px-2 py-0.5 bg-mahogany text-silkGold text-[10px] rounded uppercase tracking-widest border border-silkGold/30">
                    Latest Release
                </span>
            </div>
            <h1 class="text-5xl md:text-6xl font-serif font-bold text-silkGold tracking-tight mb-6">
                {{ $featuredArticle->title }}
            </h1>
            <p class="text-silkGold opacity-60 text-lg font-serif italic max-w-2xl mx-auto mb-10">
                "{{ $featuredArticle->subject }} · {{ $featuredArticle->level }} · By {{ $featuredArticle->author }}"
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('articles.speaking', $featuredArticle->article_id) }}" 
                   class="px-12 py-4 bg-mahogany text-silkGold rounded-full font-serif font-bold shadow-2xl hover:bg-black transition-all transform hover:-translate-y-1">
                    Start Speaking Practice <i class="fas fa-microphone-alt ml-2 text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. 数据看板：反映真实数据库规模 -->
    <div class="container mx-auto px-6 -mt-10 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-xl shadow-lg border-b-4 border-silkGold flex items-center gap-6">
                <div class="text-3xl font-bold text-darkWood">{{ $stats['count'] }}</div>
                <div class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Articles<br>Available</div>
            </div>
            <div class="bg-white p-8 rounded-xl shadow-lg border-b-4 border-silkGold flex items-center gap-6">
                <div class="text-3xl font-bold text-darkWood">{{ number_format($stats['words']) }}</div>
                <div class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Academic<br>Word Count</div>
            </div>
            <div class="bg-white p-8 rounded-xl shadow-lg border-b-4 border-silkGold flex items-center gap-6">
                <div class="text-3xl font-bold text-darkWood">{{ $stats['author_count'] }}</div>
                <div class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Expert<br>Authors</div>
            </div>
        </div>
    </div>

    <!-- 3. 文章内容预览：直接从数据库提取 -->
    <div class="container mx-auto px-6 py-20">
        <div class="max-w-4xl mx-auto bg-white/60 p-12 rounded-2xl border border-silkGold/30 shadow-sm relative">
            <!-- 装饰性引号 -->
            <i class="fas fa-quote-left absolute top-8 left-8 text-silkGold/20 text-4xl"></i>
            
            <div class="relative z-10">
                <h2 class="text-xl font-serif font-bold text-darkWood mb-6 uppercase tracking-widest opacity-40 text-center">Preview Content</h2>
                <div class="text-xl leading-loose font-serif text-gray-700 italic text-center px-10">
                    {{ Str::limit($featuredArticle->content, 220) }}
                </div>
                <div class="mt-10 text-center">
                    <a href="{{ route('articles.show', $featuredArticle->article_id) }}" class="text-mahogany font-bold text-sm hover:underline uppercase tracking-widest">
                        Read Full Article <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 4. 学科分类：从数据库 subject 字段动态渲染 -->
    @if($categories->isNotEmpty())
    <div class="bg-paper py-20 border-t border-silkGold/10">
        <div class="container mx-auto px-6">
            <h3 class="text-center text-[10px] font-bold uppercase tracking-[0.4em] text-mahogany opacity-40 mb-12">
                Available Disciplines
            </h3>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($categories as $category)
                <div class="px-8 py-3 bg-white rounded-lg shadow-sm border border-transparent hover:border-mahogany hover:shadow-md transition-all cursor-pointer group">
                    <span class="font-serif font-bold text-darkWood tracking-tight group-hover:text-mahogany">
                        {{ $category }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>
@endsection