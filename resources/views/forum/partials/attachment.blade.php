@php
    $embedded = $embedded ?? false;
    $showLabel = $showLabel ?? true;
    $attachments = $attachmentsOverride ?? (
        $item->relationLoaded('attachments')
            ? $item->attachments
            : $item->attachments()->get()
    );

    if ($attachments->isEmpty() && $item->hasLegacyAttachment() && $item->attachmentUrl()) {
        $attachments = collect([
            (object) [
                'display_url' => $item->attachmentUrl(),
                'original_name' => $item->attachment_original_name,
                'mime_type' => $item->attachment_mime_type,
                'size' => $item->attachment_size,
                'is_image' => $item->isImageAttachment(),
            ],
        ]);
    }
@endphp

@if($attachments->isNotEmpty())
    <div class="{{ $embedded ? 'mt-5' : 'mt-4 rounded-[1.5rem] border border-[#E6D3BC] bg-[#FBF7F1] p-4' }}" data-photo-gallery>
        @if($showLabel && ! $embedded)
            <div class="mb-3 text-xs font-semibold uppercase tracking-[0.14em] text-[#8B6B47]">Photos</div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($attachments as $attachment)
                @php
                    $attachmentUrl = $attachment->display_url ?? $attachment->url();
                    $attachmentName = $attachment->original_name ?: 'Forum photo';
                    $attachmentMimeType = $attachment->mime_type;
                    $attachmentSize = $attachment->size;
                    $isImage = $attachment->is_image ?? $attachment->isImage();
                    $attachmentSizeLabel = null;

                    if ($attachmentSize) {
                        $attachmentSizeLabel = $attachmentSize >= 1024 * 1024
                            ? number_format($attachmentSize / 1024 / 1024, 1).' MB'
                            : number_format($attachmentSize / 1024, 0).' KB';
                    }
                @endphp

                <div class="overflow-hidden rounded-[1.25rem] border border-[#E6D3BC] bg-white">
                    @if($isImage)
                        <button
                            type="button"
                            class="flex aspect-[4/3] w-full items-center justify-center bg-white transition hover:bg-[#FBF7F1]"
                            title="View image"
                            data-gallery-trigger
                            data-gallery-src="{{ $attachmentUrl }}"
                            data-gallery-alt="{{ $attachmentName }}"
                            data-gallery-caption="{{ $attachmentName }}"
                        >
                            <img src="{{ $attachmentUrl }}" alt="{{ $attachmentName }}" class="max-h-full max-w-full object-contain">
                        </button>
                    @else
                        <div class="flex aspect-[4/3] items-center justify-center bg-[#F6F0E8] px-4 text-center text-sm font-semibold text-[#6B3D2E]">
                            {{ $attachmentName }}
                        </div>
                    @endif

                    <div class="space-y-2 px-4 py-3">
                        <div class="truncate text-sm font-semibold text-[#4A2C2A]">{{ $attachmentName }}</div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-[#8B6B47]">
                            @if($attachmentSizeLabel)
                                <span>{{ $attachmentSizeLabel }}</span>
                            @endif
                            @if($attachmentMimeType)
                                <span>{{ $attachmentMimeType }}</span>
                            @endif
                        </div>

                        @unless($isImage)
                            <a href="{{ $attachmentUrl }}" target="_blank" rel="noreferrer" class="inline-flex rounded-full border border-[#D9C7B5] px-3 py-1 text-xs font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]">
                                Open File
                            </a>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@once
    @push('scripts')
        <script>
        (() => {
            const modal = document.getElementById('forum-photo-viewer');
            const modalImage = document.getElementById('forum-photo-viewer-image');
            const modalCaption = document.getElementById('forum-photo-viewer-caption');
            const modalCounter = document.getElementById('forum-photo-viewer-counter');
            const closeButton = document.getElementById('forum-photo-viewer-close');
            const prevButton = document.getElementById('forum-photo-viewer-prev');
            const nextButton = document.getElementById('forum-photo-viewer-next');

            if (!modal || !modalImage || !modalCaption || !modalCounter || !closeButton || !prevButton || !nextButton) {
                return;
            }

            const state = {
                items: [],
                index: 0,
            };

            function render() {
                const current = state.items[state.index];
                if (!current) {
                    return;
                }

                modalImage.src = current.src;
                modalImage.alt = current.alt || current.caption || 'Forum photo';
                modalCaption.textContent = current.caption || current.alt || '';
                modalCounter.textContent = state.items.length > 1 ? `${state.index + 1} / ${state.items.length}` : '1 / 1';
                prevButton.disabled = state.items.length <= 1;
                nextButton.disabled = state.items.length <= 1;
            }

            function open(items, index) {
                state.items = items;
                state.index = index;
                render();
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function close() {
                modal.classList.add('hidden');
                modalImage.src = '';
                document.body.classList.remove('overflow-hidden');
            }

            function showPrevious() {
                if (state.items.length <= 1) {
                    return;
                }

                state.index = (state.index - 1 + state.items.length) % state.items.length;
                render();
            }

            function showNext() {
                if (state.items.length <= 1) {
                    return;
                }

                state.index = (state.index + 1) % state.items.length;
                render();
            }

            document.querySelectorAll('[data-photo-gallery]').forEach((gallery) => {
                const triggers = Array.from(gallery.querySelectorAll('[data-gallery-trigger]'));

                triggers.forEach((trigger, index) => {
                    trigger.addEventListener('click', () => {
                        const items = triggers.map((item) => ({
                            src: item.dataset.gallerySrc,
                            alt: item.dataset.galleryAlt || '',
                            caption: item.dataset.galleryCaption || '',
                        }));

                        open(items, index);
                    });
                });
            });

            closeButton.addEventListener('click', close);
            prevButton.addEventListener('click', showPrevious);
            nextButton.addEventListener('click', showNext);

            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    close();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (modal.classList.contains('hidden')) {
                    return;
                }

                if (event.key === 'Escape') {
                    close();
                } else if (event.key === 'ArrowLeft') {
                    showPrevious();
                } else if (event.key === 'ArrowRight') {
                    showNext();
                }
            });
        })();
        </script>
    @endpush
@endonce

@once
    <div id="forum-photo-viewer" class="hidden fixed inset-0 z-[90] bg-[#2C1810]/85 px-4 py-6">
        <div class="mx-auto flex h-full max-w-[1400px] items-center justify-center gap-3">
            <button id="forum-photo-viewer-prev" type="button" class="rounded-full border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-40">
                Prev
            </button>

            <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-[2rem] border border-white/10 bg-[#1F1410]/90 shadow-2xl">
                <div class="flex items-center justify-between gap-3 border-b border-white/10 px-5 py-4 text-white">
                    <div class="min-w-0">
                        <div id="forum-photo-viewer-caption" class="truncate text-sm font-semibold"></div>
                        <div id="forum-photo-viewer-counter" class="mt-1 text-xs text-white/70"></div>
                    </div>

                    <button id="forum-photo-viewer-close" type="button" class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                        Close
                    </button>
                </div>

                <div class="flex min-h-0 flex-1 items-center justify-center bg-[#120C09] p-4">
                    <img id="forum-photo-viewer-image" src="" alt="" class="max-h-full max-w-full object-contain">
                </div>
            </div>

            <button id="forum-photo-viewer-next" type="button" class="rounded-full border border-white/20 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/20 disabled:cursor-not-allowed disabled:opacity-40">
                Next
            </button>
        </div>
    </div>
@endonce
