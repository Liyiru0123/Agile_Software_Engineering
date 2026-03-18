@extends('layouts.app')

@section('title', 'Reading History - English Reading Platform')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reading History ({{ $histories->total() }})</h5>
        <button class="btn btn-danger btn-sm" id="clearHistoryBtn" {{ $histories->count() > 0 ? '' : 'disabled' }}>
            <i class="fas fa-broom me-1"></i>Clear History
        </button>
    </div>
    <div class="card-body">
        @if($histories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Difficulty</th>
                            <th>Reading Progress</th>
                            <th>Last Read Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($histories as $history)
                            <tr>
                                <td>
                                    <a href="{{ route('articles.show', ['article' => $history->article_id]) }}" class="text-decoration-none">
                                        {{ $history->article->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $history->article->subject }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $history->article->level == 'easy' ? 'success' : ($history->article->level == 'intermediate' ? 'primary' : 'danger') }}">
                                        {{ $history->article->level }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 8px; width: 100px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $history->progress }}%" 
                                             aria-valuenow="{{ $history->progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small>{{ $history->progress }}%</small>
                                </td>
                                <td>{{ $history->updated_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <!-- Fix: use article_id instead of history_id -->
                                    <button class="btn btn-sm btn-outline-danger delete-history" 
                                            data-article-id="{{ $history->article_id }}">
                                        <i class="fas fa-trash me-1"></i>Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-3">
                {{ $histories->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-history fa-3x mb-3"></i>
                <h5>You have no reading history yet</h5>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary mt-3">
                    Go to Read Articles
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    // Delete single history record (Fix: use article_id)
    document.querySelectorAll('.delete-history').forEach(btn => {
        btn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            if (confirm('Are you sure you want to delete this reading record?')) {
                fetch(`/reading-history/${articleId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.code === 0) {
                        window.location.reload();
                    } else {
                        alert('Deletion failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Deletion failed, please try again');
                });
            }
        });
    });

    // Clear all history
    document.getElementById('clearHistoryBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all reading history? This action cannot be undone!')) {
            fetch('/reading-history/clear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 0) {
                    window.location.reload();
                } else {
                    alert('Clear failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Clear failed, please try again');
            });
        }
    });
</script>
@endsection