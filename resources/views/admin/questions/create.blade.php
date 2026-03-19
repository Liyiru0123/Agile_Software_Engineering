@extends('layouts.admin')

@section('title', 'Add New Question - Admin Panel')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Add New Question</h5>
    </div>
    <div class="card-body">
        {{-- Error alert for validation failures --}}
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <strong>Submission Failed:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <form action="{{ route('admin.questions.store') }}" method="POST" id="createQuestionForm">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="article_id" class="form-label">Associated Article <span class="text-danger">*</span></label>
                    <select class="form-select @error('article_id') is-invalid @enderror" 
                            id="article_id" name="article_id" required>
                        <option value="">Select Article</option>
                        @foreach($articles as $article)
                            <option value="{{ $article->article_id }}" {{ old('article_id', $article?->article_id) == $article->article_id ? 'selected' : '' }}>
                                {{ $article->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('article_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="type" class="form-label">Question Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" 
                            id="type" name="type" required>
                        <option value="">Select Question Type</option>
                        <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>Single Choice</option>
                        <option value="multiple" {{ old('type') == 'multiple' ? 'selected' : '' }}>Multiple Choice</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Question Content <span class="text-danger">*</span></label>
                <textarea class="form-control @error('content') is-invalid @enderror" 
                          id="content" name="content" rows="3" required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Options Section -->
            <div class="mb-3">
                <label class="form-label">Question Options <span class="text-danger">*</span></label>
                <div id="optionsContainer">
                    <div class="option-group">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control option-key" placeholder="Option Identifier (e.g. A/B/C)" value="A">
                            <input type="text" class="form-control option-value flex-grow-1" placeholder="Option Content" value="{{ old('options.A') ?? '' }}">
                        </div>
                    </div>
                    <div class="option-group">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control option-key" placeholder="Option Identifier (e.g. A/B/C)" value="B">
                            <input type="text" class="form-control option-value flex-grow-1" placeholder="Option Content" value="{{ old('options.B') ?? '' }}">
                        </div>
                    </div>
                    <div class="option-group">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control option-key" placeholder="Option Identifier (e.g. A/B/C)" value="C">
                            <input type="text" class="form-control option-value flex-grow-1" placeholder="Option Content" value="{{ old('options.C') ?? '' }}">
                        </div>
                    </div>
                    <div class="option-group">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control option-key" placeholder="Option Identifier (e.g. A/B/C)" value="D">
                            <input type="text" class="form-control option-value flex-grow-1" placeholder="Option Content" value="{{ old('options.D') ?? '' }}">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addOptionBtn">
                    <i class="fas fa-plus me-1"></i>Add Option
                </button>
                @error('options')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Correct Answer -->
            <div class="mb-3">
                <label for="answer" class="form-label">Correct Answer <span class="text-danger">*</span></label>
                <div class="form-text mb-2" id="answerHint">
                    Enter correct option identifier (single choice: one value, e.g. A; multiple choice: comma separated, e.g. A,C)
                </div>
                <input type="text" class="form-control @error('answer') is-invalid @enderror" 
                       id="answer" name="answer" value="{{ old('answer') }}" required>
                @error('answer')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('answer_array')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Answer Explanation -->
            <div class="mb-3">
                <label for="explanation" class="form-label">Answer Explanation</label>
                <textarea class="form-control @error('explanation') is-invalid @enderror" 
                          id="explanation" name="explanation" rows="3">{{ old('explanation') }}</textarea>
                @error('explanation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            {{-- Hidden fields: Store options and answer in JSON format --}}
            <input type="hidden" id="optionsJson" name="options">
            <input type="hidden" id="answerArrayJson" name="answer_array">
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save Question
                </button>
                <a href="{{ route('admin.questions.index', request()->query()) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Add option
    document.getElementById('addOptionBtn').addEventListener('click', function() {
        const container = document.getElementById('optionsContainer');
        const optionGroups = container.querySelectorAll('.option-group');
        const lastKey = optionGroups.length > 0 
            ? String.fromCharCode(65 + optionGroups.length) 
            : 'A';
        
        const newOption = document.createElement('div');
        newOption.className = 'option-group';
        newOption.innerHTML = `
            <div class="d-flex gap-2">
                <input type="text" class="form-control option-key" placeholder="Option Identifier (e.g. A/B/C)" value="${lastKey}">
                <input type="text" class="form-control option-value flex-grow-1" placeholder="Option Content">
                <button type="button" class="btn btn-sm btn-danger remove-option-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newOption);
        
        // Bind remove option event
        newOption.querySelector('.remove-option-btn').addEventListener('click', function() {
            newOption.remove();
        });
    });

    // Form submission handling (Fix: Correctly pass answer_array)
    document.getElementById('createQuestionForm').addEventListener('submit', function(e) {
        // Collect options
        const options = {};
        let hasEmptyOption = false;
        
        document.querySelectorAll('.option-group').forEach(group => {
            const keyInput = group.querySelector('.option-key');
            const valueInput = group.querySelector('.option-value');
            const key = keyInput.value.trim();
            const value = valueInput.value.trim();
            
            if (key && value) {
                options[key] = value;
            } else if (key || value) {
                hasEmptyOption = true;
            }
        });
        
        // Validate options
        if (Object.keys(options).length < 2) {
            alert('At least 2 valid options are required!');
            e.preventDefault();
            return;
        }
        
        if (hasEmptyOption) {
            alert('Option identifier and content cannot be empty!');
            e.preventDefault();
            return;
        }
        
        // Validate answer
        const answerInput = document.getElementById('answer');
        const answer = answerInput.value.trim();
        const type = document.getElementById('type').value;
        const answerArray = answer.split(',').map(item => item.trim());
        
        if (type === 'single' && answerArray.length > 1) {
            alert('Single choice questions can only have one correct answer!');
            e.preventDefault();
            return;
        }
        
        // Check if answers exist in options
        for (const ans of answerArray) {
            if (!options.hasOwnProperty(ans)) {
                alert(`Answer "${ans}" is not in the option list!`);
                e.preventDefault();
                return;
            }
        }
        
        // ✅ Critical fix: Correctly set hidden fields
        document.getElementById('optionsJson').value = JSON.stringify(options);
        document.getElementById('answerArrayJson').value = JSON.stringify(answerArray);
        
        // ✅ Remove e.preventDefault() to allow normal form submission
        // Let form submit to backend after validation passes
    });

    // Question type change hint
    document.getElementById('type').addEventListener('change', function() {
        const hint = document.getElementById('answerHint');
        if (this.value === 'single') {
            hint.textContent = 'Enter correct option identifier (single choice: one value, e.g. A)';
        } else {
            hint.textContent = 'Enter correct option identifiers (multiple choice: comma separated, e.g. A,C)';
        }
    });
</script>
@endsection