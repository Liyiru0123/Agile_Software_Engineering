@extends('layouts.app')

@section('title', 'Create Tag - Forum')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-[900px] px-6">
        <a href="{{ route('forum.index') }}" class="mb-6 inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A]">
            &larr; Back to Forum
        </a>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="mb-6">
                <div class="inline-flex items-center rounded-full bg-[#F3E7D8] px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-[#8B6B47]">
                    Create a Tag
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-[#4A2C2A]">Start a focused discussion board.</h1>
                <p class="mt-3 max-w-2xl leading-7 text-[#8B6B47]">
                    Use tags to group posts around one learning topic, such as essay structure, shadowing practice, or reading strategies.
                </p>
            </div>

            <form method="POST" action="{{ route('forum.tags.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="tag-name" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Tag Name</label>
                    <input id="tag-name" name="name" type="text" maxlength="80" value="{{ old('name') }}" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="For example: IELTS Writing">
                </div>

                <div>
                    <label for="tag-description" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Description</label>
                    <textarea id="tag-description" name="description" rows="4" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="What kind of discussion belongs here?">{{ old('description') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                        Create Tag
                    </button>
                    <a href="{{ route('forum.index') }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
