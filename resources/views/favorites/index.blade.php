@extends('layouts.app')

@section('title', 'My Collections - English Reading Platform')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">My Collections ({{ $favorites->total() }})</h5>
        <button class="btn btn-danger btn-sm" id="batchDeleteBtn" disabled>
            <i class="fas fa-trash me-1"></i>Batch Delete
        </button>
    </div>
    <div class="card-body">
        @if($favorites->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Difficulty</th>
                            <th>Collection Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($favorites as $favorite)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input favorite-checkbox" 
                                           data-article-id="{{ $favorite->article_id }}">
                                </td>
                                <td>
                                    <a href="{{ route('articles.show', $favorite->article) }}" class="text-decoration-none">
                                        {{ $favorite->article->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $favorite->article->subject }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $favorite->article->level == 'easy' ? 'success' : ($favorite->article->level == 'intermediate' ? 'primary' : 'danger') }}">
                                        {{ $favorite->article->level }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($favorite->created_at)->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('articles.show', $favorite->article) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-book-reader me-1"></i>Read
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-favorite" 
                                                data-article-id="{{ $favorite->article_id }}">
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
                {{ $favorites->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-star fa-3x mb-3"></i>
                <h5>You haven't collected any articles yet</h5>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary mt-3">
                    Go to Collect Articles
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    // Select/deselect all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.favorite-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBatchDeleteBtn();
    });

    // Single checkbox change
    document.querySelectorAll('.favorite-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBatchDeleteBtn);
    });

    // Update batch delete button status
    function updateBatchDeleteBtn() {
        const checkedBoxes = document.querySelectorAll('.favorite-checkbox:checked');
        document.getElementById('batchDeleteBtn').disabled = checkedBoxes.length === 0;
    }

    // Delete single collection
    document.querySelectorAll('.delete-favorite').forEach(btn => {
        btn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            if (confirm('Are you sure you want to remove this article from your collections?')) {
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
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Deletion failed: ' + (data.message || 'Operation error'));
                    }
                })
                .catch(error => {
                    console.error('Deletion failed:', error);
                    alert('Deletion failed, please try again');
                });
            }
        });
    });

    // Batch delete (fix event bubbling)
    document.getElementById('batchDeleteBtn').addEventListener('click', function(e) {
        e.stopPropagation();

        const checkedBoxes = document.querySelectorAll('.favorite-checkbox:checked');
        if (checkedBoxes.length === 0) return;
        
        if (confirm(`Are you sure you want to remove these ${checkedBoxes.length} articles from your collections?`)) {
            const formData = new FormData();
            checkedBoxes.forEach(checkbox => {
                formData.append('article_ids[]', checkbox.dataset.articleId);
            });

            fetch('/favorites/batch-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 0) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Batch deletion failed: ' + (data.message || 'Operation error'));
                }
            })
            .catch(error => {
                console.error('Batch deletion failed:', error);
                alert('Batch deletion failed, please try again');
            });
        }
    });
</script>
@endsection