@extends('layouts.app')

@section('title', 'Home - English Reading Platform')

@section('content')
<div class="container-fluid px-0">
    <!-- Hero banner -->
    <div class="py-5 mb-5" style="background-color: #F8F4E9; color: #5C1A1A;">
        <div class="container">
            <h1 class="display-4" style="color: #5C1A1A; font-weight: 600;">Improve Your English Reading Skills</h1>
            <p class="lead" style="color: #3A2618;">Curated graded reading articles for English learners at all levels</p>
            <a href="{{ route('articles.index') }}" class="btn btn-lg" style="background-color: #8B4513; color: #F8F4E9; border-color: #8B4513;">
                Start Reading <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>

    <!-- Browse by subject -->
    <div class="mb-8">
        <h2 class="mb-4">Browse by Subject</h2>
        <div class="row g-4">
            <!-- Civil Engineering -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="fas fa-building fa-3x text-primary mb-3"></i>
                        <h4 class="card-title mb-2">Civil Engineering</h4>
                        <p class="text-muted mb-4">English reading materials related to Civil Engineering</p>
                        <a href="{{ route('articles.index', ['subject' => 'Civil Engineering']) }}" class="btn btn-outline-primary">
                            View Articles
                        </a>
                    </div>
                </div>
            </div>
            <!-- Mathematics -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="fas fa-calculator fa-3x text-success mb-3"></i>
                        <h4 class="card-title mb-2">Mathematics</h4>
                        <p class="text-muted mb-4">English reading materials related to Mathematics</p>
                        <a href="{{ route('articles.index', ['subject' => 'Mathematics']) }}" class="btn btn-outline-success">
                            View Articles
                        </a>
                    </div>
                </div>
            </div>
            <!-- Computer Science -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="fas fa-laptop-code fa-3x text-info mb-3"></i>
                        <h4 class="card-title mb-2">Computer Science</h4>
                        <p class="text-muted mb-4">English reading materials related to Computer Science</p>
                        <a href="{{ route('articles.index', ['subject' => 'Computer Science']) }}" class="btn btn-outline-info">
                            View Articles
                        </a>
                    </div>
                </div>
            </div>
            <!-- Mechanical Engineering -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="fas fa-cogs fa-3x text-warning mb-3"></i>
                        <h4 class="card-title mb-2">Mechanical Engineering</h4>
                        <p class="text-muted mb-4">English reading materials related to Mechanical Engineering</p>
                        <a href="{{ route('articles.index', ['subject' => 'Mechanical Engineering']) }}" class="btn btn-outline-warning">
                            View Articles
                        </a>
                    </div>
                </div>
            </div>
            <!-- Mechanical Engineering with Transportation -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-5">
                        <i class="fas fa-truck fa-3x text-danger mb-3"></i>
                        <h4 class="card-title mb-2">Mechanical Engineering with Transportation</h4>
                        <p class="text-muted mb-4">English reading materials related to Mechanical Engineering (Transportation)</p>
                        <a href="{{ route('articles.index', ['subject' => 'Mechanical Engineering with Transportation']) }}" class="btn btn-outline-danger">
                            View Articles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Choose by difficulty -->
    <div class="mb-8">
        <h2 class="mb-4 mt-4">Choose by Difficulty</h2>
        <div class="row g-4">
            <!-- Easy -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Easy</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Suitable for beginners, vocabulary 1000-2000, simple sentences</p>
                        <a href="{{ route('articles.index', ['level' => 'easy']) }}" class="btn btn-success">
                            <i class="fas fa-book-reader me-2"></i>Start Reading
                        </a>
                    </div>
                </div>
            </div>
            <!-- Intermediate -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Intermediate</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Suitable for intermediate learners, vocabulary 3000-5000, rich sentence patterns</p>
                        <a href="{{ route('articles.index', ['level' => 'intermediate']) }}" class="btn btn-primary">
                            <i class="fas fa-book-reader me-2"></i>Start Reading
                        </a>
                    </div>
                </div>
            </div>
            <!-- Advanced -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Advanced</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Suitable for advanced learners, vocabulary 6000+, complex sentence structures</p>
                        <a href="{{ route('articles.index', ['level' => 'advanced']) }}" class="btn btn-danger">
                            <i class="fas fa-book-reader me-2"></i>Start Reading
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured articles -->
    <div>
        <h2 class="mb-4 mt-4">Featured Articles</h2>
        @if($featuredArticles ?? false)
            <div class="row g-4">
                @foreach($featuredArticles as $article)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-secondary">{{ $article->subject }}</span>
                                    <span class="badge bg-{{ $article->level == 'easy' ? 'success' : ($article->level == 'intermediate' ? 'primary' : 'danger') }}">
                                        {{ $article->level }}
                                    </span>
                                </div>
                                <h5 class="card-title">{{ $article->title }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($article->excerpt, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">{{ $article->created_at->format('Y-m-d') }}</small>
                                    <small class="text-muted">Author: {{ $article->author }}</small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="{{ route('articles.show', $article) }}" class="btn btn-outline-primary w-100">
                                    Read Full Article <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-newspaper fa-3x mb-3"></i>
                <p>No featured articles available</p>
            </div>
        @endif
    </div>
</div>
@endsection