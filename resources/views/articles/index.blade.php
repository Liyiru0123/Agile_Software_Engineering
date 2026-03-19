@extends('layouts.app')

@section('title', 'Article List - English Reading Platform')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Article List</h5>
        <div class="d-flex gap-2">
            <!-- Subject Filter -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ request('subject') ? request('subject') : 'All Subjects' }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('articles.index', request()->except('subject')) }}">All Subjects</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['subject' => 'Civil Engineering'])) }}">Civil Engineering</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['subject' => 'Mathematics'])) }}">Mathematics</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['subject' => 'Computer Science'])) }}">Computer Science</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['subject' => 'Mechanical Engineering'])) }}">Mechanical Engineering</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['subject' => 'Mechanical Engineering with Transportation'])) }}">Mechanical Engineering with Transportation</a></li>
                </ul>
            </div>
            <!-- Difficulty Filter -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ request('level') ? ucfirst(request('level')) : 'All Levels' }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('articles.index', request()->except('level')) }}">All Levels</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['level' => 'easy'])) }}">Easy</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['level' => 'intermediate'])) }}">Intermediate</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['level' => 'advanced'])) }}">Advanced</a></li>
                </ul>
            </div>
            <!-- Sort -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    {{ request('sort') == 'popular' ? 'By Read Count' : 'By Latest' }}
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['sort' => 'newest'])) }}">By Latest</a></li>
                    <li><a class="dropdown-item" href="{{ route('articles.index', array_merge(request()->all(), ['sort' => 'popular'])) }}">By Read Count</a></li>
                </ul>
            </div>
            <!-- Reset Filter -->
            <button class="btn btn-outline-danger" onclick="window.location.href='{{ route('articles.index') }}'">
                <i class="fas fa-refresh me-1"></i>Reset
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($articles->count() > 0)
            <div class="row g-4">
                @foreach($articles as $article)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-secondary">{{ $article->subject }}</span>
                                    {{-- Fix: Difficulty label color compatibility --}}
                                    @php
                                        $level = strtolower($article->level ?? '');
                                        $bgClass = match($level) {
                                            'easy', 'simple', 'e' => 'success',
                                            'intermediate', 'middle', 'medium', 'i' => 'primary',
                                            'hard', 'advanced', 'difficult', 'h' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $bgClass }}">
                                        {{ ucfirst($article->level) }}
                                    </span>
                                </div>
                                <h5 class="card-title">{{ $article->title }}</h5>
                                <p class="card-text text-muted">{{ \Illuminate\Support\Str::limit($article->excerpt, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">Read Count: {{ $article->read_count }}</small>
                                    <small class="text-muted">Word Count: {{ $article->word_count }}</small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent d-flex gap-2">
                                <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-primary flex-grow-1">
                                    <i class="fas fa-book-reader me-1"></i>Read
                                </a>
                                @auth
                                    <button class="btn {{ $article->is_favorited ? 'btn-danger' : 'btn-outline-danger' }} favorite-btn" 
                                            data-article-id="{{ $article->article_id }}">
                                        <i class="fas fa-star me-1"></i>{{ $article->is_favorited ? 'Favorited' : 'Favorite' }}
                                    </button>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $articles->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-search fa-3x mb-3"></i>
                <h5>No articles found matching your criteria</h5>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary mt-3">
                    View All Articles
                </a>
            </div>
        @endif
    </div>
</div>

@auth
<script>
    // Favorite function
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;

            fetch(`/favorites/${articleId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 0) {
                    if (data.action === 'unfavorite') {
                        this.classList.remove('btn-danger');
                        this.classList.add('btn-outline-danger');
                        this.innerHTML = '<i class="fas fa-star me-1"></i>Favorite';
                    } else {
                        this.classList.remove('btn-outline-danger');
                        this.classList.add('btn-danger');
                        this.innerHTML = '<i class="fas fa-star me-1"></i>Favorited';
                    }
                    alert(data.message);
                } else {
                    alert(data.message || 'Operation failed');
                }
            })
            .catch(error => {
                console.error('Favorite operation failed:', error);
                alert('Operation failed, please try again');
            });
        });
    });
</script>
@endauth
@endsection