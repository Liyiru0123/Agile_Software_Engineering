@extends('layouts.admin')

@section('title', 'Question Management - Admin Panel')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Question List</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.questions.create', request()->query()) }}" class="btn btn-light">
                <i class="fas fa-plus me-1"></i> Add New Question
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-8">
                <form action="{{ route('admin.questions.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                    <div class="flex-grow-1">
                        <select class="form-select" name="article_id">
                            <option value="">All Articles</option>
                            @foreach($articles as $article)
                                <option value="{{ $article->article_id }}" {{ request('article_id') == $article->article_id ? 'selected' : '' }}>
                                    {{ $article->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-grow-1">
                        <select class="form-select" name="type">
                            <option value="">All Question Types</option>
                            <option value="single" {{ request('type') == 'single' ? 'selected' : '' }}>Single Choice</option>
                            <option value="multiple" {{ request('type') == 'multiple' ? 'selected' : '' }}>Multiple Choice</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-light">Reset</a>
                </form>
            </div>
        </div>

        @if($questions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Associated Article</th>
                            <th>Question Content</th>
                            <th>Question Type</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                            <tr>
                                <td>{{ $question->question_id }}</td>
                                <td>
                                    <a href="{{ route('admin.articles.edit', $question->article_id) }}" class="text-primary">
                                        {{ $question->article->title }}
                                    </a>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($question->content, 50) }}</td>
                                <td>
                                    <span class="badge bg-{{ $question->type == 'single' ? 'info' : 'warning' }}">
                                        {{ $question->type == 'single' ? 'Single Choice' : 'Multiple Choice' }}
                                    </span>
                                </td>
                                <td>{{ $question->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.questions.edit', $question->question_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger delete-question-btn" 
                                                data-question-id="{{ $question->question_id }}">
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
                {{ $questions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No questions found</h5>
                <a href="{{ route('admin.questions.create', request()->query()) }}" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-plus me-1"></i>Add New Question
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this question? This action cannot be undone!</p>
                <p class="text-danger"><small>Deleting this question will also remove associated wrong answer book records!</small></p>
                <input type="hidden" id="deleteQuestionId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteQuestionBtn">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Show delete confirmation modal
    document.querySelectorAll('.delete-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionId = this.dataset.questionId;
            document.getElementById('deleteQuestionId').value = questionId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteQuestionModal'));
            deleteModal.show();
        });
    });

    // Confirm deletion
    document.getElementById('confirmDeleteQuestionBtn').addEventListener('click', function() {
        const questionId = document.getElementById('deleteQuestionId').value;
        
        fetch(`/admin/questions/${questionId}`, {
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
        bootstrap.Modal.getInstance(document.getElementById('deleteQuestionModal')).hide();
    });
</script>
@endsection