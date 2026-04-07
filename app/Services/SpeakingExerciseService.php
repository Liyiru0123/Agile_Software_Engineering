<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleSegment;

class SpeakingExerciseService
{
    public function __construct(
        protected ArticleTextProcessor $processor
    ) {
    }

    public function buildShadowingClips(Article $article, int $maxClips = 6): array
    {
        $segmentClips = $this->buildSegmentShadowingClips($article);

        if ($segmentClips !== []) {
            return $segmentClips;
        }

        $chunks = $this->buildShadowingChunks($article->content);

        if ($chunks === []) {
            return [];
        }

        $totalWords = max(1, $this->processor->countWords($article->content));

        return collect($this->sampleEvenly($chunks, $maxClips))
            ->values()
            ->map(function (array $chunk, int $index) use ($totalWords) {
                return [
                    'id' => 'shadow-'.$index,
                    'title' => 'Shadowing Clip '.($index + 1),
                    'transcript' => $chunk['transcript'],
                    'word_count' => $chunk['word_count'],
                    'start_ratio' => round($chunk['start_word'] / $totalWords, 6),
                    'end_ratio' => round($chunk['end_word'] / $totalWords, 6),
                    'duration_hint_seconds' => max(4, (int) round($chunk['word_count'] / 2.6)),
                ];
            })
            ->all();
    }

    public function findShadowingClip(Article $article, string $clipId): ?array
    {
        return collect($this->buildShadowingClips($article))
            ->first(fn (array $clip) => ($clip['id'] ?? null) === $clipId);
    }

    protected function buildSegmentShadowingClips(Article $article): array
    {
        /** @var \Illuminate\Support\Collection<int, ArticleSegment> $segments */
        $segments = $article->relationLoaded('segments')
            ? $article->segments
            : $article->segments()
                ->orderBy('paragraph_index')
                ->orderBy('sentence_index')
                ->get();

        return $segments
            ->filter(fn (ArticleSegment $segment) => filled($segment->content_en))
            ->map(function (ArticleSegment $segment) {
                $transcript = trim((string) preg_replace('/\s+/u', ' ', $segment->content_en));
                $wordCount = $this->processor->countWords($transcript);
                $startTime = $segment->start_time;
                $endTime = $segment->end_time;
                $duration = null;

                if ($startTime !== null && $endTime !== null && $endTime > $startTime) {
                    $duration = round($endTime - $startTime, 2);
                }

                return [
                    'id' => 'segment-'.$segment->getKey(),
                    'title' => 'Paragraph '.$segment->paragraph_index,
                    'transcript' => $transcript,
                    'word_count' => $wordCount,
                    'paragraph_index' => $segment->paragraph_index,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'duration_hint_seconds' => $duration !== null
                        ? max(4, (int) round($duration))
                        : max(4, (int) round($wordCount / 2.6)),
                    'time_range_label' => $duration !== null
                        ? $this->formatTimeRange($startTime, $endTime)
                        : null,
                ];
            })
            ->filter(fn (array $clip) => $clip['word_count'] > 0)
            ->values()
            ->all();
    }

    protected function buildShadowingChunks(string $content): array
    {
        $units = $this->buildShadowingUnits($content);
        $chunks = [];
        $buffer = [];
        $bufferWords = 0;
        $bufferStartWord = null;

        foreach ($units as $unit) {
            $unitWords = $unit['word_count'];

            if ($buffer === []) {
                $buffer = [$unit];
                $bufferWords = $unitWords;
                $bufferStartWord = $unit['start_word'];

                continue;
            }

            if ($bufferWords < 10 || $bufferWords + $unitWords <= 22) {
                $buffer[] = $unit;
                $bufferWords += $unitWords;
            } else {
                $chunks[] = [
                    'transcript' => $this->joinUnits($buffer),
                    'word_count' => $bufferWords,
                    'start_word' => $bufferStartWord,
                    'end_word' => $buffer[array_key_last($buffer)]['end_word'],
                ];

                $buffer = [$unit];
                $bufferWords = $unitWords;
                $bufferStartWord = $unit['start_word'];
            }
        }

        if ($buffer !== []) {
            $chunks[] = [
                'transcript' => $this->joinUnits($buffer),
                'word_count' => $bufferWords,
                'start_word' => $bufferStartWord,
                'end_word' => $buffer[array_key_last($buffer)]['end_word'],
            ];
        }

        return array_values(array_filter($chunks, fn (array $chunk) => $chunk['word_count'] >= 6));
    }

