<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Academic English')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .selection-panel-shadow {
            box-shadow: 0 22px 45px rgba(74, 44, 42, 0.16);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#FAF0E6]">
    <nav class="bg-[#4A2C2A] border-b-4 border-[#2C1810] shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <svg class="w-8 h-8 text-[#C9A961] group-hover:text-[#D4B970] transition" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-serif font-bold text-[#F5E6D3]">Academic English</h1>
                        <p class="text-xs text-[#C9A961]">Your Learning Journey</p>
                    </div>
                </a>

                <div class="flex items-center gap-2 ml-8">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('home') ? 'bg-[#6B3D2E]' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('articles.index') }}"
                       class="px-4 py-2 text-[#F5E6D3] hover:bg-[#6B3D2E] rounded-lg transition text-sm font-medium {{ request()->routeIs('articles.*') ? 'bg-[#6B3D2E]' : '' }}">
                        Library
                    </a>
                </div>
            </div>

            @auth
                <div class="flex items-center gap-4">
                    <span class="text-[#F5E6D3] text-sm font-medium">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-[#6B3D2E] text-[#F5E6D3] rounded-lg hover:bg-[#8B4D3A] transition font-medium text-sm shadow-md">
                            Logout
                        </button>
                    </form>
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

    @stack('scripts')

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
            resultEl.textContent = text.length > 220
                ? 'Select a shorter passage to translate. The current limit is 220 characters.'
                : 'Select text, then click Translate.';
            setStatus('', 'info');
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
                throw new Error(data.message || 'Request failed.');
            }

            return data;
        }

        translateButton.addEventListener('click', async () => {
            if (!state.text) {
                return;
            }

            if (state.text.length > 220) {
                setStatus('Select a shorter passage before translating.', 'error');
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
</body>
</html>
