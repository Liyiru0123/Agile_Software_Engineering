@extends('layouts.admin')

@section('title', 'Article Management - Admin Panel')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Article List</h5>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-light">
            <i class="fas fa-plus me-1"></i> Add New Article
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="{{ route('admin.articles.index') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" 
                           placeholder="Search article title..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </form>
            </div>
        </div>

        @if($articles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Subject</th> <!-- Replaced Category with Subject -->
                            <th>Level</th>
                            <th>Author</th>
                            <th>Read Count</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($articles as $article)
                            <tr>
                                {{-- Fix 1: Show article_id instead of id --}}
                                <td>{{ $article->article_id }}</td>
                                <td>{{ $article->title }}</td>
                                <!-- Replaced category with subject -->
                                <td><span class="badge bg-secondary">{{ $article->subject }}</span></td>
                                <td>
                                    <!-- Style mapping for difficulty levels (Easy/Intermediate/Advanced) -->
                                    <span class="badge bg-{{ $article->level == 'Easy' ? 'success' : ($article->level == 'Intermediate' ? 'primary' : 'danger') }}">
                                        {{ $article->level }}
                                    </span>
                                </td>
                                <td>{{ $article->author }}</td>
                                <!-- Replaced views with read_count -->
                                <td>{{ $article->read_count }}</td>
                                <td>{{ $article->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        {{-- New: Manage Questions button --}}
                                        <a href="{{ route('admin.questions.index', ['article_id' => $article->article_id]) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-question-circle me-1"></i>Manage Questions
                                        </a>
                                        {{-- Fix 2: Edit route parameter changed to article_id --}}
                                        <a href="{{ route('admin.articles.edit', $article->article_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        {{-- Fix 3: Delete button data-article-id changed to article_id --}}
                                        <button class="btn btn-sm btn-outline-danger delete-btn" 
                                                data-article-id="{{ $article->article_id }}">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $articles->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No articles found</h5>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this article? This action cannot be undone!</p>
                <p class="text-danger"><small>Deleting the article will also remove all associated questions!</small></p>
                <input type="hidden" id="deleteArticleId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Show delete confirmation modal
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            document.getElementById('deleteArticleId').value = articleId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        });
    });

    // Confirm deletion
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const articleId = document.getElementById('deleteArticleId').value;
        
        fetch(`/admin/articles/${articleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.code === 0) {
                alert('Deleted successfully!');
                window.location.reload();
            } else {
                alert('Deletion failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Deletion failed, please try again');
        });
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
    });
</script>
@endsection