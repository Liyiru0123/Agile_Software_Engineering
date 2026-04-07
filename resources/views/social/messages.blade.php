@extends('layouts.app')

@section('title', 'Inbox')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-7xl px-6 space-y-8">
        <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Social</div>
                    <h1 class="mt-2 text-4xl font-black tracking-tight text-[#4A2C2A]">Inbox</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-[#6B3D2E]">
                        Continue conversations with your friends and keep your study discussions in one place.
                    </p>
                </div>
                <a href="{{ route('friends.index') }}" class="rounded-2xl border border-[#D9C7B5] px-5 py-3 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                    Manage Friends
                </a>
            </div>

            @if(session('social_status'))
                <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('social_status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-[#E6D3BC] bg-white shadow-sm">
            <div class="grid min-h-[72vh] lg:grid-cols-[340px_minmax(0,1fr)]">
                <aside class="border-b border-[#F0E4D7] bg-[#FCF8F3] lg:border-b-0 lg:border-r">
                    <div class="border-b border-[#F0E4D7] px-6 py-5">
                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Private Messages</div>
                        <h2 class="mt-2 text-2xl font-black text-[#4A2C2A]">Recent Messages</h2>
                    </div>

                    <div class="max-h-[72vh] space-y-2 overflow-y-auto p-3">
                    @forelse($conversations as $item)
                        @php
                            $conversation = $item['conversation'];
                            $other = $item['other'];
                            $latest = $conversation->latestMessage;
                            $isActive = $selectedConversation && $selectedConversation->id === $conversation->id;
                            $avatarText = mb_strtoupper(trim(mb_substr($other?->name ?? 'U', 0, 2)));
                        @endphp
                        <a href="{{ route('messages.show', $conversation) }}"
                           class="block rounded-[1.5rem] border px-4 py-4 transition {{ $isActive ? 'border-[#4A2C2A] bg-[#F3E7D8]' : 'border-[#E6D3BC] bg-white hover:border-[#C9A961] hover:bg-[#FFF8F0]' }}">
                            <div class="flex items-center gap-4">
                                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#D4B970] text-base font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                                    {{ $avatarText }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="truncate text-base font-black text-[#4A2C2A]">
                                            {{ $other?->name ?? 'Unknown friend' }}
                                        </div>
                                        @if($item['has_unread'])
                                            <span class="mt-1 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-[#D35D47]"></span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-[11px] uppercase tracking-[0.14em] text-[#8B6B47]">
                                        {{ optional($latest?->created_at)->diffForHumans() ?? 'Ready' }}
                                    </div>
                                    <div class="mt-2 text-sm leading-6 text-[#6B3D2E] forum-preview-clamp">
                                        {{ $latest?->body ? \Illuminate\Support\Str::limit($latest->body, 88) : 'No messages yet.' }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-white px-5 py-8 text-center text-sm text-[#8B6B47]">
                            No conversations yet. Add a friend from the forum and start chatting.
                        </div>
                    @endforelse
                    </div>
                </aside>

                <div class="flex min-h-[72vh] flex-col bg-white">
                    @if($selectedConversation && $selectedFriend)
                        @php
                            $avatarText = mb_strtoupper(trim(mb_substr($selectedFriend->name ?? 'U', 0, 2)));
                        @endphp
                        <div class="border-b border-[#F0E4D7] px-8 py-5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex min-w-0 items-center gap-4">
                                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#D4B970] text-base font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                                        {{ $avatarText }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Direct Message</div>
                                        <h2 class="mt-1 truncate text-2xl font-black text-[#4A2C2A]">{{ $selectedFriend->name }}</h2>
                                    </div>
                                </div>
                                <a href="{{ route('friends.index') }}" class="rounded-xl border border-[#D9C7B5] px-4 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                    Friends
                                </a>
                            </div>
                        </div>

                        <div class="flex-1 space-y-5 overflow-y-auto bg-[#FFFDFC] px-6 py-6 sm:px-8">
                        @forelse($messages as $message)
                            @php
                                $isMine = $message->sender_id === auth()->id();
                                $messageAvatar = mb_strtoupper(trim(mb_substr($message->sender?->name ?? 'U', 0, 2)));
                            @endphp
                            <div class="{{ $isMine ? 'text-right' : 'text-left' }}">
                                <div class="mb-2 text-xs text-[#8B6B47]">
                                    {{ optional($message->created_at)->format('Y-m-d H:i') }}
                                </div>
                                <div class="flex items-end gap-3 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    @unless($isMine)
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#E6D3BC] text-xs font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                                            {{ $messageAvatar }}
                                        </span>
                                    @endunless
                                    <article class="max-w-[min(42rem,85%)] rounded-[1.75rem] px-5 py-4 shadow-sm {{ $isMine ? 'bg-[#4A2C2A] text-white' : 'border border-[#E6D3BC] bg-white text-[#3A2A22]' }}">
                                        <div class="text-xs font-semibold uppercase tracking-[0.14em] {{ $isMine ? 'text-white/70' : 'text-[#8B6B47]' }}">
                                            {{ $isMine ? 'You' : ($message->sender?->name ?? 'Unknown') }}
                                        </div>
                                        <div class="mt-2 whitespace-pre-line text-sm leading-7">{{ $message->body }}</div>
                                    </article>
                                    @if($isMine)
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#D4B970] text-xs font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                                            {{ $messageAvatar }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="flex h-full min-h-[18rem] items-center justify-center">
                                <div class="w-full max-w-md rounded-[1.75rem] border border-dashed border-[#D8C3A6] bg-[#FBF7F1] px-6 py-10 text-center text-sm text-[#8B6B47]">
                                    No messages yet. Start the conversation below.
                                </div>
                            </div>
                        @endforelse
                        </div>

                        <div class="border-t border-[#F0E4D7] bg-white px-6 py-5 sm:px-8">
                            <form method="POST" action="{{ route('messages.store', $selectedConversation) }}" class="space-y-4">
                                @csrf
                                <textarea name="body" rows="4" maxlength="2000" class="w-full rounded-[1.5rem] border border-[#D9C7B5] px-5 py-4 leading-7 text-[#3A2A22] focus:border-[#6B3D2E] focus:outline-none" placeholder="Write a private message to {{ $selectedFriend->name }}...">{{ old('body') }}</textarea>
                                <div class="flex items-center justify-between gap-4">
                                    <div class="text-xs leading-6 text-[#8B6B47]">
                                        Keep the discussion focused, respectful, and helpful.
                                    </div>
                                    <button type="submit" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="flex flex-1 items-center justify-center px-8 py-16 text-center">
                            <div class="max-w-lg">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#F3E7D8] text-lg font-black uppercase tracking-[0.18em] text-[#6B3D2E]">
                                    DM
                                </div>
                                <div class="mt-5 text-2xl font-black text-[#4A2C2A]">No conversation selected</div>
                                <p class="mt-3 text-sm leading-7 text-[#6B3D2E]">
                                    Choose a conversation on the left, or go to your friends page and click a friend to open a private chat.
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
