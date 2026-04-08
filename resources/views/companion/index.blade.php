@extends('layouts.app')

@section('title', 'Companion')

@push('styles')
<style>
    #companion-shell {
        display: none !important;
    }

    .game-shop-open #companion-shop-drawer {
        transform: translateX(0);
        opacity: 1;
        pointer-events: auto;
    }

    .game-shop-open #companion-shop-backdrop {
        opacity: 1;
        pointer-events: auto;
    }

    .companion-main-bubble-visible {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@section('content')
@php
    $outfits = collect($shopItems)->where('type', 'outfit')->values();
    $items = collect($shopItems)->where('type', '!=', 'outfit')->values();
@endphp
<div id="companion-game-page" class="relative overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(255,247,236,0.98),_rgba(241,214,181,0.88)_22%,_rgba(204,146,110,0.86)_52%,_rgba(90,54,46,0.96)_100%)] min-h-[calc(100vh-88px)]">
    <div class="absolute inset-0 bg-[linear-gradient(140deg,rgba(255,255,255,0.16),transparent_32%,rgba(74,44,42,0.14)_68%,rgba(255,255,255,0.08))]"></div>
    <div class="absolute inset-x-0 bottom-0 h-48 bg-[radial-gradient(circle_at_center,_rgba(255,238,210,0.28),rgba(74,44,42,0)_72%)]"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
        <div class="flex items-start justify-between gap-4">
            <div class="rounded-[1.75rem] border border-white/20 bg-white/12 backdrop-blur-md px-5 py-4 shadow-[0_24px_50px_rgba(44,24,16,0.18)]">
                <div class="text-[11px] font-bold uppercase tracking-[0.22em] text-white/75">Hiyori Companion Room</div>
                <h1 class="mt-2 text-3xl sm:text-4xl font-black text-white leading-tight">Hiyori's Room</h1>
                <p class="mt-3 max-w-xl text-sm sm:text-base leading-7 text-white/82">
                    Train, collect gold, and build up Hiyori's wardrobe and item shelf. This page is your main companion scene.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="rounded-[1.5rem] border border-white/20 bg-[#4A2C2A]/75 backdrop-blur-md px-4 py-3 text-[#F5E6D3] shadow-[0_16px_40px_rgba(44,24,16,0.22)]">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-[#D4B970] font-bold">Gold</div>
                    <div class="mt-1 text-2xl font-black">{{ number_format($profile->gold) }}</div>
                </div>
                <button id="open-companion-shop" type="button" class="rounded-[1.5rem] border border-white/20 bg-white/14 backdrop-blur-md px-5 py-3 text-sm font-bold uppercase tracking-[0.16em] text-white shadow-[0_16px_40px_rgba(44,24,16,0.22)] hover:bg-white/18 transition">
                    Open Shop
                </button>
            </div>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50/95 px-4 py-3 text-sm text-emerald-700 shadow-lg">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50/95 px-4 py-3 text-sm text-red-700 shadow-lg">
                {{ session('error') }}
            </div>
        @endif

        <section class="mt-8 grid lg:grid-cols-[1fr_320px] gap-6 items-start">
            <div class="relative min-h-[700px] rounded-[2.25rem] border border-white/18 bg-[linear-gradient(180deg,rgba(255,255,255,0.18)_0%,rgba(255,255,255,0.06)_36%,rgba(74,44,42,0.08)_100%)] backdrop-blur-md shadow-[0_35px_80px_rgba(44,24,16,0.22)] overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.26),rgba(255,255,255,0.04)_40%,rgba(74,44,42,0.08)_100%)]"></div>
                <div class="absolute left-8 right-8 bottom-0 h-40 rounded-t-[50%] bg-[radial-gradient(circle_at_center,_rgba(255,247,227,0.88),rgba(255,240,216,0.42)_45%,rgba(255,240,216,0)_76%)]"></div>

                <div class="absolute top-6 left-1/2 -translate-x-1/2 w-[min(28rem,calc(100%-2rem))]">
                    <div id="companion-main-bubble" class="rounded-[1.75rem] border border-white/30 bg-white/92 px-5 py-4 text-sm leading-7 text-[#4A2C2A] shadow-[0_20px_45px_rgba(44,24,16,0.16)] opacity-0 -translate-y-2 transition duration-300"></div>
                </div>

                <div class="absolute left-6 top-6 rounded-[1.5rem] border border-white/18 bg-white/14 backdrop-blur-md px-4 py-4 max-w-[220px] text-white shadow-[0_16px_40px_rgba(44,24,16,0.16)]">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-white/70 font-bold">Current Look</div>
                    <div class="mt-2 text-xl font-black">{{ $profile->equippedItem?->name ?? 'Default Look' }}</div>
                    <div class="mt-2 text-sm leading-6 text-white/80">Outfits update Hiyori's profile state now. Visible costume switching can be added once more model assets are available.</div>
                </div>

                <div class="absolute right-6 top-6 rounded-[1.5rem] border border-white/18 bg-[#4A2C2A]/70 backdrop-blur-md px-4 py-4 max-w-[240px] text-[#F5E6D3] shadow-[0_16px_40px_rgba(44,24,16,0.2)]">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-[#D4B970] font-bold">Daily Reward</div>
                    <div class="mt-2 text-xl font-black">{{ $dailyRewardAmount }} Gold</div>
                    <div class="mt-2 text-sm leading-6 text-[#F5E6D3]/82">
                        {{ $claimedDailyReward ? 'Claimed for today.' : 'Available on your next day login.' }}
                    </div>
                </div>

                <div id="companion-main-stage" class="absolute inset-x-0 bottom-0 top-20 cursor-pointer select-none"></div>
                <div id="companion-main-stage-fallback" class="absolute inset-x-0 bottom-16 text-center text-sm text-white/85">
                    Loading Hiyori scene...
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-[1.75rem] border border-white/18 bg-white/14 backdrop-blur-md p-5 text-white shadow-[0_20px_45px_rgba(44,24,16,0.18)]">
                    <div class="text-[11px] uppercase tracking-[0.18em] text-white/68 font-bold">Progress</div>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl bg-white/10 px-4 py-4">
                            <div class="text-[11px] uppercase tracking-[0.14em] text-white/68">Gold</div>
                            <div class="mt-2 text-2xl font-black">{{ number_format($profile->gold) }}</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 px-4 py-4">
                            <div class="text-[11px] uppercase tracking-[0.14em] text-white/68">Owned</div>
                            <div class="mt-2 text-2xl font-black">{{ count(array_filter($shopItems, fn ($item) => $item['owned'])) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 rounded-2xl bg-white/10 px-4 py-4">
                        <div class="text-[11px] uppercase tracking-[0.14em] text-white/68">How To Earn</div>
                        <ul class="mt-3 space-y-2 text-sm leading-6 text-white/82">
                            <li>Finish listening, reading, writing, and speaking modules.</li>
                            <li>Claim one daily login reward per day.</li>
                            <li>Use the shop drawer to buy outfits and items.</li>
                        </ul>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-white/18 bg-[#4A2C2A]/72 backdrop-blur-md p-5 text-[#F5E6D3] shadow-[0_20px_45px_rgba(44,24,16,0.2)]">
                    <div class="text-[11px] uppercase tracking-[0.18em] text-[#D4B970] font-bold">Recent Gold Activity</div>
                    <div class="mt-4 space-y-3">
                        @forelse($transactions as $transaction)
                            <div class="rounded-2xl bg-white/8 px-4 py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-sm font-semibold">{{ ucwords($transaction['source']) }}</div>
                                        <div class="mt-1 text-xs text-[#F5E6D3]/68">{{ $transaction['created_at'] }}</div>
                                    </div>
                                    <div class="text-sm font-black {{ $transaction['amount'] >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                        {{ $transaction['amount'] >= 0 ? '+' : '' }}{{ $transaction['amount'] }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-white/18 px-4 py-5 text-sm text-[#F5E6D3]/76">
                                No gold transactions yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>
        </section>

        <section class="mt-8 rounded-[2rem] border border-white/18 bg-[linear-gradient(180deg,rgba(255,255,255,0.16)_0%,rgba(255,255,255,0.08)_100%)] backdrop-blur-md shadow-[0_28px_65px_rgba(44,24,16,0.2)] overflow-hidden">
            <div class="grid lg:grid-cols-[0.95fr_1.05fr] gap-0">
                <div class="px-6 py-6 sm:px-8 border-b lg:border-b-0 lg:border-r border-white/14">
                    <div class="text-[11px] uppercase tracking-[0.2em] text-white/72 font-bold">Voice Prototype</div>
                    <h2 class="mt-3 text-3xl font-black text-white">Talk To Hiyori</h2>
                    <p class="mt-3 text-sm sm:text-base leading-7 text-white/80 max-w-xl">
                        This is a frontend-only prototype for voice chat. The browser handles speech recognition and speech synthesis locally. No backend conversation service is connected yet.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button id="companion-voice-start" type="button" class="rounded-[1.25rem] bg-[#F5E6D3] px-5 py-3 text-sm font-bold uppercase tracking-[0.14em] text-[#4A2C2A] shadow-lg hover:bg-white transition">
                            Start Listening
                        </button>
                        <button id="companion-voice-stop" type="button" class="rounded-[1.25rem] border border-white/20 bg-white/10 px-5 py-3 text-sm font-bold uppercase tracking-[0.14em] text-white hover:bg-white/14 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Stop
                        </button>
                        <button id="companion-voice-repeat" type="button" class="rounded-[1.25rem] border border-white/20 bg-white/10 px-5 py-3 text-sm font-bold uppercase tracking-[0.14em] text-white hover:bg-white/14 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Repeat Reply
                        </button>
                    </div>

                    <div class="mt-5 rounded-[1.5rem] border border-white/18 bg-[#4A2C2A]/45 px-4 py-4 text-[#F5E6D3] shadow-[0_14px_35px_rgba(44,24,16,0.16)]">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="text-[11px] uppercase tracking-[0.16em] text-[#D4B970] font-bold">Status</div>
                                <div id="companion-voice-status" class="mt-2 text-sm leading-6 text-[#F5E6D3]">Idle. Press Start Listening to begin.</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-300/70"></span>
                                <span class="text-xs uppercase tracking-[0.14em] text-[#F5E6D3]/70">Browser Voice</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8 grid md:grid-cols-2 gap-4">
                    <div class="rounded-[1.5rem] border border-white/16 bg-white/14 p-5 shadow-[0_18px_38px_rgba(44,24,16,0.14)]">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-white/72 font-bold">You Said</div>
                        <div id="companion-voice-transcript" class="mt-4 min-h-[150px] text-sm sm:text-base leading-7 text-white/88">
                            Waiting for microphone input.
                        </div>
                    </div>
                    <div class="rounded-[1.5rem] border border-white/16 bg-[#FFF8F0]/88 p-5 shadow-[0_18px_38px_rgba(44,24,16,0.14)]">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-[#8B6B47] font-bold">Hiyori Reply</div>
                        <div id="companion-voice-response" class="mt-4 min-h-[150px] text-sm sm:text-base leading-7 text-[#4A2C2A]">
                            I am ready. Ask about studying, gold, the shop, or just say hello.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div id="companion-shop-backdrop" class="fixed inset-0 z-[70] bg-[#2C1810]/50 opacity-0 pointer-events-none transition duration-300"></div>
    <aside id="companion-shop-drawer" class="fixed top-[88px] right-0 bottom-0 z-[75] w-[min(420px,100vw)] translate-x-full opacity-0 pointer-events-none transition duration-300">
        <div class="h-full border-l border-[#E8D7C4] bg-[linear-gradient(180deg,#FFF8F0_0%,#F3E4D8_100%)] shadow-[-24px_0_60px_rgba(44,24,16,0.22)] flex flex-col">
            <div class="px-6 py-5 border-b border-[#E8D7C4] flex items-start justify-between gap-4">
                <div>
                    <div class="text-[11px] uppercase tracking-[0.18em] text-[#8B6B47] font-bold">Shop</div>
                    <h2 class="mt-2 text-2xl font-black text-[#4A2C2A]">Hiyori Boutique</h2>
                    <p class="mt-2 text-sm leading-6 text-[#8B6B47]">Buy outfits and companion items with your current gold balance.</p>
                </div>
                <div class="text-right">
                    <div class="text-[11px] uppercase tracking-[0.14em] text-[#8B6B47] font-bold">Balance</div>
                    <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ number_format($profile->gold) }}</div>
                    <button id="close-companion-shop" type="button" class="mt-3 rounded-full border border-[#D8C3A6] bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-[0.14em] text-[#6B3D2E] hover:bg-[#FBF6EF] transition">
                        Close
                    </button>
                </div>
            </div>

            <div class="px-6 pt-5 pb-3 flex items-center gap-2">
                <button type="button" data-companion-tab="outfit" class="companion-tab-button rounded-full bg-[#4A2C2A] px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-[#F5E6D3]">Outfits</button>
                <button type="button" data-companion-tab="item" class="companion-tab-button rounded-full border border-[#D8C3A6] bg-white px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-[#6B3D2E]">Items</button>
            </div>

            <div class="flex-1 overflow-y-auto px-6 pb-8 space-y-5">
                <div data-companion-panel="outfit" class="space-y-4">
                    @foreach($outfits as $item)
                        @php
                            $visual = $item['visual'] ?? [];
                            $surface = $visual['surface'] ?? '#F9EFE2';
                            $accent = $visual['accent'] ?? '#8B6B47';
                        @endphp
                        <div class="rounded-[1.6rem] border border-[#E2CFB3] p-5" style="background-color: {{ $surface }};">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-white" style="background-color: {{ $accent }};">Outfit</div>
                                    <h3 class="mt-3 text-xl font-black text-[#4A2C2A]">{{ $item['name'] }}</h3>
                                    <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">{{ $item['description'] }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-[11px] uppercase tracking-[0.14em] text-[#8B6B47]">Price</div>
                                    <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $item['price'] }}</div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2 text-xs text-[#8B6B47]">
                                <span class="rounded-full border border-[#D9C6A8] px-2.5 py-1">{{ ucfirst($item['rarity']) }}</span>
                                @if($item['owned'])
                                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">Owned</span>
                                @endif
                                @if($item['equipped'])
                                    <span class="rounded-full border border-[#C9A961] bg-[#FFF5DB] px-2.5 py-1 text-[#8B6B47]">Equipped</span>
                                @endif
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                @if(! $item['owned'])
                                    <form method="POST" action="{{ route('companion.purchase', $item['id']) }}">
                                        @csrf
                                        <button type="submit" class="rounded-2xl bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] px-4 py-3 text-sm font-semibold transition">
                                            Buy Outfit
                                        </button>
                                    </form>
                                @elseif(! $item['equipped'])
                                    <form method="POST" action="{{ route('companion.equip', $item['id']) }}">
                                        @csrf
                                        <button type="submit" class="rounded-2xl border border-[#C9A961] bg-[#FFF8ED] px-4 py-3 text-sm font-semibold text-[#6B3D2E] hover:bg-[#F6EAD8] transition">
                                            Equip
                                        </button>
                                    </form>
                                @else
                                    <div class="rounded-2xl border border-[#D8C3A6] bg-white/70 px-4 py-3 text-sm font-semibold text-[#8B6B47]">
                                        Currently equipped
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div data-companion-panel="item" class="hidden space-y-4">
                    @foreach($items as $item)
                        @php
                            $visual = $item['visual'] ?? [];
                            $surface = $visual['surface'] ?? '#F9EFE2';
                            $accent = $visual['accent'] ?? '#8B6B47';
                        @endphp
                        <div class="rounded-[1.6rem] border border-[#E2CFB3] p-5" style="background-color: {{ $surface }};">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-white" style="background-color: {{ $accent }};">Item</div>
                                    <h3 class="mt-3 text-xl font-black text-[#4A2C2A]">{{ $item['name'] }}</h3>
                                    <p class="mt-2 text-sm leading-7 text-[#6B3D2E]">{{ $item['description'] }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <div class="text-[11px] uppercase tracking-[0.14em] text-[#8B6B47]">Price</div>
                                    <div class="mt-1 text-2xl font-black text-[#4A2C2A]">{{ $item['price'] }}</div>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2 text-xs text-[#8B6B47]">
                                <span class="rounded-full border border-[#D9C6A8] px-2.5 py-1">{{ ucfirst($item['rarity']) }}</span>
                                @if($item['owned'])
                                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-emerald-700">Owned</span>
                                @endif
                            </div>

                            <div class="mt-5 flex flex-wrap gap-3">
                                @if(! $item['owned'])
                                    <form method="POST" action="{{ route('companion.purchase', $item['id']) }}">
                                        @csrf
                                        <button type="submit" class="rounded-2xl bg-[#4A2C2A] hover:bg-[#6B3D2E] text-[#F5E6D3] px-4 py-3 text-sm font-semibold transition">
                                            Buy Item
                                        </button>
                                    </form>
                                @else
                                    <div class="rounded-2xl border border-[#D8C3A6] bg-white/70 px-4 py-3 text-sm font-semibold text-[#8B6B47]">
                                        Already collected
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const stage = document.getElementById('companion-main-stage');
    const fallback = document.getElementById('companion-main-stage-fallback');
    const bubble = document.getElementById('companion-main-bubble');
    const page = document.getElementById('companion-game-page');
    const openButton = document.getElementById('open-companion-shop');
    const closeButton = document.getElementById('close-companion-shop');
    const backdrop = document.getElementById('companion-shop-backdrop');
    const tabButtons = Array.from(document.querySelectorAll('.companion-tab-button'));
    const panels = Array.from(document.querySelectorAll('[data-companion-panel]'));
    const modelUrl = @json(asset('live2d/hiyori/hiyori_free_t08.model3.json'));
    const voiceStartButton = document.getElementById('companion-voice-start');
    const voiceStopButton = document.getElementById('companion-voice-stop');
    const voiceRepeatButton = document.getElementById('companion-voice-repeat');
    const voiceStatus = document.getElementById('companion-voice-status');
    const voiceTranscript = document.getElementById('companion-voice-transcript');
    const voiceResponse = document.getElementById('companion-voice-response');
    const clickLines = [
        'Welcome back. This room looks better when you actually study.',
        'Check the shop if you want a new look for me.',
        'If you want more gold, finish one article module now.',
        'A good companion room is built one finished task at a time.',
        'You clicked me. I am awake and paying attention.',
    ];
    const idleLines = [
        'This scene is your main companion page now.',
        'Open the shop in the corner whenever you want to buy outfits or items.',
        'You can keep me here while checking your gold and inventory.',
    ];

    let app = null;
    let model = null;
    let bubbleTimer = null;
    let recognition = null;
    let isListening = false;
    let finalTranscript = '';
    let lastVoiceReply = 'I am ready. Ask about studying, gold, the shop, or just say hello.';
    let preferredVoice = null;

    function speak(text, duration = 5200) {
        if (!text) {
            return;
        }

        bubble.textContent = text;
        bubble.classList.add('companion-main-bubble-visible');
        window.clearTimeout(bubbleTimer);
        bubbleTimer = window.setTimeout(() => {
            bubble.classList.remove('companion-main-bubble-visible');
        }, duration);
    }

    function randomFrom(list) {
        return list[Math.floor(Math.random() * list.length)];
    }
    function setVoiceStatus(message) {
        voiceStatus.textContent = message;
    }

    function buildVoiceReply(transcript) {
        const normalized = transcript.toLowerCase();

        if (normalized.includes('hello') || normalized.includes('hi ')) {
            return 'Hello. I am here. If you want more gold, pick one study task and finish it.';
        }

        if (normalized.includes('gold') || normalized.includes('coin')) {
            return 'Gold comes from daily login rewards and from finishing article modules. Use the shop drawer when you want to spend it.';
        }

        if (normalized.includes('shop') || normalized.includes('buy') || normalized.includes('outfit') || normalized.includes('item')) {
            return 'Open the shop in the corner. Outfits change my equipped look in the profile, and items expand the collection.';
        }

        if (normalized.includes('listen')) {
            return 'If listening feels difficult, replay one short segment and focus on keywords before replaying the full passage.';
        }

        if (normalized.includes('read')) {
            return 'For reading, try finishing one paragraph, then paraphrase it aloud before moving on.';
        }

        if (normalized.includes('write')) {
            return 'For writing, draft fast first, then revise only after the main idea is already on the page.';
        }

        if (normalized.includes('speak') || normalized.includes('shadowing')) {
            return 'If you want to speak better, keep the clip short and imitate rhythm before chasing perfect accuracy.';
        }

        if (normalized.includes('tired') || normalized.includes('busy')) {
            return 'Then do the smallest useful thing. One short finished task is still progress.';
        }

        if (normalized.includes('bye')) {
            return 'All right. I will stay here until you come back.';
        }

        return 'I heard you. This frontend prototype can already listen and reply locally, but the deeper conversation logic still needs a backend later.';
    }

    function pickPreferredVoice() {
        if (!('speechSynthesis' in window)) {
            return null;
        }

        const voices = window.speechSynthesis.getVoices();
        if (!voices.length) {
            return null;
        }

        const preferredPatterns = [
            /aria/i,
            /ava/i,
            /samantha/i,
            /allison/i,
            /zoe/i,
            /emma/i,
            /jenny/i,
            /female/i,
            /woman/i,
            /girl/i,
        ];

        const englishVoices = voices.filter((voice) => /^en(-|_)/i.test(voice.lang || ''));
        const femaleEnglish = englishVoices.find((voice) =>
            preferredPatterns.some((pattern) => pattern.test(voice.name || ''))
        );

        preferredVoice = femaleEnglish
            || englishVoices.find((voice) => /en-us/i.test(voice.lang || ''))
            || englishVoices[0]
            || voices[0]
            || null;

        return preferredVoice;
    }

    function speakOutLoud(text) {
        if (!('speechSynthesis' in window) || !text) {
            return;
        }

        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'en-US';
        utterance.rate = 1.0;
        utterance.pitch = 1.22;

        const voice = preferredVoice || pickPreferredVoice();
        if (voice) {
            utterance.voice = voice;
            utterance.lang = voice.lang || utterance.lang;
        }

        window.speechSynthesis.speak(utterance);
    }

    function updateVoiceButtons() {
        voiceStartButton.disabled = isListening;
        voiceStopButton.disabled = !isListening;
        voiceRepeatButton.disabled = !lastVoiceReply;
    }

    function handleVoiceResult(transcript) {
        finalTranscript = transcript.trim();
        voiceTranscript.textContent = finalTranscript || 'No speech captured.';
        lastVoiceReply = buildVoiceReply(finalTranscript);
        voiceResponse.textContent = lastVoiceReply;
        voiceRepeatButton.disabled = false;
        speak(lastVoiceReply, 5600);
        speakOutLoud(lastVoiceReply);
        setVoiceStatus('Reply generated locally. This conversation is still frontend-only.');
    }

    function setupVoiceRecognition() {
        const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!Recognition) {
            setVoiceStatus('Speech recognition is not supported in this browser. Use Chrome or Edge for this prototype.');
            voiceTranscript.textContent = 'Speech recognition is unavailable here.';
            voiceStartButton.disabled = true;
            voiceStopButton.disabled = true;
            return;
        }

        recognition = new Recognition();
        recognition.lang = 'en-US';
        recognition.interimResults = true;
        recognition.continuous = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = () => {
            isListening = true;
            finalTranscript = '';
            voiceTranscript.textContent = 'Listening...';
            setVoiceStatus('Listening through the browser microphone. Speak naturally.');
            updateVoiceButtons();
        };

        recognition.onresult = (event) => {
            let interimTranscript = '';
            let completedTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; i += 1) {
                const chunk = event.results[i][0]?.transcript || '';
                if (event.results[i].isFinal) {
                    completedTranscript += chunk + ' ';
                } else {
                    interimTranscript += chunk + ' ';
                }
            }

            voiceTranscript.textContent = (completedTranscript || interimTranscript || 'Listening...').trim();

            if (completedTranscript.trim()) {
                handleVoiceResult(completedTranscript);
            }
        };

        recognition.onerror = (event) => {
            isListening = false;
            updateVoiceButtons();
            setVoiceStatus(`Voice recognition error: ${event.error}.`);
        };

        recognition.onend = () => {
            isListening = false;
            updateVoiceButtons();
            if (!finalTranscript) {
                setVoiceStatus('Listening stopped. No final transcript was captured.');
            }
        };

        voiceStartButton.addEventListener('click', () => {
            if (!recognition || isListening) {
                return;
            }

            finalTranscript = '';
            recognition.start();
        });

        voiceStopButton.addEventListener('click', () => {
            if (!recognition || !isListening) {
                return;
            }

            recognition.stop();
        });

        voiceRepeatButton.addEventListener('click', () => {
            if (!lastVoiceReply) {
                return;
            }

            speak(lastVoiceReply, 5200);
            speakOutLoud(lastVoiceReply);
            setVoiceStatus('Repeating the latest local reply.');
        });

        updateVoiceButtons();
    }

    function setShopOpen(open) {
        page.classList.toggle('game-shop-open', open);
    }

    function activateTab(type) {
        tabButtons.forEach((button) => {
            const active = button.dataset.companionTab === type;
            button.className = active
                ? 'companion-tab-button rounded-full bg-[#4A2C2A] px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-[#F5E6D3]'
                : 'companion-tab-button rounded-full border border-[#D8C3A6] bg-white px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-[#6B3D2E]';
        });

        panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.dataset.companionPanel !== type);
        });
    }

    async function loadScript(sources, id) {
        if (document.getElementById(id)) {
            return;
        }

        const candidates = Array.isArray(sources) ? sources : [sources];
        let lastError = null;

        for (const src of candidates) {
            try {
                await new Promise((resolve, reject) => {
                    const existing = document.getElementById(id);
                    if (existing) {
                        existing.remove();
                    }

                    const script = document.createElement('script');
                    script.id = id;
                    script.src = src;
                    script.async = true;
                    script.onload = resolve;
                    script.onerror = () => reject(new Error('Failed to load script: ' + src));
                    document.head.appendChild(script);
                });

                return;
            } catch (error) {
                lastError = error;
            }
        }

        throw lastError || new Error('Failed to load runtime script.');
    }

    function fitModel() {
        if (!app || !model || !stage) {
            return;
        }

        const width = stage.clientWidth;
        const height = stage.clientHeight;
        app.renderer.resize(width, height);

        const scale = Math.min(width / model.width, height / model.height) * 1.58;
        model.scale.set(scale);
        model.x = (width - model.width) / 2;
        model.y = Math.max(-12, height - model.height + 30);
    }

    function playMotion() {
        if (!model || typeof model.motion !== 'function') {
            return;
        }

        for (const group of ['Tap', 'Tap@Body', 'Flick', 'Flick@Body', 'Idle']) {
            try {
                model.motion(group);
                return;
            } catch (error) {
                continue;
            }
        }
    }

    async function loadScene() {
        try {
            await loadScript([
                'https://cdn.jsdelivr.net/npm/pixi.js@6.5.10/dist/browser/pixi.min.js',
                'https://unpkg.com/pixi.js@6.5.10/dist/browser/pixi.min.js'
            ], 'companion-pixi');
            await loadScript([
                'https://cdn.jsdelivr.net/npm/live2dcubismcore@1.0.2/live2dcubismcore.min.js',
                'https://unpkg.com/live2dcubismcore@1.0.2/live2dcubismcore.min.js'
            ], 'companion-cubism-core');
            await loadScript([
                'https://cdn.jsdelivr.net/npm/pixi-live2d-display@0.4.0/dist/cubism4.min.js',
                'https://unpkg.com/pixi-live2d-display@0.4.0/dist/cubism4.min.js'
            ], 'companion-live2d');

            if (!window.PIXI?.live2d?.Live2DModel) {
                throw new Error('Live2D runtime missing.');
            }

            fallback?.remove();
            app = new window.PIXI.Application({
                autoStart: true,
                backgroundAlpha: 0,
                resizeTo: stage,
                antialias: true,
                autoDensity: true,
            });
            stage.appendChild(app.view);

            model = await window.PIXI.live2d.Live2DModel.from(modelUrl);
            model.interactive = true;
            model.buttonMode = true;
            app.stage.addChild(model);
            fitModel();
            window.addEventListener('resize', fitModel);

            model.on('pointertap', () => {
                playMotion();
                speak(randomFrom(clickLines), 5200);
            });

            playMotion();
            speak(randomFrom([...idleLines, ...clickLines]), 5600);
        } catch (error) {
            if (fallback) {
                fallback.textContent = `Hiyori scene failed. ${error?.message || 'Runtime unavailable.'}`;
            }
            speak(`Scene load failed. ${error?.message || 'Runtime unavailable.'}`, 6200);
        }
    }

    stage.addEventListener('click', () => {
        playMotion();
        speak(randomFrom(clickLines), 5200);
    });

    openButton.addEventListener('click', () => setShopOpen(true));
    closeButton.addEventListener('click', () => setShopOpen(false));
    backdrop.addEventListener('click', () => setShopOpen(false));

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => activateTab(button.dataset.companionTab));
    });

    activateTab('outfit');
    pickPreferredVoice();
    if ('speechSynthesis' in window) {
        window.speechSynthesis.onvoiceschanged = () => {
            pickPreferredVoice();
        };
    }
    setupVoiceRecognition();
    loadScene();
})();
</script>
@endpush

