@extends('layouts.app')

@section('title', $article->title . ' - English Reading Platform')

@section('content')
<div class="row">
    <!-- Article Main Content -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-secondary me-2">{{ $article->subject }}</span>
                    {{-- Fix: Compatibility for difficulty label color --}}
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
                @auth
                    <button class="btn {{ $article->is_favorited ? 'btn-danger' : 'btn-outline-danger' }} favorite-btn" 
                            data-article-id="{{ $article->article_id }}">
                        <i class="fas fa-star me-1"></i>{{ $article->is_favorited ? 'Favorited' : 'Favorite' }}
                    </button>
                @endauth
            </div>
            <div class="card-body">
                <h1 class="card-title mb-4">{{ $article->title }}</h1>
                <div class="d-flex justify-content-between text-muted mb-4 flex-wrap">
                    <div class="me-3">Author: {{ $article->author }}</div>
                    <div class="me-3">Read Count: {{ $article->read_count }}</div>
                    <div class="me-3">Word Count: {{ $article->word_count }}</div>
                    <div>Published: {{ $article->created_at->format('Y-m-d') }}</div>
                </div>
                <!-- Reading Controls -->
                <div class="d-flex gap-2 mb-4">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Font Size
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeFontSize('small')">Small</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeFontSize('normal')">Medium</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeFontSize('large')">Large</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Line Spacing
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeLineHeight('small')">Compact</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeLineHeight('normal')">Normal</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeLineHeight('large')">Relaxed</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Article Content -->
                <div class="article-content" id="articleContent">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>
        </div>

        <!-- Reading Comprehension Questions -->
        @if($questions->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Reading Comprehension Questions</h5>
                </div>
                <div class="card-body">
                    @auth
                        <form id="answerForm">
                            @csrf
                            <div id="questionsContainer">
                                @foreach($questions as $index => $question)
                                    <div class="card question-card mb-4" data-question-id="{{ $question->question_id }}">
                                        <div class="card-body">
                                            <h6 class="card-title mb-3">
                                                {{ $index + 1 }}. {{ $question->content }}
                                                <span class="badge bg-{{ $question->type == 'single' ? 'info' : 'warning' }} ms-2">
                                                    {{ $question->type == 'single' ? 'Single Choice' : 'Multiple Choice' }}
                                                </span>
                                            </h6>
                                            <div class="options-container">
                                                @foreach($question->options as $key => $option)
                                                    <div class="option-item">
                                                        <input type="{{ $question->type == 'single' ? 'radio' : 'checkbox' }}"
                                                               name="answers[{{ $question->question_id }}][]"
                                                               id="option-{{ $question->question_id }}-{{ $key }}"
                                                               value="{{ $key }}"
                                                               class="me-2">
                                                        <label for="option-{{ $question->question_id }}-{{ $key }}" class="form-check-label p-2 rounded border w-100">
                                                            {{ $key }}. {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" id="submitAnswersBtn">
                                    <i class="fas fa-check me-1"></i>Submit Answers
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="resetAnswersBtn">
                                    <i class="fas fa-refresh me-1"></i>Reset Answers
                                </button>
                            </div>
                        </form>

                        <!-- Answer Results (Hidden by default) -->
                        <div id="answerResults" class="mt-4 d-none">
                            <div class="alert alert-info mb-3">
                                <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Answer Results</h6>
                            </div>
                            <div id="resultsContainer"></div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Please login to answer questions
                        </div>
                    @endauth
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Reading Progress -->
        @auth
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Reading Progress</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between text-sm text-muted">
                        <span id="progressText">0%</span>
                        <span>Last Read: {{ $article->readingHistory?->updated_at?->format('Y-m-d H:i') ?? 'Not read yet' }}</span>
                    </div>
                </div>
            </div>
        @endauth

        <!-- Related Recommendations -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Related Recommendations</h6>
            </div>
            <div class="card-body">
                @if($relatedArticles->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($relatedArticles as $relArticle)
                            <li class="list-group-item">
                                <a href="{{ route('articles.show', $relArticle) }}" class="text-decoration-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-medium">{{ $relArticle->title }}</span>
                                        <span class="badge bg-secondary">{{ $relArticle->subject }}</span>
                                    </div>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($relArticle->excerpt, 50) }}</small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-3 text-muted">
                        <small>No related recommendations</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Font size adjustment
    function changeFontSize(size) {
        const content = document.getElementById('articleContent');
        if (size === 'small') content.style.fontSize = '14px';
        else if (size === 'normal') content.style.fontSize = '16px';
        else if (size === 'large') content.style.fontSize = '18px';
    }

    // Line height adjustment
    function changeLineHeight(size) {
        const content = document.getElementById('articleContent');
        if (size === 'small') content.style.lineHeight = '1.4';
        else if (size === 'normal') content.style.lineHeight = '1.8';
        else if (size === 'large') content.style.lineHeight = '2.2';
    }

    // Initialize default styles
    changeFontSize('normal');
    changeLineHeight('normal');

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

    // Reading progress tracking
    @auth
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const articleContent = document.getElementById('articleContent');
        const articleId = {{ $article->article_id }};

        // Initial progress
        let initialProgress = {{ $article->readingHistory?->progress ?? 0 }};
        updateProgress(initialProgress);

        // Send start reading request
        fetch(`/reading-history/${articleId}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        // Scroll listener with debounce
        let progressTimer = null;
        window.addEventListener('scroll', function() {
            clearTimeout(progressTimer);
            progressTimer = setTimeout(() => {
                const contentRect = articleContent.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const contentHeight = articleContent.offsetHeight;
                
                let scrollTop = window.scrollY;
                let contentTop = contentRect.top + scrollTop;
                let scrollPosition = scrollTop - contentTop + windowHeight;
                let progress = Math.min(Math.max(0, (scrollPosition / contentHeight) * 100), 100);
                
                if (Math.abs(Math.round(progress) - Math.round(initialProgress)) >= 5) {
                    initialProgress = progress;
                    updateProgress(progress);
                    fetch(`/reading-history/${articleId}/progress`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ progress: Math.round(progress) })
                    });
                }
            }, 300);
        });

        function updateProgress(progress) {
            const percent = Math.round(progress);
            progressBar.style.width = `${percent}%`;
            progressBar.setAttribute('aria-valuenow', percent);
            progressText.textContent = `${percent}%`;
        }
    @endauth

    // Answer submission (prevent duplicate submission)
    document.addEventListener('DOMContentLoaded', function() {
        let isAnswerSubmitted = false;

        // Reset answers
        document.getElementById('resetAnswersBtn').addEventListener('click', function() {
            if (!isAnswerSubmitted) {
                document.querySelectorAll('input[name^="answers["]').forEach(input => {
                    input.checked = false;
                });
            } else {
                alert('Answers have been submitted, cannot reset!');
            }
        });

        // Submit answers
        document.getElementById('answerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (isAnswerSubmitted) {
                alert('You have already submitted your answers, cannot submit again!');
                return;
            }

            const submitBtn = document.getElementById('submitAnswersBtn');
            const originalHtml = submitBtn.innerHTML;
            
            isAnswerSubmitted = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            submitBtn.style.cursor = 'not-allowed';

            // Collect answers
            const answers = {};
            document.querySelectorAll('[data-question-id]').forEach(card => {
                const questionId = card.dataset.questionId;
                const selectedOptions = Array.from(card.querySelectorAll('input[name="answers[' + questionId + '][]"]:checked'))
                    .map(input => input.value);
                if (selectedOptions.length > 0) {
                    answers[questionId] = selectedOptions;
                }
            });

            // Submit answers
            fetch('{{ route("reading-answers.submit") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ answers: answers })
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 0) {
                    submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Submitted';
                    
                    // Show results
                    document.getElementById('answerResults').classList.remove('d-none');
                    const resultsContainer = document.getElementById('resultsContainer');
                    resultsContainer.innerHTML = '';

                    // Render results
                    let correctCount = 0;
                    data.results.forEach((result, index) => {
                        correctCount += result.is_correct ? 1 : 0;

                        const resultCard = document.createElement('div');
                        resultCard.className = 'card mb-3';
                        resultCard.innerHTML = `
                            <div class="card-header ${result.is_correct ? 'bg-success text-white' : 'bg-danger text-white'}">
                                <h6 class="mb-0">
                                    ${index + 1}. ${result.is_correct ? '<i class="fas fa-check me-2"></i>Correct Answer' : '<i class="fas fa-times me-2"></i>Wrong Answer'}
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text mb-2"><strong>Question：</strong>${result.content}</p>
                                <p class="card-text mb-2">
                                    <strong>Your Answer：</strong>
                                    <span class="${result.is_correct ? 'result-correct' : 'result-incorrect'}">
                                        ${Array.isArray(result.user_answer) ? result.user_answer.join(', ') : result.user_answer}
                                    </span>
                                </p>
                                <p class="card-text mb-2">
                                    <strong>Correct Answer：</strong>
                                    <span class="result-correct">${Array.isArray(result.correct_answer) ? result.correct_answer.join(', ') : result.correct_answer}</span>
                                </p>
                                ${result.explanation ? `<p class="card-text"><strong>Explanation：</strong>${result.explanation}</p>` : ''}
                                ${!result.is_correct ? `
                                    <button class="btn btn-sm btn-outline-danger mt-2 add-to-wrong-btn" 
                                            data-question-id="${result.question_id}"
                                            data-user-answer='${JSON.stringify(result.user_answer)}'>
                                        <i class="fas fa-bookmark me-1"></i>Add to Wrong Answer Book
                                    </button>
                                ` : ''}
                            </div>
                        `;
                        resultsContainer.appendChild(resultCard);
                    });

                    // Stats
                    const statsDiv = document.createElement('div');
                    statsDiv.className = 'alert alert-secondary mb-0';
                    statsDiv.innerHTML = `
                        <strong>Answer Statistics：</strong>
                        Total ${data.results.length} questions, ${correctCount} correct, ${data.results.length - correctCount} wrong
                    `;
                    resultsContainer.appendChild(statsDiv);

                    // Add to wrong answer book
                    document.querySelectorAll('.add-to-wrong-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const questionId = this.dataset.questionId;
                            const userAnswer = JSON.parse(this.dataset.userAnswer);

                            fetch('{{ route("reading-answers.add-to-wrong") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    question_id: questionId,
                                    user_answer: userAnswer
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.code === 0) {
                                    alert(data.message);
                                    this.disabled = true;
                                    this.innerHTML = '<i class="fas fa-check me-1"></i>Added to Wrong Answer Book';
                                } else {
                                    alert(data.message || 'Operation failed');
                                }
                            })
                            .catch(error => {
                                console.error('Add to wrong answer book failed:', error);
                                alert('Operation failed, please try again');
                            });
                        });
                    });
                } else {
                    // Restore button state on failure
                    isAnswerSubmitted = false;
                    submitBtn.innerHTML = originalHtml;
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                    alert(data.message || 'Submission failed, please try again');
                }
            })
            .catch(error => {
                // Restore button state on network error
                isAnswerSubmitted = false;
                submitBtn.innerHTML = originalHtml;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
                console.error('Answer submission failed:', error);
                alert('Submission failed, please try again');
            });
        });
    });
</script>
@endsection