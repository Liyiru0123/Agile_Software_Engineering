<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'EAPlus'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .selection-panel-shadow {
            box-shadow: 0 22px 45px rgba(74, 44, 42, 0.16);
        }

        .companion-bubble-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .forum-preview-clamp {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .forum-title-clamp {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .forum-content-wrap {
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        summary::-webkit-details-marker {
            display: none;
        }
    </style>
    @stack('styles')
</head>
@php
    $hasCompanionTables = \Illuminate\Support\Facades\Schema::hasTable('companion_profiles')
        && \Illuminate\Support\Facades\Schema::hasTable('companion_shop_items');
    $hasFriendRequestsTable = \Illuminate\Support\Facades\Schema::hasTable('friend_requests');
    $hasConversationTables = \Illuminate\Support\Facades\Schema::hasTable('conversations')
        && \Illuminate\Support\Facades\Schema::hasTable('conversation_participants')
        && \Illuminate\Support\Facades\Schema::hasTable('conversation_messages');
    $hasForumNotificationsTable = \Illuminate\Support\Facades\Schema::hasTable('forum_notifications');

    $companionProfile = auth()->check() && $hasCompanionTables
        ? \App\Models\CompanionProfile::query()->with('equippedItem')->where('user_id', auth()->id())->first()
        : null;
    $companionGold = $companionProfile?->gold ?? 0;
    $companionEquipped = $companionProfile?->equippedItem?->name;
    $companionNotice = session('companion_notice');
    $pendingFriendRequestCount = 0;
    $unreadConversationCount = 0;
    $unreadForumNotificationCount = 0;

    if (auth()->check()) {
        if ($hasFriendRequestsTable) {
            $pendingFriendRequestCount = \App\Models\FriendRequest::query()
                ->where('receiver_id', auth()->id())
                ->where('status', 'pending')
                ->count();
        }

        if ($hasConversationTables) {
            $unreadConversationCount = \App\Models\Conversation::query()
                ->whereHas('participants', fn ($query) => $query->where('users.id', auth()->id()))
                ->with([
                    'latestMessage',
                    'participants' => fn ($query) => $query->where('users.id', auth()->id()),
                ])
                ->get()
                ->filter(function (\App\Models\Conversation $conversation) {
                    $latestMessage = $conversation->latestMessage;
                    $lastReadAt = optional($conversation->participants->first()?->pivot)->last_read_at;

                    return $latestMessage
                        && (int) $latestMessage->sender_id !== (int) auth()->id()
                        && (! $lastReadAt || $latestMessage->created_at?->gt($lastReadAt));
                })
                ->count();
        }

        if ($hasForumNotificationsTable) {
            $unreadForumNotificationCount = \App\Models\ForumNotification::query()
                ->where('user_id', auth()->id())
                ->whereNull('read_at')
                ->count();
        }
    }
@endphp
<body class="bg-[#FAF0E6]">
    <nav class="bg-[#4A2C2A] border-b-4 border-[#2C1810] shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <svg class="w-8 h-8 text-[#C9A961] group-hover:text-[#D4B970] transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-serif font-bold text-[#F5E6D3]">EAPlus</h1>
                        <p class="text-xs text-[#C9A961]">Your Learning Journey</p>
                    </div>
                </a>

                <div class="flex items-center gap-2 ml-8">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('home') ? 'bg-[#6B3D2E]' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('articles.index', ['skill' => 'listening']) }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ (request()->routeIs('articles.listening') || (request()->routeIs('articles.index') && request('skill', 'listening') === 'listening')) ? 'bg-[#6B3D2E]' : '' }}">
                        Listening
                    </a>
                    <a href="{{ route('speaking.hub') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ (request()->routeIs('speaking.*') || request()->routeIs('articles.speaking') || (request()->routeIs('articles.index') && request('skill') === 'speaking')) ? 'bg-[#6B3D2E]' : '' }}">
                        Speaking
                    </a>
                    <a href="{{ route('forum.index') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('forum.*') ? 'bg-[#6B3D2E]' : '' }}">
                        Forum
                    </a>
                    <a href="{{ route('companion.index') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('companion.*') ? 'bg-[#6B3D2E]' : '' }}">
                        Companion
                    </a>
                    <a href="{{ route('game.index') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('game.*') ? 'bg-[#6B3D2E]' : '' }}">
                        Game
                    </a>
                </div>
            </div>

            @auth
                @php
                    $userName = auth()->user()->name ?? 'User';
                    $avatarText = mb_strtoupper(trim(mb_substr($userName, 0, 2)));
                @endphp
                <div class="flex items-center gap-4">
                    <a href="{{ route('companion.index') }}" class="hidden sm:inline-flex items-center gap-2 rounded-full border border-[#C9A961]/50 bg-[#F5E6D3]/10 px-3 py-2 text-xs font-bold uppercase tracking-[0.14em] text-[#F5E6D3] hover:bg-[#F5E6D3]/15 transition">
                        <span>Gold</span>
                        <span class="text-[#D4B970]">{{ number_format($companionGold) }}</span>
                    </a>
                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-3 rounded-full border border-[#C9A961]/40 bg-[#F5E6D3]/10 px-2.5 py-2 text-[#F5E6D3] transition hover:bg-[#F5E6D3]/15">
                            <span class="relative">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#D4B970] text-sm font-black uppercase tracking-[0.08em] text-[#4A2C2A] shadow-inner">
                                    {{ $avatarText }}
                                </span>
                                @if($pendingFriendRequestCount > 0 || $unreadConversationCount > 0 || $unreadForumNotificationCount > 0)
                                    <span class="absolute -right-1 -top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-[#D35D47] px-1 text-[10px] font-bold leading-none text-white">
                                        {{ min(99, $pendingFriendRequestCount + $unreadConversationCount + $unreadForumNotificationCount) }}
                                    </span>
                                @endif
                            </span>
                            <span class="hidden text-sm font-semibold md:inline">{{ $userName }}</span>
                            <svg class="h-4 w-4 text-[#D4B970]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m6 9 6 6 6-6"/>
                            </svg>
                        </summary>

                        <div class="absolute right-0 top-[calc(100%+0.85rem)] w-60 overflow-hidden rounded-[1.5rem] border border-[#6B3D2E] bg-[#4A2C2A] p-3 shadow-2xl shadow-[#2C1810]/30">
                            <div class="mb-3 rounded-[1.25rem] bg-[#5C3732] px-4 py-3">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.16em] text-[#D8B58A]">Signed in</div>
                                <div class="mt-2 text-sm font-semibold text-[#F9EBDD]">{{ $userName }}</div>
                            </div>

                            <div class="space-y-1.5">
                                <a href="{{ route('forum.my') }}" class="flex items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#F9EBDD] transition hover:bg-[#6B3D2E] {{ request()->routeIs('forum.my') ? 'bg-[#6B3D2E]' : '' }}">
                                    <span>My Forum</span>
                                    @if($unreadForumNotificationCount > 0)
                                        <span class="flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[#D35D47] px-1.5 text-[11px] font-bold text-white">
                                            {{ min(99, $unreadForumNotificationCount) }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('friends.index') }}" class="flex items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#F9EBDD] transition hover:bg-[#6B3D2E] {{ request()->routeIs('friends.*') ? 'bg-[#6B3D2E]' : '' }}">
                                    <span>Friends</span>
                                    @if($pendingFriendRequestCount > 0)
                                        <span class="flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[#D35D47] px-1.5 text-[11px] font-bold text-white">
                                            {{ min(99, $pendingFriendRequestCount) }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('messages.index') }}" class="flex items-center justify-between gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-[#F9EBDD] transition hover:bg-[#6B3D2E] {{ request()->routeIs('messages.*') ? 'bg-[#6B3D2E]' : '' }}">
                                    <span>Private Messages</span>
                                    @if($unreadConversationCount > 0)
                                        <span class="flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-[#D35D47] px-1.5 text-[11px] font-bold text-white">
                                            {{ min(99, $unreadConversationCount) }}
                                        </span>
                                    @endif
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl bg-[#6B3D2E] px-4 py-3 text-left text-sm font-semibold text-[#F9EBDD] transition hover:bg-[#8B4D3A]">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </details>
                </div>
            @endauth
        </div>
    </nav>

    @yield('content')

    <div id="selection-translate-popover" class="hidden fixed z-[70] w-[min(24rem,calc(100vw-1.5rem))] rounded-3xl border border-[#E0D2C2] bg-white shadow-2xl shadow-[#2C1810]/15">
        <div class="px-4 pt-4 pb-3 border-b border-[#F0E4D7]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-[11px] uppercase tracking-[0.16em] text-[#9A7358] font-semibold">Selection</div>
                    <div id="selection-translate-selected" class="mt-2 text-sm leading-6 text-[#3A2A22]"></div>
                </div>
                <div id="selection-translate-save-hint" class="hidden text-[11px] leading-5 text-[#9A7358] max-w-[8rem] text-right"></div>
            </div>
        </div>

        <div class="px-4 py-3 space-y-3">
            <div id="selection-translate-status" class="hidden rounded-2xl px-3 py-2 text-sm"></div>
            <div id="selection-translate-result" class="rounded-2xl bg-[#FBF7F1] border border-[#EEE2D4] px-3 py-3 text-sm leading-6 text-[#3A2A22]">
                Select text, then click Translate.
            </div>
        </div>

        <div class="px-4 pb-4 flex items-center gap-3">
            <button id="selection-translate-action" type="button" class="flex-1 rounded-2xl bg-[#6B3D2E] hover:bg-[#4A2C2A] text-white px-4 py-3 text-sm font-semibold transition">
                Translate
            </button>
            <button id="selection-save-action" type="button" class="flex-1 rounded-2xl border border-[#D9C7B5] text-[#4A2C2A] px-4 py-3 text-sm font-semibold transition hover:bg-[#FBF7F1] disabled:opacity-50 disabled:cursor-not-allowed">
                Save
            </button>
        </div>
    </div>

    <div id="companion-shell" class="fixed bottom-4 right-4 z-[60] flex flex-col items-end gap-3">
        <button id="companion-reopen" type="button" class="hidden fixed bottom-4 right-4 z-[61] rounded-full border border-[#D9C7B5] bg-white/95 px-4 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-[#4A2C2A] shadow-lg hover:bg-[#FBF7F1] transition">
            Open Hiyori
        </button>

        <div id="companion-widget" class="w-[220px] sm:w-[260px] pointer-events-auto">
            <div id="companion-bubble" class="mb-3 rounded-3xl border border-[#E7D4C3] bg-white/95 px-4 py-3 text-sm leading-6 text-[#4A2C2A] shadow-xl opacity-0 translate-y-2 transition duration-300"></div>
            <div id="companion-stage" class="relative h-[340px] cursor-pointer select-none overflow-visible bg-transparent">
                <button id="companion-hitbox" type="button" aria-label="Talk to Hiyori" class="absolute inset-0 z-10 bg-transparent"></button>
                <div id="companion-stage-fallback" class="absolute inset-x-0 bottom-10 text-center text-sm text-[#6B3D2E]">
                    Loading Hiyori v2...
                </div>
            </div>
        </div>

        <div id="companion-menu" class="hidden fixed z-[65] min-w-[180px] rounded-2xl border border-[#D9C7B5] bg-white py-2 shadow-2xl shadow-[#2C1810]/20">
            <button id="companion-hide-action" type="button" class="w-full px-4 py-2.5 text-left text-sm font-medium text-[#4A2C2A] hover:bg-[#FBF7F1] transition">
                Hide Hiyori
            </button>
        </div>
    </div>
    @stack('scripts')

    @auth
    <script>
    (() => {
        const accountMenu = document.querySelector('nav details');

        if (!accountMenu) {
            return;
        }

        document.addEventListener('click', (event) => {
            if (!accountMenu.contains(event.target)) {
                accountMenu.removeAttribute('open');
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                accountMenu.removeAttribute('open');
            }
        });
    })();
    </script>
    @endauth
    @auth
    <script>
    (() => {
        const popover = document.getElementById('selection-translate-popover');
        const selectedEl = document.getElementById('selection-translate-selected');
        const resultEl = document.getElementById('selection-translate-result');
        const statusEl = document.getElementById('selection-translate-status');
        const saveHintEl = document.getElementById('selection-translate-save-hint');
        const translateButton = document.getElementById('selection-translate-action');
        const saveButton = document.getElementById('selection-save-action');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const translateUrl = @json(route('selection.translate'));
        const saveUrl = @json(route('selection.save'));
        const maxTranslateCharacters = 220;
        const state = {
            text: '',
            rect: null,
            articleId: null,
            paragraphIndex: null,
            sourceLanguage: 'en',
            targetLanguage: 'zh-CN',
            translatedText: '',
        };

        let selectionTimeout = null;

        function queueSelectionUpdate() {
            window.clearTimeout(selectionTimeout);
            selectionTimeout = window.setTimeout(updateFromSelection, 0);
        }

        function updateFromSelection() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
                hidePopover();
                return;
            }

            const text = selection.toString().replace(/\s+/g, ' ').trim();
            if (!text) {
                hidePopover();
                return;
            }

            const anchorMeta = getParagraphMeta(selection.anchorNode);
            const focusMeta = getParagraphMeta(selection.focusNode);
            const paragraphMeta = isSameParagraph(anchorMeta, focusMeta) ? anchorMeta : null;
            const range = selection.getRangeAt(0);
            const rect = range.getBoundingClientRect();

            if (!rect || (!rect.width && !rect.height)) {
                hidePopover();
                return;
            }

            state.text = text;
            state.rect = rect;
            state.articleId = paragraphMeta?.articleId ?? null;
            state.paragraphIndex = paragraphMeta?.paragraphIndex ?? null;
            state.sourceLanguage = paragraphMeta?.sourceLanguage ?? 'en';
            state.targetLanguage = paragraphMeta?.targetLanguage ?? 'zh-CN';
            state.translatedText = '';

            selectedEl.textContent = text;
            resultEl.textContent = text.length > maxTranslateCharacters
                ? `Selected text: ${text.length} characters. Limit: ${maxTranslateCharacters}.`
                : 'Select text, then click Translate.';
            setStatus(
                text.length > maxTranslateCharacters
                    ? `This selection is too long to translate at once. Please shorten it by ${text.length - maxTranslateCharacters} characters.`
                    : '',
                'error'
            );
            updateSaveAvailability();
            showPopover();
        }

        function updateSaveAvailability() {
            if (state.articleId !== null && state.paragraphIndex !== null) {
                saveButton.disabled = false;
                saveHintEl.classList.add('hidden');
                saveHintEl.textContent = '';
                return;
            }

            saveButton.disabled = true;
            saveHintEl.textContent = 'Save works inside article paragraphs.';
            saveHintEl.classList.remove('hidden');
        }

        function getParagraphMeta(node) {
            const element = node instanceof Element ? node : node?.parentElement;
            const paragraph = element?.closest('[data-paragraph-index]');
            if (!paragraph) {
                return null;
            }

            const scope = paragraph.closest('[data-translate-scope]');

            return {
                articleId: Number(paragraph.dataset.articleId || scope?.dataset.articleId || 0) || null,
                paragraphIndex: Number(paragraph.dataset.paragraphIndex || -1),
                sourceLanguage: scope?.dataset.sourceLanguage || 'en',
                targetLanguage: scope?.dataset.targetLanguage || 'zh-CN',
            };
        }

        function isSameParagraph(first, second) {
            if (!first || !second) {
                return false;
            }

            return first.articleId === second.articleId && first.paragraphIndex === second.paragraphIndex;
        }

        function showPopover() {
            popover.classList.remove('hidden');
            positionPopover();
        }

        function hidePopover() {
            popover.classList.add('hidden');
        }

        function positionPopover() {
            if (popover.classList.contains('hidden') || !state.rect) {
                return;
            }

            const gap = 12;
            const rect = state.rect;
            const width = popover.offsetWidth;
            const height = popover.offsetHeight;
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            let left = rect.right + gap;
            let top = rect.bottom + gap;

            if (left + width > viewportWidth - 12) {
                left = rect.left - width - gap;
            }

            if (left < 12) {
                left = Math.max(12, viewportWidth - width - 12);
            }

            if (top + height > viewportHeight - 12) {
                top = rect.top - height - gap;
            }

            if (top < 12) {
                top = 12;
            }

            popover.style.left = `${left}px`;
            popover.style.top = `${top}px`;
        }

        function setStatus(message, kind) {
            if (!message) {
                statusEl.className = 'hidden rounded-2xl px-3 py-2 text-sm';
                statusEl.textContent = '';
                return;
            }

            const classes = {
                info: 'rounded-2xl px-3 py-2 text-sm bg-[#F8F1E7] text-[#6B3D2E] border border-[#E8D9C9]',
                success: 'rounded-2xl px-3 py-2 text-sm bg-emerald-50 text-emerald-700 border border-emerald-200',
                error: 'rounded-2xl px-3 py-2 text-sm bg-red-50 text-red-700 border border-red-200',
            };

            statusEl.className = classes[kind] || classes.info;
            statusEl.textContent = message;
        }

        async function postJson(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                const validationErrors = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : '';

                throw new Error(validationErrors || data.message || 'Request failed.');
            }

            return data;
        }

        translateButton.addEventListener('click', async () => {
            if (!state.text) {
                return;
            }

            if (state.text.length > maxTranslateCharacters) {
                resultEl.textContent = `Selected text: ${state.text.length} characters. Limit: ${maxTranslateCharacters}.`;
                setStatus(
                    `This selection is too long to translate at once. Please shorten it by ${state.text.length - maxTranslateCharacters} characters.`,
                    'error'
                );
                return;
            }

            translateButton.disabled = true;
            translateButton.textContent = 'Translating...';
            setStatus('', 'info');

            try {
                const data = await postJson(translateUrl, {
                    text: state.text,
                    source_language: state.sourceLanguage,
                    target_language: state.targetLanguage,
                });

                state.translatedText = data.translation?.translated_text || '';
                resultEl.textContent = state.translatedText || 'No translation returned.';
                setStatus('Translation ready.', 'success');
                positionPopover();
            } catch (error) {
                setStatus(error.message, 'error');
            } finally {
                translateButton.disabled = false;
                translateButton.textContent = 'Translate';
            }
        });

        saveButton.addEventListener('click', async () => {
            if (saveButton.disabled || !state.text || state.articleId === null || state.paragraphIndex === null) {
                return;
            }

            saveButton.disabled = true;
            saveButton.textContent = 'Saving...';
            setStatus('', 'info');

            try {
                const data = await postJson(saveUrl, {
                    article_id: state.articleId,
                    paragraph_index: state.paragraphIndex,
                    selected_text: state.text,
                    translated_text: state.translatedText,
                    source_language: state.sourceLanguage,
                    target_language: state.targetLanguage,
                });

                if (data.favorite?.translated_text && !state.translatedText) {
                    state.translatedText = data.favorite.translated_text;
                    resultEl.textContent = state.translatedText;
                }

                setStatus(data.message || 'Saved to favorites.', 'success');
                positionPopover();
            } catch (error) {
                setStatus(error.message, 'error');
            } finally {
                saveButton.textContent = 'Save';
                updateSaveAvailability();
            }
        });

        document.addEventListener('mouseup', queueSelectionUpdate);
        document.addEventListener('keyup', (event) => {
            if (event.key === 'Escape') {
                hidePopover();
                return;
            }

            queueSelectionUpdate();
        });
        document.addEventListener('touchend', queueSelectionUpdate);
        document.addEventListener('selectionchange', () => {
            const selection = window.getSelection();
            if (!selection || selection.toString().trim() === '') {
                hidePopover();
            }
        });
        document.addEventListener('mousedown', (event) => {
            if (popover.classList.contains('hidden')) {
                return;
            }

            if (popover.contains(event.target)) {
                return;
            }

            hidePopover();
        });
        window.addEventListener('scroll', positionPopover, true);
        window.addEventListener('resize', positionPopover);
    })();
    </script>
    @endauth
    <script>
    (() => {
        const widget = document.getElementById('companion-widget');
        const reopenButton = document.getElementById('companion-reopen');
        const bubble = document.getElementById('companion-bubble');
        const stage = document.getElementById('companion-stage');
        const hitbox = document.getElementById('companion-hitbox');
        const stageFallback = document.getElementById('companion-stage-fallback');
        const menu = document.getElementById('companion-menu');
        const hideAction = document.getElementById('companion-hide-action');
        const hiddenStorageKey = 'eaplus:hiyori:hidden';
        const runtimeVersion = 'hiyori-runtime-v3';
        const modelUrl = @json(asset('live2d/hiyori/hiyori_free_t08.model3.json'));
        const initialNotice = @json($companionNotice['message'] ?? null);
        const goldBalance = @json($companionGold);
        const equippedName = @json($companionEquipped);
        const authenticated = @json(auth()->check());
        const clickLines = [
            'Hello. Ready for one focused study set?',
            'A short session still counts. Let us keep the streak moving.',
            'You clicked me. I will assume that means we are working now.',
            'Pick one article and finish one module. Momentum first.',
            'If you only have ten minutes, use them well.',
            'One finished task is better than five half-started ones.',
            'Let us keep going. I am paying attention.',
        ];
        const factLines = [
            'Fun fact: repeating short listening clips is usually more effective than replaying a long one passively.',
            'Fun fact: saving one strong sentence to your notebook is often better than saving ten vague ones.',
            'Fun fact: paraphrasing after reading improves recall much more than rereading alone.',
            'Fun fact: a stable daily routine beats occasional marathon sessions.',
        ];
        const idleLines = [
            'I am staying here in the corner while you study.',
            'Right click me if you want to hide the widget for now.',
            'Click me for a short line or a study fact.',
        ];
        const authLines = authenticated
            ? [
                `Current gold: ${goldBalance}.`,
                equippedName ? `Equipped outfit: ${equippedName}.` : 'Current outfit: Default Look.',
                'Finish one module in an article to earn more gold.',
            ]
            : ['Log in to start collecting gold for Hiyori.'];

        let bubbleTimer = null;
        let live2dLoaded = false;
        let live2dApp = null;
        let live2dModel = null;
        let lastSpeechTriggerAt = 0;

        function randomFrom(list) {
            return list[Math.floor(Math.random() * list.length)];
        }

        function speak(text, duration = 5000) {
            if (!text || !bubble) {
                return;
            }

            bubble.textContent = text;
            bubble.style.opacity = '1';
            bubble.style.transform = 'translateY(0)';
            window.clearTimeout(bubbleTimer);
            bubbleTimer = window.setTimeout(() => {
                bubble.style.opacity = '0';
                bubble.style.transform = 'translateY(0.5rem)';
            }, duration);
        }

        function hideMenu() {
            menu.classList.add('hidden');
        }

        function showMenu(x, y) {
            menu.classList.remove('hidden');
            const width = menu.offsetWidth || 180;
            const height = menu.offsetHeight || 60;
            menu.style.left = `${Math.min(x, window.innerWidth - width - 12)}px`;
            menu.style.top = `${Math.min(y, window.innerHeight - height - 12)}px`;
        }

        function applyHiddenState(hidden) {
            widget.classList.toggle('hidden', hidden);
            reopenButton.classList.toggle('hidden', !hidden);
        }

        function hideWidget(persist = true) {
            applyHiddenState(true);
            hideMenu();
            if (persist) {
                window.localStorage.setItem(hiddenStorageKey, '1');
            }
        }

        function showWidget() {
            applyHiddenState(false);
            window.localStorage.removeItem(hiddenStorageKey);
            hideMenu();
            if (!live2dLoaded) {
                loadLive2D();
            }
            speak(initialNotice || randomFrom([...authLines, ...idleLines]), 5200);
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
            if (!live2dApp || !live2dModel || !stage) {
                return;
            }

            const width = stage.clientWidth;
            const height = stage.clientHeight;
            live2dApp.renderer.resize(width, height);

            const scale = Math.min(width / live2dModel.width, height / live2dModel.height) * 1.14;
            live2dModel.scale.set(scale);
            live2dModel.x = (width - live2dModel.width) / 2;
            live2dModel.y = Math.max(0, height - live2dModel.height + 18);
        }

        function playMotion() {
            if (!live2dModel || typeof live2dModel.motion !== 'function') {
                return;
            }

            for (const group of ['Tap', 'Tap@Body', 'Flick', 'Flick@Body', 'Idle']) {
                try {
                    live2dModel.motion(group);
                    return;
                } catch (error) {
                    continue;
                }
            }
        }

        function triggerWidgetSpeech(preferredDuration = 5200) {
            const now = Date.now();
            if (now - lastSpeechTriggerAt < 320) {
                return;
            }

            lastSpeechTriggerAt = now;
            playMotion();
            speak(randomFrom(Math.random() > 0.35 ? clickLines : factLines), preferredDuration);
        }

        async function loadLive2D() {
            if (live2dLoaded) {
                return;
            }

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

                stageFallback?.remove();
                live2dApp = new window.PIXI.Application({
                    autoStart: true,
                    backgroundAlpha: 0,
                    resizeTo: stage,
                    antialias: true,
                    autoDensity: true,
                });
                stage.appendChild(live2dApp.view);

                live2dModel = await window.PIXI.live2d.Live2DModel.from(modelUrl);
                live2dModel.interactive = true;
                live2dModel.buttonMode = true;
                live2dApp.stage.addChild(live2dModel);
                fitModel();
                window.addEventListener('resize', fitModel);

                live2dModel.on('pointertap', () => {
                    triggerWidgetSpeech(5400);
                });

                live2dApp.view.addEventListener('click', triggerWidgetSpeech);
                live2dApp.view.addEventListener('pointerdown', () => triggerWidgetSpeech(5400));

                live2dLoaded = true;
                playMotion();
                speak(initialNotice || randomFrom([...authLines, ...idleLines]), 5600);
            } catch (error) {
                const message = error && error.message ? error.message : 'The model runtime could not be loaded here.';
                if (stageFallback) {
                    stageFallback.textContent = `Hiyori is resting. ${message}`;
                }
                speak(`The widget loaded, but the Live2D runtime could not start. ${message}`, 6200);
            }
        }

        hitbox?.addEventListener('click', () => {
            triggerWidgetSpeech(5200);
        });

        hitbox?.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            showMenu(event.clientX, event.clientY);
        });

        stage.addEventListener('click', () => {
            triggerWidgetSpeech(5200);
        });

        widget.addEventListener('click', (event) => {
            if (event.target.closest('#companion-reopen, #companion-hide-action, #companion-menu')) {
                return;
            }

            triggerWidgetSpeech(5200);
        });

        stage.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            showMenu(event.clientX, event.clientY);
        });

        widget.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            showMenu(event.clientX, event.clientY);
        });

        hideAction.addEventListener('click', () => hideWidget(true));
        reopenButton.addEventListener('click', showWidget);

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target)) {
                hideMenu();
            }
        });

        document.addEventListener('keyup', (event) => {
            if (event.key === 'Escape') {
                hideMenu();
            }
        });

        window.setInterval(() => {
            if (!widget.classList.contains('hidden')) {
                speak(randomFrom([...idleLines, ...authLines]), 5000);
            }
        }, 45000);

        const shouldStartHidden = window.localStorage.getItem(hiddenStorageKey) === '1';
        applyHiddenState(shouldStartHidden);

        if (!shouldStartHidden) {
            loadLive2D();
        }
    })();
    </script>
</body>
</html>