    protected function buildShadowingUnits(string $content): array
    {
        $sentences = collect($this->processor->splitParagraphs($content))
            ->flatMap(fn (string $paragraph) => $this->processor->splitSentences($paragraph))
            ->filter()
            ->values();

        $units = [];
        $wordCursor = 0;

        foreach ($sentences as $sentence) {
            foreach ($this->splitSentenceForShadowing((string) $sentence) as $part) {
                $part = trim($part);
                $wordCount = $this->processor->countWords($part);

                if ($part === '' || $wordCount === 0) {
                    continue;
                }

                $units[] = [
                    'transcript' => $part,
                    'word_count' => $wordCount,
                    'start_word' => $wordCursor,
                    'end_word' => $wordCursor + $wordCount,
                ];

                $wordCursor += $wordCount;
            }
        }

        return $units;
    }

    protected function splitSentenceForShadowing(string $sentence): array
    {
        $normalized = trim((string) preg_replace('/\s+/u', ' ', $sentence));

        if ($normalized === '') {
            return [];
        }

        if ($this->processor->countWords($normalized) <= 22) {
            return [$normalized];
        }

        $clauses = preg_split('/(?<=[,;:])\s+|(?<=—)\s+|(?<=--)\s+/u', $normalized) ?: [$normalized];
        $prepared = [];

        foreach ($clauses as $clause) {
            $clause = trim($clause);

            if ($clause === '') {
                continue;
            }

            if ($this->processor->countWords($clause) <= 22) {
                $prepared[] = $clause;

                continue;
            }

            $prepared = [...$prepared, ...$this->splitByWordWindow($clause, 16)];
        }

        return $prepared !== [] ? $prepared : [$normalized];
    }

    protected function splitByWordWindow(string $text, int $window): array
    {
        preg_match_all("/[A-Za-z][A-Za-z0-9'-]*|[0-9]+|[.,!?;:—-]/u", $text, $matches);
        $tokens = $matches[0] ?? [];

        if ($tokens === []) {
            return [$text];
        }

        $parts = [];
        $current = [];
        $wordCount = 0;

        foreach ($tokens as $token) {
            $current[] = $token;

            if (preg_match("/[A-Za-z0-9]/", $token) === 1) {
                $wordCount++;
            }

            if ($wordCount >= $window && preg_match('/[.,!?;:]/', $token) === 1) {
                $parts[] = $this->rebuildTokens($current);
                $current = [];
                $wordCount = 0;
            }
        }

        if ($current !== []) {
            $parts[] = $this->rebuildTokens($current);
        }

        return array_values(array_filter(array_map('trim', $parts)));
    }

    protected function rebuildTokens(array $tokens): string
    {
        $text = implode(' ', $tokens);
        $text = preg_replace('/\s+([,.;:!?])/u', '$1', $text) ?? $text;
        $text = preg_replace('/\s+—\s+/u', ' — ', $text) ?? $text;

        return trim($text);
    }

    protected function joinUnits(array $units): string
    {
        return trim(collect($units)
            ->pluck('transcript')
            ->map(fn (string $text) => trim($text))
            ->implode(' '));
    }

    protected function formatTimeRange(float $startTime, float $endTime): string
    {
        return $this->formatSeconds($startTime).' - '.$this->formatSeconds($endTime);
    }

    protected function formatSeconds(float $seconds): string
    {
        $rounded = max(0, (int) round($seconds));
        $minutes = intdiv($rounded, 60);
        $remainingSeconds = $rounded % 60;

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    protected function sampleEvenly(array $items, int $maxItems): array
    {
        if (count($items) <= $maxItems) {
            return $items;
        }

        $selectedIndexes = collect(range(0, $maxItems - 1))
            ->map(function (int $slot) use ($items, $maxItems) {
                if ($maxItems === 1) {
                    return 0;
                }

                return (int) round($slot * ((count($items) - 1) / ($maxItems - 1)));
            })
            ->unique()
            ->values();

        return $selectedIndexes
            ->map(fn (int $index) => $items[$index])
            ->values()
            ->all();
    }
}
