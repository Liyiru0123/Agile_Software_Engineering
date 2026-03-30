@extends('layouts.app')

@section('title', $article->title.' - Reading Training')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-6xl mx-auto px-6">
        <a href="{{ route('articles.show', $article) }}" class="inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A] mb-6">
            Back to Article
        </a>

        <div class="grid lg:grid-cols-[minmax(0,1.2fr)_360px] gap-8 items-start">
            <section class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-8">
                <h1 class="text-3xl font-bold text-[#4A2C2A] mb-3">{{ $article->title }}</h1>
                <p class="text-[#6B3D2E] leading-7 mb-8">
                    Read the article first, then focus on the academic keywords and the main idea. This page keeps the full text available for close reading, annotation, and comprehension work.
                </p>

                <div class="space-y-6 text-[#3A2A22] leading-8 text-[17px]">
                    @foreach($paragraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            </section>

            <aside class="space-y-6 sticky top-24">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">Key Vocabulary</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($keywords as $keyword)
                            <span class="px-3 py-2 rounded-full bg-[#F3E7D8] text-[#4A2C2A] text-sm font-medium">{{ $keyword }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">Reading Task</h2>

                    @if($readingExercise)
                        <div class="text-sm text-[#6B3D2E] leading-6 mb-4">{{ $readingExercise->question_data['question'] ?? 'Answer the question below.' }}</div>
                        <div class="space-y-3">
                            @foreach(($readingExercise->question_data['options'] ?? []) as $option)
                                <label class="flex items-start gap-3 rounded-2xl border border-[#E8D9C9] px-4 py-3 cursor-pointer">
                                    <input type="radio" name="reading-answer" value="{{ $option['key'] }}" class="mt-1">
                                    <span class="text-sm text-[#3A2A22] leading-6"><strong>{{ $option['key'] }}.</strong> {{ $option['text'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button id="show-reading-answer" class="w-full mt-4 px-4 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold">
                            Check answer
                        </button>
                        <div id="reading-answer-feedback" class="hidden mt-4 rounded-2xl px-4 py-3 text-sm"></div>
                    @else
                        <ul class="space-y-3 text-sm text-[#6B3D2E] leading-6">
                            @foreach($readingQuestions as $question)
                                <li>{{ $loop->iteration }}. {{ $question }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@if($readingExercise)
    @push('scripts')
    <script>
    document.getElementById('show-reading-answer').addEventListener('click', () => {
        const selected = document.querySelector('input[name="reading-answer"]:checked');
        const feedback = document.getElementById('reading-answer-feedback');
        const correct = @json($readingExercise->answer);

        feedback.classList.remove('hidden');

        if (!selected) {
            feedback.className = 'mt-4 rounded-2xl px-4 py-3 text-sm bg-amber-50 text-amber-800';
            feedback.textContent = 'Select an option first.';
            return;
        }

        if (selected.value === correct) {
            feedback.className = 'mt-4 rounded-2xl px-4 py-3 text-sm bg-emerald-50 text-emerald-800';
            feedback.textContent = `Correct. The answer is ${correct}.`;
            return;
        }

        feedback.className = 'mt-4 rounded-2xl px-4 py-3 text-sm bg-red-50 text-red-800';
        feedback.textContent = `Not quite. The correct answer is ${correct}.`;
    });
    </script>
    @endpush
@endif
