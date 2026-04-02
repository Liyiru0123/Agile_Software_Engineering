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

    <div id="selection-translation-panel" class="hidden fixed z-[70] w-[360px] max-w-[calc(100vw-2rem)] rounded-[1.75rem] border border-[#E0D2C2] bg-white p-5 selection-panel-shadow">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <div class="text-xs uppercase tracking-[0.16em] text-[#9A7358] font-semibold">Quick Actions</div>
                <div class="text-lg font-bold text-[#4A2C2A] mt-1">Selected Text</div>
            </div>
            <button id="selection-panel-close" type="button" class="rounded-full border border-[#E8D9C9] px-3 py-1 text-sm text-[#6B3D2E] hover:bg-[#F8F1E7]">
                Close
            </button>
        </div>

        <div id="selection-source-text" class="rounded-2xl bg-[#FBF7F1] border border-[#EEE2D4] px-4 py-3 text-sm leading-6 text-[#3A2A22] mb-4"></div>

        <div class="flex gap-3 mb-4">
            <button id="selection-translate-btn" type="button" class="flex-1 rounded-2xl bg-[#6B3D2E] text-white px-4 py-3 font-semibold hover:bg-[#4A2C2A] transition">
                Translate
            </button>
            <button id="selection-save-btn" type="button" class="flex-1 rounded-2xl border border-[#D9C7B5] text-[#6B3D2E] px-4 py-3 font-semibold hover:bg-[#F8F1E7] transition disabled:cursor-not-allowed disabled:opacity-60">
                Save
            </button>
        </div>

        <div id="selection-status" class="hidden rounded-2xl px-4 py-3 text-sm mb-4"></div>

        <div id="selection-translation-result" class="hidden rounded-2xl bg-[#F5EEE6] border border-[#E0D2C2] px-4 py-3">
            <div class="text-xs uppercase tracking-[0.15em] text-[#9A7358] font-semibold mb-2">Translation</div>
            <div id="selection-translation-text" class="text-sm leading-6 text-[#3A2A22]"></div>
        </div>
    </div>

    @stack('scripts')

    <script>
    (() => {
        const panel = document.getElementById('selection-translation-panel');
        const closeBtn = document.getElementById('selection-panel-close');
        const translateBtn = document.getElementById('selection-translate-btn');
        const saveBtn = document.getElementById('selection-save-btn');
        const sourceTextEl = document.getElementById('selection-source-text');
        const statusEl = document.getElementById('selection-status');
        const translationResultEl = document.getElementById('selection-translation-result');
        const translationTextEl = document.getElementById('selection-translation-text');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const translateUrl = @json(route('selection.translate'));
        const saveUrl = @json(route('selection.save'));

        let activeSelection = null;

        function normalizeText(value) {
            return String(value || '').replace(/\s+/g, ' ').trim();
        }

        function getElementFromNode(node) {
            if (!node) {
                return null;
            }

            return node.nodeType === Node.TEXT_NODE ? node.parentElement : node;
        }

        function isIgnoredElement(element) {
            return Boolean(element?.closest('input, textarea, select, option, audio, video, script, style, #selection-translation-panel'));
        }

        function hidePanel() {
            panel.classList.add('hidden');
            panel.style.top = '';
            panel.style.left = '';
            activeSelection = null;
            clearStatus();
            hideTranslation();
        }

        function showPanel(selection) {
            sourceTextEl.textContent = selection.text;
            panel.classList.remove('hidden');
            positionPanel(selection.rect);
            updateSaveState(selection.canSave, false);
            clearStatus();

            if (selection.translatedText) {
                showTranslation(selection.translatedText);
            } else {
                hideTranslation();
            }
        }

        function showStatus(message, tone = 'neutral') {
            const tones = {
                neutral: 'bg-[#F5EEE6] text-[#6B3D2E] border border-[#E0D2C2]',
                success: 'bg-emerald-50 text-emerald-800 border border-emerald-200',
                error: 'bg-red-50 text-red-800 border border-red-200',
                warning: 'bg-amber-50 text-amber-800 border border-amber-200',
            };

            statusEl.className = `rounded-2xl px-4 py-3 text-sm mb-4 ${tones[tone] || tones.neutral}`;
            statusEl.textContent = message;
            statusEl.classList.remove('hidden');
        }

        function clearStatus() {
            statusEl.classList.add('hidden');
            statusEl.textContent = '';
        }

        function showTranslation(text) {
            translationTextEl.textContent = text;
            translationResultEl.classList.remove('hidden');
        }

        function hideTranslation() {
            translationResultEl.classList.add('hidden');
            translationTextEl.textContent = '';
        }

        function updateSaveState(canSave, saved) {
            saveBtn.disabled = !canSave || saved;
            saveBtn.textContent = saved ? 'Saved' : 'Save';
            if (!canSave) {
                saveBtn.textContent = 'Save unavailable';
            }
        }

        function positionPanel(rect) {
            if (!rect) {
                panel.style.top = '6rem';
                panel.style.left = `${Math.max(16, window.innerWidth - panel.offsetWidth - 24)}px`;
                return;
            }

            const gap = 12;
            const margin = 16;
            const panelWidth = panel.offsetWidth || 360;
            const panelHeight = panel.offsetHeight || 260;
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            let left = rect.right + gap;
            let top = rect.top + Math.min(rect.height + gap, 24);

            if (left + panelWidth > viewportWidth - margin) {
                left = rect.left - panelWidth - gap;
            }

            if (left < margin) {
                left = Math.min(
                    Math.max(margin, rect.left),
                    Math.max(margin, viewportWidth - panelWidth - margin)
                );
            }

            if (top + panelHeight > viewportHeight - margin) {
                top = rect.top - panelHeight - gap;
            }

            if (top < margin) {
                top = Math.min(
                    Math.max(margin, rect.bottom + gap),
                    Math.max(margin, viewportHeight - panelHeight - margin)
                );
            }

            panel.style.left = `${Math.max(margin, left)}px`;
            panel.style.top = `${Math.max(margin, top)}px`;
        }

        function collectSelectionState() {
            const selection = window.getSelection();

            if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
                return null;
            }

            const range = selection.getRangeAt(0);
            const rect = range.getBoundingClientRect();

            const text = normalizeText(selection.toString());

            if (!text || text.length > 220) {
                return null;
            }

            const anchorEl = getElementFromNode(selection.anchorNode);
            const focusEl = getElementFromNode(selection.focusNode);

            if (!anchorEl || isIgnoredElement(anchorEl)) {
                return null;
            }

            const scope = anchorEl.closest('[data-translate-scope="true"]') || document.body;
            const focusScope = focusEl?.closest?.('[data-translate-scope="true"]') || document.body;

            if (!scope || (focusScope && focusScope !== scope)) {
                return null;
            }

            const paragraphEl = anchorEl.closest('[data-paragraph-index]') || focusEl?.closest?.('[data-paragraph-index]') || null;
            const articleId = scope.dataset.articleId || paragraphEl?.dataset.articleId || null;
            const paragraphIndex = paragraphEl?.dataset.paragraphIndex ?? null;

            return {
                text,
                articleId,
                paragraphIndex,
                paragraphText: normalizeText(paragraphEl?.innerText || ''),
                sourceLanguage: scope.dataset.sourceLanguage || 'en',
                targetLanguage: scope.dataset.targetLanguage || 'zh-CN',
                translatedText: '',
                canSave: Boolean(articleId && paragraphIndex !== null),
                rect: rect.width > 0 || rect.height > 0 ? rect : null,
            };
        }

        async function postJson(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'Request failed.');
            }

            return data;
        }

        function handleSelectionChange() {
            const selectionState = collectSelectionState();

            if (!selectionState) {
                return;
            }

            activeSelection = selectionState;
            showPanel(activeSelection);
        }

        document.addEventListener('mouseup', () => setTimeout(handleSelectionChange, 0));
        document.addEventListener('keyup', (event) => {
            if (event.key === 'Shift' || event.key.startsWith('Arrow')) {
                setTimeout(handleSelectionChange, 0);
            }
        });
        document.addEventListener('mousedown', (event) => {
            if (panel.classList.contains('hidden')) {
                return;
            }

            if (panel.contains(event.target)) {
                return;
            }

            window.getSelection()?.removeAllRanges();
            hidePanel();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                window.getSelection()?.removeAllRanges();
                hidePanel();
            }
        });
        window.addEventListener('resize', () => {
            if (activeSelection && !panel.classList.contains('hidden')) {
                positionPanel(activeSelection.rect);
            }
        });
        window.addEventListener('scroll', () => {
            if (activeSelection && !panel.classList.contains('hidden')) {
                const selection = window.getSelection();
                if (selection && selection.rangeCount > 0 && !selection.isCollapsed) {
                    const rect = selection.getRangeAt(0).getBoundingClientRect();
                    activeSelection.rect = rect.width > 0 || rect.height > 0 ? rect : activeSelection.rect;
                }

                positionPanel(activeSelection.rect);
            }
        }, true);

        closeBtn.addEventListener('click', () => {
            window.getSelection()?.removeAllRanges();
            hidePanel();
        });

        translateBtn.addEventListener('click', async () => {
            if (!activeSelection) {
                return;
            }

            translateBtn.disabled = true;
            translateBtn.textContent = 'Translating...';
            clearStatus();

            try {
                const data = await postJson(translateUrl, {
                    text: activeSelection.text,
                    source_language: activeSelection.sourceLanguage,
                    target_language: activeSelection.targetLanguage,
                });

                activeSelection.translatedText = data.translation.translated_text;
                activeSelection.sourceLanguage = data.translation.source_language || activeSelection.sourceLanguage;
                activeSelection.targetLanguage = data.translation.target_language || activeSelection.targetLanguage;
                showTranslation(activeSelection.translatedText);
                showStatus('Translation loaded.', 'success');
            } catch (error) {
                showStatus(error.message || 'Unable to translate the selected text right now.', 'error');
            } finally {
                translateBtn.disabled = false;
                translateBtn.textContent = 'Translate';
            }
        });

        saveBtn.addEventListener('click', async () => {
            if (!activeSelection) {
                return;
            }

            if (!activeSelection.canSave) {
                showStatus('Save is only available for selections inside article paragraphs.', 'warning');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            clearStatus();

            try {
                const data = await postJson(saveUrl, {
                    article_id: Number(activeSelection.articleId),
                    paragraph_index: Number(activeSelection.paragraphIndex),
                    selected_text: activeSelection.text,
                    translated_text: activeSelection.translatedText || null,
                    source_language: activeSelection.sourceLanguage,
                    target_language: activeSelection.targetLanguage,
                });

                activeSelection.translatedText = data.favorite.translated_text;
                showTranslation(activeSelection.translatedText);
                updateSaveState(true, true);
                showStatus(data.message || 'Saved to favorites.', 'success');
            } catch (error) {
                updateSaveState(true, false);
                showStatus(error.message || 'Unable to save the selection right now.', 'error');
            }
        });
    })();
    </script>
</body>
</html>
