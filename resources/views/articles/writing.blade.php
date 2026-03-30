@extends('layouts.app')

@section('title', $article->title.' - Writing Training')

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
                    This page provides a summary-plus-response writing structure to help learners build a clear written response around the article. Drafts are stored locally in the browser.
                </p>

                <div class="rounded-3xl bg-[#FBF7F1] border border-[#EEE2D4] p-6 mb-6">
                    <div class="text-xs font-semibold uppercase tracking-[0.15em] text-[#6B3D2E] mb-2">Task</div>
                    <div class="text-[#3A2A22] leading-7 mb-3">{{ $writingTask['instruction'] }}</div>
                    <div class="text-sm text-[#6B3D2E] leading-6 mb-3">{{ $writingTask['requirement'] }}</div>
                    <div class="rounded-2xl bg-white px-4 py-3 text-sm text-[#6B3D2E] leading-6 border border-[#EEE2D4]">
                        <strong>Source excerpt:</strong> {{ $writingTask['source_text'] }}
                    </div>
                </div>

                <textarea id="writing-draft"
                          rows="16"
                          class="w-full rounded-3xl border border-[#D9C7B5] px-5 py-4 text-[#3A2A22] leading-7 focus:outline-none focus:border-[#6B3D2E]"
                          placeholder="Write your summary and response here..."></textarea>

                <div class="flex flex-wrap items-center justify-between gap-3 mt-4">
                    <div id="writing-count" class="text-sm text-[#6B3D2E]">
                        Word count: 0
                    </div>
                    <button id="save-writing-draft" class="px-4 py-3 rounded-2xl bg-[#6B3D2E] text-white font-semibold">
                        Save draft locally
                    </button>
                </div>
            </section>

            <aside class="space-y-6 sticky top-24">
                <div class="bg-white rounded-3xl border border-[#E0D2C2] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#4A2C2A] mb-4">Writing Checklist</h2>
                    <ul class="space-y-3 text-sm text-[#6B3D2E] leading-6">
                        <li>1. Open with one sentence that introduces the topic and main claim.</li>
                        <li>2. Use 2-3 sentences to summarize the key supporting points.</li>
                        <li>3. End with your evaluation, application, or reflection.</li>
                        <li>4. Keep the response within {{ $writingTask['word_limit']['min'] }}-{{ $writingTask['word_limit']['max'] }} words.</li>
                    </ul>
                </div>

                <div class="bg-[#4A2C2A] text-white rounded-3xl p-6 shadow-lg">
                    <h2 class="text-2xl font-bold mb-3">Next Step</h2>
                    <p class="text-sm text-[#F5E6D3]/80 leading-6">
                        This page already supports drafting and local save. If you want to extend it next, the same Gemini scoring pattern can be reused for writing evaluation.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const draftKey = @json('writing-draft-'.$article->id);
const writingDraft = document.getElementById('writing-draft');
const writingCount = document.getElementById('writing-count');

writingDraft.value = localStorage.getItem(draftKey) || '';
updateWordCount();

writingDraft.addEventListener('input', updateWordCount);

document.getElementById('save-writing-draft').addEventListener('click', () => {
    localStorage.setItem(draftKey, writingDraft.value);
});

function updateWordCount() {
    const words = (writingDraft.value.match(/[A-Za-z0-9']+/g) || []).length;
    writingCount.textContent = `Word count: ${words}`;
}
</script>
@endpush
