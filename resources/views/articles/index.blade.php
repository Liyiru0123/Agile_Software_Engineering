@extends('layouts.app')

@section('title', 'Learning - EAPlus')

@push('styles')
<style>
    .articles-chip-row > span > span:first-child {
        color: transparent;
        font-size: 0;
        position: relative;
    }

    .articles-chip-row > span > span:first-child::after {
        content: '';
        display: block;
        width: 1rem;
        height: 1rem;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }

    .articles-chip-row > span:nth-child(1) > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M4 8H20' stroke='%23C95F43' stroke-width='2.2' stroke-linecap='round'/%3E%3Cpath d='M7 12H17' stroke='%23C95F43' stroke-width='2.2' stroke-linecap='round'/%3E%3Cpath d='M10 16H14' stroke='%23C95F43' stroke-width='2.2' stroke-linecap='round'/%3E%3C/svg%3E");
    }

    .articles-chip-row > span:nth-child(2) > span:first-child::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none'%3E%3Ccircle cx='12' cy='12' r='7' stroke='%23C9A961' stroke-width='2.2'/%3E%3Cpath d='M12 9V12L14 14' stroke='%23C9A961' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    }
</style>
@endpush

@section('content')
<div class="max-w-[1440px] mx-auto px-5 md:px-8 xl:px-12">
    @php
        $skill = $skill ?? request('skill');
        $currentSkill = in_array($skill, ['listening', 'speaking'], true) ? $skill : 'listening';
        $pageHeading = $currentSkill === 'speaking' ? 'Speaking' : 'Listening Hub';
        $pageSubheading = $currentSkill === 'speaking'
            ? 'Browse speaking-ready articles and practice prompts'
            : 'Browse listening-ready articles and transcript tasks';
    @endphp

    <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-[#4A2C2A]">{{ $pageHeading }}</h1>
    </div>

    <div class="space-y-6">

        <div class="bg-white border-2 border-[#6B3D2E] rounded-lg p-4 shadow-md">
                <form method="GET" action="{{ route('articles.index') }}" class="flex flex-wrap items-center gap-4">
                    <input type="hidden" name="skill" value="{{ $currentSkill ?? 'listening' }}">

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            type="checkbox"
                            name="favorites"
                            value="1"
                            {{ request('favorites') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-[#6B3D2E] border-2 border-[#6B3D2E] rounded focus:ring-[#6B3D2E]"
                        >
                        <span class="text-[#4A2C2A] font-medium">My Favorites</span>
                    </label>

                    <div class="flex-1 min-w-[200px]">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search articles..."
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 bg-[#FAF0E6] border-2 border-[#6B3D2E] rounded-lg text-[#4A2C2A] placeholder-[#6B3D2E]/50 focus:outline-none focus:border-[#8B4D3A]"
                        >
                    </div>

                    <select name="difficulty" class="px-4 py-2 bg-[#FAF0E6] border-2 border-[#6B3D2E] rounded-lg text-[#4A2C2A] focus:outline-none focus:border-[#8B4D3A]">
                        <option value="">All Difficulties</option>
                        <option value="1" {{ request('difficulty') == 1 ? 'selected' : '' }}>Level 1</option>
                        <option value="2" {{ request('difficulty') == 2 ? 'selected' : '' }}>Level 2</option>
                        <option value="3" {{ request('difficulty') == 3 ? 'selected' : '' }}>Level 3</option>
                    </select>

                    <select name="progress" class="px-4 py-2 bg-[#FAF0E6] border-2 border-[#6B3D2E] rounded-lg text-[#4A2C2A] focus:outline-none focus:border-[#8B4D3A]">
                        <option value="">All Progress</option>
                        <option value="not_started" {{ request('progress') == 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ request('progress') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('progress') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <select name="sort" class="px-4 py-2 bg-[#FAF0E6] border-2 border-[#6B3D2E] rounded-lg text-[#4A2C2A] focus:outline-none focus:border-[#8B4D3A]">
                        <option value="newest" {{ request('sort') == 'newest' || !request('sort') ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                        <option value="words" {{ request('sort') == 'words' ? 'selected' : '' }}>Word Count</option>
                    </select>

                    <button type="submit" class="px-6 py-2 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg font-medium hover:bg-[#8B4D3A] transition">
                        Search
                    </button>

                    @if(request('search') || request('difficulty') || request('favorites') || request('progress'))
                        <a href="{{ route('articles.index', ['skill' => $currentSkill ?? 'listening']) }}" class="px-4 py-2 text-[#6B3D2E] hover:text-[#8B4D3A] transition">
                            Clear
                        </a>
                    @endif
                </form>
        </div>

        @php
            $effectiveSkill = $currentSkill ?? 'listening';
            $skillRouteMap = [
                'listening' => 'articles.listening',
                'speaking' => 'articles.speaking',
            ];
            $skillCtaLabel = [
                'listening' => 'Start Listening Hub',
                'speaking' => 'Start Speaking',
            ];
            $targetRouteName = $skillRouteMap[$effectiveSkill] ?? 'articles.show';
            $ctaText = $skillCtaLabel[$effectiveSkill] ?? 'Open Article';
        @endphp

        @if($articles->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
                @php
                    $fallbackCoverImage = 'https://picsum.photos/seed/eaplus-article-'.$article->id.'/1200/800';
                    $coverImage = filled($article->cover_image_url) ? $article->cover_image_url : $fallbackCoverImage;
                @endphp
                <article class="bg-white border-2 border-[#6B3D2E] rounded-lg p-6 shadow-md hover:shadow-xl transition-all duration-200 hover:-translate-y-1 group flex flex-col h-full">
                    <div class="mb-4 overflow-hidden rounded-2xl">
                        <img
                            src="{{ $coverImage }}"
                            alt="Library study scene"
                            class="h-40 w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                            loading="lazy"
                            referrerpolicy="no-referrer"
                        >
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-[#6B3D2E]/10 text-[#6B3D2E] text-xs rounded-full font-bold border border-[#6B3D2E]/30 whitespace-nowrap">
                                Level {{ $article->difficulty }}
                            </span>
                            @if(!empty($completedArticleIds) && in_array($article->id, $completedArticleIds))
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-bold border border-emerald-300 whitespace-nowrap">
                                    Completed
                                </span>
                            @elseif(!empty($inProgressArticleIds) && in_array($article->id, $inProgressArticleIds))
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-bold border border-amber-300 whitespace-nowrap">
                                    In Progress
                                </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-500 flex items-center gap-1 whitespace-nowrap ml-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            {{ number_format($article->word_count) }}
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-[#4A2C2A] mb-3 group-hover:text-[#6B3D2E] transition-colors line-clamp-2 min-h-[3.5rem]">
                        {{ $article->title }}
                    </h3>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-3 leading-relaxed flex-grow">
                        {{ Str::limit(strip_tags($article->content), 120) }}
                    </p>

                    @if($article->audio_url)
                        <div class="flex items-center gap-2 mb-4 text-xs text-[#6B3D2E]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 8.464a5 5 0 000 7.072m2.828-9.9a9 9 0 000 12.728M9 12h.01M12 12h.01M15 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Audio available</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-4 border-t border-[#6B3D2E]/20 mt-auto">
                        <a href="{{ route($targetRouteName, $article) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg font-medium hover:bg-[#8B4D3A] transition text-sm group-hover:shadow-md">
                            {{ $ctaText }}
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>

                        <button
                            type="button"
                            onclick="toggleFavorite({{ $article->id }}, this)"
                            class="favorite-btn p-2 transition rounded-lg hover:bg-gray-100 {{ $favoritedArticleIds && in_array($article->id, $favoritedArticleIds) ? 'text-red-500' : 'text-gray-400' }}"
                            title="{{ $favoritedArticleIds && in_array($article->id, $favoritedArticleIds) ? 'Remove from favorites' : 'Add to favorites' }}"
                        >
                            <svg class="w-5 h-5" fill="{{ $favoritedArticleIds && in_array($article->id, $favoritedArticleIds) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                </article>
            @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                {{ $articles->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-20">
            <div class="w-24 h-24 bg-[#6B3D2E]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-[#6B3D2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#4A2C2A] mb-2">
                @if(request('favorites'))
                    No favorite articles yet
                @else
                    No articles found
                @endif
            </h3>
            <p class="text-gray-500 mb-6">
                @if(request('favorites'))
                    Start adding articles to your favorites.
                @else
                    Try adjusting your search or filters.
                @endif
            </p>
            @if(request('favorites'))
                <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg font-medium hover:bg-[#8B4D3A] transition">
                    Browse All Articles
                </a>
            @else
                <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg font-medium hover:bg-[#8B4D3A] transition">
                    Clear Filters
                </a>
            @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        border: 2px solid #6B3D2E;
        border-radius: 8px;
        color: #4A2C2A;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
        background: #FAF0E6;
    }

    .pagination a:hover {
        background: #6B3D2E;
        color: #F5E6D3;
        border-color: #6B3D2E;
    }

    .pagination .active span {
        background: #6B3D2E;
        color: #F5E6D3;
        border-color: #6B3D2E;
        font-weight: 600;
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
function toggleFavorite(articleId, button) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = meta ? meta.getAttribute('content') : '';

    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Error: CSRF token not found. Please refresh the page.');
        return;
    }

    const originalHTML = button.innerHTML;
    const originalClass = button.className;

    button.disabled = true;
    button.innerHTML = '<svg class="w-5 h-5 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    fetch(`/articles/${articleId}/toggle-favorite`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || 'Network response was not ok');
        }
        return response.json();
    })
    .then(() => {
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = originalHTML;
        button.className = originalClass;
        alert('Failed to update favorites: ' + error.message);
    });
}
</script>
@endpush
