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

    public function buildSegments(string $content): array
    {
        $paragraphs = $this->splitParagraphs($content);
        $segments = [];

        foreach ($paragraphs as $paragraphIndex => $paragraph) {
            $segments[] = [
                'paragraph_index' => $paragraphIndex + 1,
                'sentence_index' => 0,
                'content_en' => $paragraph,
                'content_cn' => null,
                'start_time' => null,
                'end_time' => null,
            ];
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

