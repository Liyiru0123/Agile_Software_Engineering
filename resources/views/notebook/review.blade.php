@extends('layouts.app')

@section('title', 'Notebook Review')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="max-w-5xl mx-auto px-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-[#4A2C2A]">Notebook Review</h1>
                <p class="mt-2 text-[#6B3D2E]">Review your saved excerpts one by one with their translation and source article.</p>
            </div>
            <a href="{{ route('notebook.index') }}" class="inline-flex items-center rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-white transition">
                Open Notebook List
            </a>
        </div>

        @if($notes->count() > 0)
            <div class="rounded-[2rem] border border-[#E0D2C2] bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div class="text-sm font-semibold text-[#8A654E]">
                        Card <span id="review-card-index">1</span> of {{ $notes->count() }}
                    </div>
                    <a id="review-article-link" href="{{ $notes[0]['article_url'] }}" class="text-sm font-semibold text-[#6B3D2E] hover:text-[#4A2C2A]">
                        {{ $notes[0]['article_title'] }}
                    </a>
                </div>

                <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6 mb-5">
                    <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Saved Excerpt</div>
                    <div id="review-selected-text" class="text-2xl font-bold leading-9 text-[#4A2C2A]">{{ $notes[0]['selected_text'] }}</div>
                </div>

                <div class="rounded-3xl bg-sky-50 border border-sky-200 p-6 mb-5">
                    <div class="text-xs uppercase tracking-[0.15em] text-sky-800 font-semibold mb-2">Translation</div>
                    <div id="review-translation" class="text-lg leading-8 text-sky-900">{{ $notes[0]['translated_text'] }}</div>
                </div>

                <div class="rounded-3xl bg-[#F8F1E7] border border-[#E8D9C9] p-6">
                    <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Source Paragraph</div>
                    <p id="review-paragraph" class="text-sm leading-7 text-[#3A2A22]">{{ $notes[0]['paragraph_text'] }}</p>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm text-[#8A654E]" id="review-created-at">
                        Saved at {{ $notes[0]['created_at'] ?? '-' }}
                    </div>
                    <div class="flex gap-3">
                        <button id="review-prev" type="button" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#4A2C2A] hover:bg-[#FBF7F1] transition">
                            Previous
                        </button>
                        <button id="review-next" type="button" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white hover:bg-[#6B3D2E] transition">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-3xl border border-dashed border-[#D9C7B5] bg-white p-10 text-center text-[#6B3D2E]">
                No saved excerpts to review yet. Highlight text in an article and save it first.
            </div>
        @endif
    </div>
</div>
@endsection

@if($notes->count() > 0)
@push('scripts')
<script>
const notebookReviewCards = @json($notes);
let notebookReviewIndex = 0;

const reviewCardIndex = document.getElementById('review-card-index');
const reviewArticleLink = document.getElementById('review-article-link');
const reviewSelectedText = document.getElementById('review-selected-text');
const reviewTranslation = document.getElementById('review-translation');
const reviewParagraph = document.getElementById('review-paragraph');
const reviewCreatedAt = document.getElementById('review-created-at');
const reviewPrev = document.getElementById('review-prev');
const reviewNext = document.getElementById('review-next');

reviewPrev.addEventListener('click', () => {
    notebookReviewIndex = (notebookReviewIndex - 1 + notebookReviewCards.length) % notebookReviewCards.length;
    renderNotebookReviewCard();
});

reviewNext.addEventListener('click', () => {
    notebookReviewIndex = (notebookReviewIndex + 1) % notebookReviewCards.length;
    renderNotebookReviewCard();
});

function renderNotebookReviewCard() {
    const card = notebookReviewCards[notebookReviewIndex];

    reviewCardIndex.textContent = notebookReviewIndex + 1;
    reviewArticleLink.textContent = card.article_title;
    reviewArticleLink.href = card.article_url;
    reviewSelectedText.textContent = card.selected_text;
    reviewTranslation.textContent = card.translated_text;
    reviewParagraph.textContent = card.paragraph_text;
    reviewCreatedAt.textContent = `Saved at ${card.created_at || '-'}`;
}
</script>
@endpush
@endif
