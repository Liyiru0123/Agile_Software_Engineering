@extends('layouts.app')

@section('title', 'Friends')

@section('content')
<div class="min-h-screen bg-[#F6F0E8] py-10">
    <div class="mx-auto max-w-6xl px-6 space-y-8">
        <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.16em] text-[#8B6B47]">Social</div>
                    <h1 class="mt-2 text-4xl font-black tracking-tight text-[#4A2C2A]">Friends</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-[#6B3D2E]">
                        Manage incoming requests, keep track of the requests you have sent, and open direct messages with your friends.
                    </p>
                </div>
                <a href="{{ route('messages.index') }}" class="rounded-2xl bg-[#4A2C2A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                    Open Inbox
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

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)]">
            <div class="rounded-[2rem] border border-[#E6D3BC] bg-white p-8 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-2xl font-black text-[#4A2C2A]">Your Friends</h2>
                    <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $friends->count() }}</span>
                </div>

                <p class="mt-3 text-sm leading-7 text-[#6B3D2E]">
                    Click a friend card to open a private chat. Use the button on the right only when you want to remove that friend.
                </p>

                <div class="mt-6 space-y-4">
                    @forelse($friends as $friend)
                        @php
                            $conversationId = $conversationMap[\App\Models\Conversation::directKeyFor(auth()->id(), $friend->id)] ?? null;
                            $avatarText = mb_strtoupper(trim(mb_substr($friend->name ?? 'U', 0, 2)));
                        @endphp
                        <article class="group flex flex-wrap items-center gap-3 rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] p-3 transition hover:border-[#C9A961] hover:bg-[#FFF8F0]">
                            <div class="min-w-0 flex-1">
                                @if($conversationId)
                                    <a href="{{ route('messages.show', $conversationId) }}" class="flex min-w-0 items-center gap-4 rounded-[1.25rem] px-3 py-3 transition group-hover:bg-white/70">
                                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#D4B970] text-base font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                                            {{ $avatarText }}
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-lg font-black text-[#4A2C2A]">{{ $friend->name }}</span>
                                            <span class="mt-1 block truncate text-sm text-[#8B6B47]">{{ $friend->email }}</span>
                                            <span class="mt-2 inline-flex rounded-full bg-[#F3E7D8] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#6B3D2E]">
                                                Open chat
                                            </span>
                                        </span>
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('messages.start') }}">
                                        @csrf
                                        <input type="hidden" name="recipient_id" value="{{ $friend->id }}">
                                        <button type="submit" class="flex w-full min-w-0 items-center gap-4 rounded-[1.25rem] px-3 py-3 text-left transition group-hover:bg-white/70">
                                            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#D4B970] text-base font-black uppercase tracking-[0.08em] text-[#4A2C2A]">
                                                {{ $avatarText }}
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-lg font-black text-[#4A2C2A]">{{ $friend->name }}</span>
                                                <span class="mt-1 block truncate text-sm text-[#8B6B47]">{{ $friend->email }}</span>
                                                <span class="mt-2 inline-flex rounded-full bg-[#F3E7D8] px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[#6B3D2E]">
                                                    Start chat
                                                </span>
                                            </span>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('friends.destroy', $friend) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                    Remove
                                </button>
                            </form>
                        </article>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FBF7F1] px-6 py-10 text-center text-[#8B6B47]">
                            No friends yet. Send a request from the forum to start connecting with other learners.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-[#4A2C2A]">Incoming Requests</h2>
                        <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $incomingRequests->count() }}</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse($incomingRequests as $friendRequest)
                            <article class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] px-4 py-4">
                                <div class="text-sm font-semibold text-[#4A2C2A]">{{ $friendRequest->sender?->name ?? 'Unknown user' }}</div>
                                <div class="mt-1 text-xs text-[#8B6B47]">{{ optional($friendRequest->created_at)->diffForHumans() }}</div>
                                @if($friendRequest->message)
                                    <div class="mt-3 text-sm leading-6 text-[#6B3D2E]">{{ $friendRequest->message }}</div>
                                @endif
                                <div class="mt-4 flex flex-wrap gap-3">
                                    <form method="POST" action="{{ route('friends.requests.accept', $friendRequest) }}">
                                        @csrf
                                        <button type="submit" class="rounded-xl bg-[#4A2C2A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]">
                                            Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('friends.requests.reject', $friendRequest) }}">
                                        @csrf
                                        <button type="submit" class="rounded-xl border border-[#D9C7B5] px-4 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:bg-[#F3E7D8]">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FBF7F1] px-5 py-8 text-center text-sm text-[#8B6B47]">
                                No pending incoming requests.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-[2rem] border border-[#E6D3BC] bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-black text-[#4A2C2A]">Sent Requests</h2>
                        <span class="rounded-full bg-[#F3E7D8] px-3 py-1 text-xs font-bold text-[#6B3D2E]">{{ $outgoingRequests->count() }}</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse($outgoingRequests as $friendRequest)
                            <article class="rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] px-4 py-4">
                                <div class="text-sm font-semibold text-[#4A2C2A]">{{ $friendRequest->receiver?->name ?? 'Unknown user' }}</div>
                                <div class="mt-1 text-xs text-[#8B6B47]">{{ optional($friendRequest->created_at)->diffForHumans() }}</div>
                                @if($friendRequest->message)
                                    <div class="mt-3 text-sm leading-6 text-[#6B3D2E]">{{ $friendRequest->message }}</div>
                                @endif
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('friends.requests.cancel', $friendRequest) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                            Cancel Request
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-[1.5rem] border border-dashed border-[#D8C3A6] bg-[#FBF7F1] px-5 py-8 text-center text-sm text-[#8B6B47]">
                                No outgoing requests right now.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>
@endsection
