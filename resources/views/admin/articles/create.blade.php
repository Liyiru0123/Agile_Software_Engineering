@extends('layouts.admin')

@section('title', 'Add New Article - Admin Panel')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Add New Article</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.articles.store') }}" method="POST" id="createArticleForm">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('author') is-invalid @enderror" 
                           id="author" name="author" value="{{ old('author', 'Administrator') }}" required>
                    @error('author')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <select class="form-select @error('subject') is-invalid @enderror" 
                            id="subject" name="subject" required>
                        <option value="">Select Subject</option>
                        <option value="Civil Engineering" {{ old('subject') == 'Civil Engineering' ? 'selected' : '' }}>Civil Engineering</option>
                        <option value="Mathematics" {{ old('subject') == 'Mathematics' ? 'selected' : '' }}>Mathematics</option>
                        <option value="Computer Science" {{ old('subject') == 'Computer Science' ? 'selected' : '' }}>Computer Science</option>
                        <option value="Mechanical Engineering" {{ old('subject') == 'Mechanical Engineering' ? 'selected' : '' }}>Mechanical Engineering</option>
                        <option value="Mechanical Engineering with Transportation" {{ old('subject') == 'Mechanical Engineering with Transportation' ? 'selected' : '' }}>Mechanical Engineering with Transportation</option>
                    </select>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="level" class="form-label">Difficulty Level <span class="text-danger">*</span></label>
                    <select class="form-select @error('level') is-invalid @enderror" 
                            id="level" name="level" required>
                        <option value="">Select Difficulty</option>
                        <option value="Easy" {{ old('level') == 'Easy' ? 'selected' : '' }}>Easy</option>
                        <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="word_count" class="form-label">Word Count</label>
                    <input type="number" class="form-control @error('word_count') is-invalid @enderror" 
                           id="word_count" name="word_count" value="{{ old('word_count') }}" min="0" readonly>
                    @error('word_count')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="excerpt" class="form-label">Article Excerpt <span class="text-danger">*</span></label>
                <textarea class="form-control @error('excerpt') is-invalid @enderror" 
                          id="excerpt" name="excerpt" rows="3" required>{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Article Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text mt-1">Please enter plain text content, line breaks will be preserved automatically</div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save Article
                </button>
                <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto calculate word count
    document.getElementById('content').addEventListener('input', function() {
        const content = this.value.trim();
        const wordCountInput = document.getElementById('word_count');
        
        // Auto calculate only if word count is empty
        if (wordCountInput.value === '') {
            // Count words by splitting on spaces (simple counting)
            const wordCount = content.split(/\s+/).filter(word => word.length > 0).length;
            wordCountInput.value = wordCount;
        }
    });

    // Form submission validation
    document.getElementById('createArticleForm').addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const excerpt = document.getElementById('excerpt').value.trim();
        const content = document.getElementById('content').value.trim();
        
        if (!title) {
            alert('Please enter article title');
            e.preventDefault();
            return;
        }
        
        if (!excerpt) {
            alert('Please enter article excerpt');
            e.preventDefault();
            return;
        }
        
        if (!content) {
            alert('Please enter article content');
            e.preventDefault();
            return;
        }
    });
</script>
@endsection