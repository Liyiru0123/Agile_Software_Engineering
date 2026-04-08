@php
    $body = str_replace("\r\n", "\n", trim((string) ($body ?? '')));
    $attachments = $item->relationLoaded('attachments')
        ? $item->attachments
        : $item->attachments()->get();
    $attachmentsBySortOrder = $attachments->keyBy(fn ($attachment) => (int) ($attachment->sort_order ?? 0));
    $usedAttachmentIds = [];
    $blocks = $body === '' ? [] : (preg_split("/\n{2,}/", $body) ?: []);
@endphp

@foreach($blocks as $block)
    @php
        $trimmedBlock = trim($block);
        $matchedImageSortOrder = null;

        if (preg_match('/^\[\[forum-image:(\d+)\]\]$/', $trimmedBlock, $matches)) {
            $matchedImageSortOrder = (int) $matches[1];
        }
    @endphp

    @if($matchedImageSortOrder !== null && $attachmentsBySortOrder->has($matchedImageSortOrder))
        @php
            $attachment = $attachmentsBySortOrder->get($matchedImageSortOrder);
            $usedAttachmentIds[] = $attachment->id;
        @endphp
        @include('forum.partials.attachment', [
            'item' => $item,
            'embedded' => true,
            'showLabel' => false,
            'attachmentsOverride' => collect([$attachment]),
        ])
    @elseif(str_starts_with($trimmedBlock, '## '))
        <h3 class="mt-5 text-lg font-black tracking-tight text-[#4A2C2A]">
            {{ ltrim(substr($trimmedBlock, 3)) }}
        </h3>
    @elseif($trimmedBlock !== '')
        <div class="mt-4 whitespace-pre-line leading-8">{{ $trimmedBlock }}</div>
    @endif
@endforeach

@php
    $remainingAttachments = $attachments->reject(fn ($attachment) => in_array($attachment->id, $usedAttachmentIds, true))->values();
@endphp

@if($remainingAttachments->isNotEmpty())
    @include('forum.partials.attachment', [
        'item' => $item,
        'embedded' => true,
        'showLabel' => false,
        'attachmentsOverride' => $remainingAttachments,
    ])
@endif
