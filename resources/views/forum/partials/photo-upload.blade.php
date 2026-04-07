@php
    $fieldName = $name ?? 'attachments[]';
    $fieldId = $id ?? 'forum-photo-upload';
    $fieldLabel = $label ?? 'Photos';
    $buttonLabel = $buttonLabel ?? 'Choose Photos';
    $emptyText = $emptyText ?? 'No photos selected';
    $helperText = $helperText ?? 'Upload JPG, PNG, GIF, or WEBP images. Maximum 5 MB per image.';
@endphp

<div>
    <label for="{{ $fieldId }}" class="mb-2 block text-sm font-semibold text-[#4A2C2A]">{{ $fieldLabel }}</label>
    <div class="rounded-[1.5rem] border border-[#D9C7B5] bg-[#FBF7F1] p-4">
        <input
            id="{{ $fieldId }}"
            name="{{ $fieldName }}"
            type="file"
            accept=".jpg,.jpeg,.png,.gif,.webp"
            class="sr-only"
            multiple
            data-upload-input
            data-empty-text="{{ $emptyText }}"
        >

        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                data-upload-button
                data-upload-target="{{ $fieldId }}"
                class="rounded-full bg-[#4A2C2A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#6B3D2E]"
            >
                {{ $buttonLabel }}
            </button>
            <span data-upload-status="{{ $fieldId }}" class="text-sm text-[#6B3D2E]">{{ $emptyText }}</span>
        </div>

        <div class="mt-2 text-xs leading-5 text-[#8B6B47]">{{ $helperText }}</div>
    </div>
</div>

@once
    @push('scripts')
        <script>
        (() => {
            document.querySelectorAll('[data-upload-button]').forEach((button) => {
                if (button.dataset.bound === 'true') {
                    return;
                }

                button.dataset.bound = 'true';

                const targetId = button.getAttribute('data-upload-target');
                const input = document.getElementById(targetId);
                const status = document.querySelector(`[data-upload-status="${targetId}"]`);

                if (!input || !status) {
                    return;
                }

                button.addEventListener('click', () => input.click());

                input.addEventListener('change', () => {
                    const files = Array.from(input.files || []);

                    if (files.length === 0) {
                        status.textContent = input.dataset.emptyText || 'No photos selected';
                        return;
                    }

                    if (files.length === 1) {
                        status.textContent = files[0].name;
                        return;
                    }

                    status.textContent = `${files.length} photos selected`;
                });
            });
        })();
        </script>
    @endpush
@endonce
