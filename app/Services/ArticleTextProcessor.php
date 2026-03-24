<?php

namespace App\Services;

class ArticleTextProcessor
{
    private const NON_TERMINAL_ABBREVIATIONS = [
        'mr',
        'mrs',
        'ms',
        'dr',
        'prof',
        'sr',
        'jr',
        'st',
        'mt',
        'gen',
        'rep',
        'sen',
        'gov',
        'capt',
        'lt',
        'col',
        'sgt',
        'no',
    ];

    private const CONDITIONAL_ABBREVIATIONS = [
        'etc',
        'e.g',
        'i.e',
        'vs',
        'fig',
        'al',
        'inc',
        'ltd',
        'co',
        'corp',
        'jan',
        'feb',
        'mar',
        'apr',
        'jun',
        'jul',
        'aug',
        'sep',
        'sept',
        'oct',
        'nov',
        'dec',
        'u.s',
        'u.k',
    ];
 public function parseSubtitles(string $content, string $format = 'srt'): array
    {
        if ($format === 'vtt') {
            return $this->parseVtt($content);
        }

        return $this->parseSrt($content);
    }

    public function parseSrt(string $content): array
    {
        $normalized = preg_replace("/\r\n?/", "\n", trim($content));
        $blocks = preg_split("/\n\s*\n+/u", $normalized) ?: [];
        $segments = [];
        $paragraphIndex = 0;

        foreach ($blocks as $block) {
            $lines = explode("\n", $block);
            if (count($lines) < 3) {
                continue;
            }

            // Line 1 is index, Line 2 is time, Line 3+ is text
            $timeLine = $lines[1];
            if (!preg_match('/(\d{2}:\d{2}:\d{2},\d{3})\s*-->\s*(\d{2}:\d{2}:\d{2},\d{3})/', $timeLine, $matches)) {
                continue;
            }

            $startTime = $this->timeToSeconds(str_replace(',', '.', $matches[1]));
            $endTime = $this->timeToSeconds(str_replace(',', '.', $matches[2]));

            $textLines = array_slice($lines, 2);
            $text = implode(' ', $textLines);
            $text = trim(strip_tags($text));

            $segments[] = [
                'paragraph_index' => $paragraphIndex,
                'sentence_index' => 0,
                'content_en' => $text,
                'content_cn' => null,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
            $paragraphIndex++;
        }

        return $segments;
    }

    public function parseVtt(string $content): array
    {
        $normalized = preg_replace("/\r\n?/", "\n", trim($content));
        // Remove WEBVTT header
        $normalized = preg_replace('/^WEBVTT.*?\n\n/s', '', $normalized);
        $blocks = preg_split("/\n\s*\n+/u", $normalized) ?: [];
        $segments = [];
        $paragraphIndex = 0;

        foreach ($blocks as $block) {
            $lines = explode("\n", $block);
            if (count($lines) < 2) {
                continue;
            }

            $timeLineIndex = 0;
            if (!str_contains($lines[0], '-->')) {
                $timeLineIndex = 1;
            }

            if (!isset($lines[$timeLineIndex])) {
                continue;
            }

            $timeLine = $lines[$timeLineIndex];
            if (!preg_match('/(\d{2}:\d{2}:\d{2}\.\d{3}|\d{2}:\d{2}\.\d{3})\s*-->\s*(\d{2}:\d{2}:\d{2}\.\d{3}|\d{2}:\d{2}\.\d{3})/', $timeLine, $matches)) {
                continue;
            }

            $startTime = $this->timeToSeconds($matches[1]);
            $endTime = $this->timeToSeconds($matches[2]);

            $textLines = array_slice($lines, $timeLineIndex + 1);
            $text = implode(' ', $textLines);
            $text = trim(strip_tags($text));

            $segments[] = [
                'paragraph_index' => $paragraphIndex,
                'sentence_index' => 0,
                'content_en' => $text,
                'content_cn' => null,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
            $paragraphIndex++;
        }

        return $segments;
    }

    private function timeToSeconds(string $timeString): float
    {
        $parts = explode(':', $timeString);
        $seconds = 0.0;
        
        if (count($parts) === 3) {
            $seconds += (float)$parts[0] * 3600;
            $seconds += (float)$parts[1] * 60;
            $seconds += (float)$parts[2];
        } elseif (count($parts) === 2) {
            $seconds += (float)$parts[0] * 60;
            $seconds += (float)$parts[1];
        }

        return round($seconds, 3);
    }

    public function buildSegments(string $content): array
    {
        $paragraphs = $this->splitParagraphs($content);
        $segments = [];

        foreach ($paragraphs as $paragraphIndex => $paragraph) {
            $sentences = $this->splitSentences($paragraph);

            foreach ($sentences as $sentenceIndex => $sentence) {
                $segments[] = [
                    'paragraph_index' => $paragraphIndex,
                    'sentence_index' => $sentenceIndex,
                    'content_en' => $sentence,
                    'content_cn' => null,
                    'start_time' => null,
                    'end_time' => null,
                ];
            }
        }

        return $segments;
    }

    public function countWords(string $content): int
    {
        preg_match_all("/[A-Za-z0-9']+/u", $content, $matches);

        return count($matches[0]);
    }

    public function splitParagraphs(string $content): array
    {
        $normalized = preg_replace("/\r\n?/", "\n", trim($content));

        if ($normalized === null || $normalized === '') {
            return [];
        }

        $paragraphs = preg_split("/\n\s*\n+/u", $normalized) ?: [];

        return array_values(array_filter(array_map(
            fn (string $paragraph) => trim((string) preg_replace("/\s+/u", ' ', $paragraph)),
            $paragraphs
        )));
    }

    public function splitSentences(string $paragraph): array
    {
        $paragraph = trim($paragraph);

        if ($paragraph === '') {
            return [];
        }

        $paragraph = (string) preg_replace('/\s+/u', ' ', $paragraph);
        $length = strlen($paragraph);
        $sentences = [];
        $buffer = '';

        for ($index = 0; $index < $length; $index++) {
            $char = $paragraph[$index];
            $buffer .= $char;

            if (! in_array($char, ['.', '!', '?'], true)) {
                continue;
            }

            while ($index + 1 < $length && in_array($paragraph[$index + 1], ['.', '!', '?'], true)) {
                $index++;
                $buffer .= $paragraph[$index];
            }

            while ($index + 1 < $length && in_array($paragraph[$index + 1], ["\"", "'", ')', ']'], true)) {
                $index++;
                $buffer .= $paragraph[$index];
            }

            if (! $this->shouldSplitAtPunctuation($paragraph, $index, $char, $buffer)) {
                continue;
            }

            $sentence = trim($buffer);

            if ($sentence !== '') {
                $sentences[] = $sentence;
            }

            $buffer = '';

            while ($index + 1 < $length && ctype_space($paragraph[$index + 1])) {
                $index++;
            }
        }

        $tail = trim($buffer);

        if ($tail !== '') {
            $sentences[] = $tail;
        }

        return $sentences !== [] ? $sentences : [$paragraph];
    }

    private function shouldSplitAtPunctuation(string $paragraph, int $index, string $punctuation, string $buffer): bool
    {
        if ($punctuation === '!' || $punctuation === '?') {
            return true;
        }

        $previousChar = $index > 0 ? $paragraph[$index - 1] : '';
        $nextChar = $index + 1 < strlen($paragraph) ? $paragraph[$index + 1] : '';

        if (ctype_digit($previousChar) && ctype_digit($nextChar)) {
            return false;
        }

        $nextSignificant = $this->nextSignificantChar($paragraph, $index + 1);
        $normalizedToken = $this->normalizedTrailingToken($buffer);

        if ($normalizedToken === '') {
            return $nextSignificant === null;
        }

        if ($this->isInitialism($normalizedToken)) {
            return $nextSignificant === null || (! ctype_lower($nextSignificant) && ! ctype_digit($nextSignificant));
        }

        if (in_array($normalizedToken, self::NON_TERMINAL_ABBREVIATIONS, true)) {
            return false;
        }

        if (ctype_upper($previousChar) && $nextSignificant !== null && ctype_upper($nextSignificant)) {
            return false;
        }

        if (in_array($normalizedToken, self::CONDITIONAL_ABBREVIATIONS, true)) {
            return $nextSignificant === null || (! ctype_lower($nextSignificant) && ! ctype_digit($nextSignificant));
        }

        return true;
    }

    private function nextSignificantChar(string $paragraph, int $start): ?string
    {
        $length = strlen($paragraph);

        for ($index = $start; $index < $length; $index++) {
            $char = $paragraph[$index];

            if (ctype_space($char) || in_array($char, ["\"", "'", ')', ']', '('], true)) {
                continue;
            }

            return $char;
        }

        return null;
    }

    private function normalizedTrailingToken(string $buffer): string
    {
        preg_match('/([A-Za-z](?:[A-Za-z.]*)?)\.*["\')\]]*$/', $buffer, $matches);

        if (! isset($matches[1])) {
            return '';
        }

        return strtolower(trim($matches[1], '.'));
    }

    private function isInitialism(string $token): bool
    {
        return preg_match('/^(?:[a-z]\.){1,}[a-z]?$/', $token) === 1
            || preg_match('/^[a-z](?:\.[a-z])+$/', $token) === 1;
    }
}


