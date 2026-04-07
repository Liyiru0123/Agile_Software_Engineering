@extends('layouts.app')

@section('title', 'Create Post - Forum')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-[1000px] px-6">
        <a href="{{ route('forum.index', ['tag' => $selectedTag?->slug ?? $publicTag->slug]) }}" class="mb-6 inline-flex items-center text-sm text-[#6B3D2E] hover:text-[#4A2C2A]">
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
                    Create a Post
                </div>
                <h1 class="mt-4 text-4xl font-black tracking-tight text-[#4A2C2A]">Share a study reflection, question, or idea.</h1>
                <p class="mt-3 max-w-2xl leading-7 text-[#8B6B47]">
                    Every post belongs to a tag. If you do not choose a specific topic, it will be published under Public Forum by default.
                </p>
            </div>

            <form method="POST" action="{{ route('forum.posts.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label for="post-title" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Title</label>
                    <input id="post-title" name="title" type="text" maxlength="160" value="{{ old('title') }}" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="Share a learning insight or ask a question">
                </div>

                <div>
                    <label for="forum-tag-id" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Tag</label>
                    <select id="forum-tag-id" name="forum_tag_id" class="w-full rounded-2xl border border-[#D9C7B5] px-4 py-3 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" @selected((int) old('forum_tag_id', $selectedTag?->id) === $tag->id)>#{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="post-body" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">Post Content</label>
                    <textarea id="post-body" name="body" rows="10" class="w-full rounded-[1.5rem] border border-[#D9C7B5] px-5 py-4 leading-7 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="Write your learning reflection, question, or study note here.">{{ old('body') }}</textarea>
                </div>

                @include('forum.partials.photo-upload', [
                    'id' => 'post-attachments',
                    'name' => 'attachments[]',
                    'label' => 'Photos',
                    'buttonLabel' => 'Choose Photos',
                    'emptyText' => 'No photos selected',
                    'helperText' => 'Optional. Upload JPG, PNG, GIF, or WEBP images. Maximum 5 MB per image.',
                ])

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-2xl bg-[#6B3D2E] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#4A2C2A]">
                        Publish Post
                    </button>
                    <a href="{{ route('forum.index', ['tag' => $selectedTag?->slug ?? $publicTag->slug]) }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                        Cancel
                    </a>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
