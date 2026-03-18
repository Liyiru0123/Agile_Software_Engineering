@extends('layouts.app')

@section('title', 'My Wrong Questions - English Reading Platform')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-exclamation-circle me-2"></i>My Wrong Questions</h5>
    </div>
    <div class="card-body">
        @if($wrongQuestions->count() > 0)
            <div class="row g-4">
                @foreach($wrongQuestions as $wrong)
                    <div class="col-12">
                        <div class="card border-danger mb-3">
                            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    Article: <a href="{{ route('articles.show', $wrong->question->article->article_id) }}" class="text-white">
                                        {{ $wrong->question->article->title }}
                                    </a>
                                </h6>
                                <!-- Fix: use wrong_question_id as data attribute -->
                                <button class="btn btn-sm btn-light delete-wrong-btn" 
                                        data-wrong-id="{{ $wrong->wrong_question_id }}">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-2"><strong>Question:</strong>{{ $wrong->question->content }}</p>
                                <p class="card-text mb-2">
                                    <strong>Your Answer:</strong>
                                    <span class="result-incorrect">{{ implode(', ', is_array($wrong->user_answer) ? $wrong->user_answer : ($wrong->user_answer ? [$wrong->user_answer] : [])) }}</span>
                                </p>
                                <p class="card-text mb-2">
                                    <strong>Correct Answer:</strong>
                                    <span class="result-correct">{{ implode(', ', is_array($wrong->question->answer) ? $wrong->question->answer : ($wrong->question->answer ? [$wrong->question->answer] : [])) }}</span>
                                </p>
                                @if($wrong->question->explanation)
                                    <p class="card-text">
                                        <strong>Explanation:</strong>{{ $wrong->question->explanation }}
                                    </p>
                                @endif
                                <div class="mt-2">
                                    <span class="badge bg-{{ $wrong->question->type == 'single' ? 'info' : 'warning' }}">
                                        {{ $wrong->question->type == 'single' ? 'Single Choice' : 'Multiple Choice' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $wrongQuestions->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h5>No Wrong Question Records</h5>
                <p class="mb-0">Keep up the good work and practice more!</p>
                <a href="{{ route('articles.index') }}" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-book-reader me-2"></i>Start Reading & Answering
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete wrong question confirmation modal -->
<div class="modal fade" id="deleteWrongModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this wrong question? This action cannot be undone!</p>
                <input type="hidden" id="deleteWrongId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteWrongBtn">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Show delete confirmation modal
    document.querySelectorAll('.delete-wrong-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const wrongId = this.dataset.wrongId;
            document.getElementById('deleteWrongId').value = wrongId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteWrongModal'));
            deleteModal.show();
        });
    });

    // Confirm delete wrong question
    document.getElementById('confirmDeleteWrongBtn').addEventListener('click', function() {
        const wrongId = document.getElementById('deleteWrongId').value;
        
        fetch(`/wrong-questions/${wrongId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
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
        bootstrap.Modal.getInstance(document.getElementById('deleteWrongModal')).hide();
    });
</script>
@endsection