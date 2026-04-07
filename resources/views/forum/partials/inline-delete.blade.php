@php
    $detailsId = $id ?? ('inline-delete-'.md5(($action ?? '').($message ?? '')));
    $buttonText = $buttonText ?? 'Delete';
    $confirmText = $confirmText ?? 'Delete';
    $cancelText = $cancelText ?? 'Cancel';
    $message = $message ?? 'Are you sure you want to delete this item?';
    $summaryClass = $summaryClass ?? 'rounded-xl border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50';
    $panelClass = $panelClass ?? 'absolute right-0 top-full z-20 mt-3 w-[min(22rem,calc(100vw-2rem))] rounded-[1.5rem] border border-[#E6D3BC] bg-white p-4 shadow-xl';
    $confirmClass = $confirmClass ?? 'rounded-xl bg-[#C84C3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#AE3E2F]';
    $cancelClass = $cancelClass ?? 'rounded-xl border border-[#D9C7B5] px-4 py-2 text-sm font-semibold text-[#6B3D2E] transition hover:border-[#C9A961] hover:bg-[#FBF7F1]';
@endphp

<details id="{{ $detailsId }}" class="relative" data-inline-delete>
    <summary class="{{ $summaryClass }} list-none cursor-pointer">
        {{ $buttonText }}
    </summary>

    <div class="{{ $panelClass }}">
        <div class="text-sm font-semibold uppercase tracking-[0.14em] text-[#8B6B47]">Confirm Delete</div>
        <p class="mt-3 text-sm leading-6 text-[#4A2C2A]">{{ $message }}</p>

        <div class="mt-4 flex flex-wrap justify-end gap-3">
            <button type="button" class="{{ $cancelClass }}" data-inline-delete-cancel="{{ $detailsId }}">
                {{ $cancelText }}
            </button>

            <form method="POST" action="{{ $action }}">
                @csrf
                @method('DELETE')
                @foreach(($hiddenFields ?? []) as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="{{ $confirmClass }}">
                    {{ $confirmText }}
                </button>
            </form>
        </div>
    </div>
</details>

@once
    @push('scripts')
        <script>
        (() => {
            document.querySelectorAll('[data-inline-delete-cancel]').forEach((button) => {
                if (button.dataset.bound === 'true') {
                    return;
                }

                button.dataset.bound = 'true';
                button.addEventListener('click', () => {
                    const details = document.getElementById(button.dataset.inlineDeleteCancel);
                    if (details) {
                        details.open = false;
                    }
                });
            });

            document.addEventListener('click', (event) => {
                document.querySelectorAll('[data-inline-delete]').forEach((details) => {
                    if (!details.open) {
                        return;
                    }

                    if (!details.contains(event.target)) {
                        details.open = false;
                    }
                });
            });
        })();
        </script>
    @endpush
@endonce
