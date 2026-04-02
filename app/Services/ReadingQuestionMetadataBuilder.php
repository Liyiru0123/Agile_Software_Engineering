<?php

namespace App\Services;

use Illuminate\Support\Str;

class ReadingQuestionMetadataBuilder
{
    protected array $stopWords = [
        'a', 'an', 'and', 'are', 'as', 'at', 'be', 'because', 'been', 'but', 'by', 'for', 'from',
        'had', 'has', 'have', 'how', 'if', 'in', 'into', 'is', 'it', 'its', 'of', 'on', 'or', 'so',
        'than', 'that', 'the', 'their', 'them', 'there', 'these', 'they', 'this', 'to', 'was', 'were',
        'what', 'when', 'where', 'which', 'who', 'why', 'will', 'with', 'would',
    ];

    public function __construct(
        protected ArticleTextProcessor $processor
    ) {
    }

    public function enrichQuestionData(string $articleContent, array $questionData, mixed $answerData = null): array
    {
        $sentenceMap = $this->buildSentenceMap($articleContent);

        if (is_array($questionData['questions'] ?? null)) {
            $questionData['questions'] = collect($questionData['questions'])
                ->map(fn (array $question, int $index) => $this->enrichSingleQuestion($question, $sentenceMap, $answerData, $index, $questionData))
                ->all();

            return $questionData;
        }

        return $this->enrichSingleQuestion($questionData, $sentenceMap, $answerData, 0, $questionData);
    }

    protected function enrichSingleQuestion(
        array $question,
        array $sentenceMap,
        mixed $answerData,
        int $index,
        array $parentQuestionData
    ): array {
        $questionText = trim((string) ($question['question_text'] ?? $question['question'] ?? ''));
        $options = $this->normalizeOptions($question['options'] ?? $parentQuestionData['options'] ?? []);
        $correctAnswer = $this->resolveCorrectAnswer($question, $answerData, $index);

        if ($questionText === '' || $options === [] || $correctAnswer === null) {
            return $question;
        }

        $questionType = $question['question_type'] ?? $question['type'] ?? $this->inferQuestionType($questionText);
        $correctOptionText = $this->resolveCorrectOptionText($options, $correctAnswer);
        $source = $this->locateSourceSentence(
            $sentenceMap,
            $questionText,
            $correctOptionText,
            $question['source_excerpt'] ?? $question['source_sentence'] ?? null
        );

        $question['question_type'] = is_string($questionType)
            ? Str::of($questionType)->lower()->replace('_', ' ')->headline()->toString()
            : $this->inferQuestionType($questionText);

        $question['source_excerpt'] = $question['source_excerpt'] ?? $source['sentence'] ?? null;
        $question['source_anchor'] = $question['source_anchor'] ?? $source['anchor'] ?? null;
        $question['source_label'] = $question['source_label'] ?? $source['label'] ?? null;
        $question['explanation'] = $question['explanation'] ?? $this->buildExplanation(
            $question['question_type'],
            strtoupper($correctAnswer),
            $correctOptionText,
            $question['source_excerpt'] ?? null
        );

        return $question;
    }

    protected function resolveCorrectAnswer(array $question, mixed $answerData, int $index): ?string
    {
        $candidate = $question['correct_answer'] ?? $question['answer'] ?? null;

        if (is_string($candidate) && trim($candidate) !== '') {
            return strtoupper(trim($candidate));
        }

        if (is_string($answerData) && trim($answerData) !== '') {
            return strtoupper(trim($answerData, "\" \t\n\r\0\x0B"));
        }

        if (! is_array($answerData)) {
            return null;
        }

        $lookup = $answerData['questions'] ?? $answerData;

        foreach ([(string) ($index + 1), $index + 1, $question['id'] ?? null] as $key) {
            if ($key === null || ! array_key_exists($key, $lookup)) {
                continue;
            }

            $value = $lookup[$key];

            if (is_string($value) && trim($value) !== '') {
                return strtoupper(trim($value));
            }
        }

        return null;
    }

    protected function normalizeOptions(mixed $options): array
    {
        if (! is_array($options)) {
            return [];
        }

        return collect($options)
            ->map(function ($option, int $index) {
                if (is_array($option)) {
                    $key = strtoupper(trim((string) ($option['key'] ?? chr(65 + $index))));
                    $text = trim((string) ($option['text'] ?? $option['label'] ?? ''));
                } else {
                    $key = chr(65 + $index);
                    $text = trim((string) $option);
                }

                if ($text === '') {
                    return null;
                }

                return [
                    'key' => $key,
                    'text' => $text,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveCorrectOptionText(array $options, string $correctAnswer): string
    {
        foreach ($options as $option) {
            if (strtoupper((string) $option['key']) === strtoupper($correctAnswer)) {
                return (string) $option['text'];
            }
        }

        return '';
    }

    protected function inferQuestionType(string $questionText): string
    {
        $normalized = strtolower($questionText);

        if (preg_match('/according to|why|what caused|what does the article suggest|which detail|which statement|how did|what did/', $normalized) === 1) {
            return 'Detail';
        }

        if (preg_match('/main idea|central claim|best title|primarily/', $normalized) === 1) {
            return 'Main Idea';
        }

        return 'Comprehension';
    }

    protected function buildSentenceMap(string $content): array
    {
        $paragraphs = $this->processor->splitParagraphs($content);
        $sentences = [];

        foreach ($paragraphs as $paragraphIndex => $paragraph) {
            foreach ($this->processor->splitSentences($paragraph) as $sentenceIndex => $sentence) {
                $sentences[] = [
                    'paragraph_index' => $paragraphIndex,
                    'sentence_index' => $sentenceIndex,
                    'anchor' => 'p'.$paragraphIndex.'-s'.$sentenceIndex,
                    'label' => 'Paragraph '.($paragraphIndex + 1).', sentence '.($sentenceIndex + 1),
                    'sentence' => trim($sentence),
                ];
            }
        }

        return $sentences;
    }

    protected function locateSourceSentence(array $sentenceMap, string $questionText, string $correctOptionText, ?string $preferredExcerpt = null): array
    {
        if (is_string($preferredExcerpt) && trim($preferredExcerpt) !== '') {
            foreach ($sentenceMap as $sentence) {
                if (Str::contains($sentence['sentence'], trim($preferredExcerpt), true)) {
                    return $sentence;
                }
            }
        }

        $needleTokens = $this->keywords($questionText.' '.$correctOptionText);

        if ($needleTokens === []) {
            return $sentenceMap[0] ?? [
                'anchor' => null,
                'label' => null,
                'sentence' => null,
            ];
        }

        $bestSentence = null;
        $bestScore = -1;

        foreach ($sentenceMap as $sentence) {
            $sentenceTokens = $this->keywords($sentence['sentence']);
            $sharedTokens = array_intersect($needleTokens, $sentenceTokens);
            $score = count($sharedTokens);

            foreach ($sharedTokens as $token) {
                if (Str::contains(strtolower($correctOptionText), $token)) {
                    $score += 2;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestSentence = $sentence;
            }
        }

        return $bestSentence ?? ($sentenceMap[0] ?? [
            'anchor' => null,
            'label' => null,
            'sentence' => null,
        ]);
    }

    protected function keywords(string $text): array
    {
        preg_match_all("/[A-Za-z][A-Za-z'-]{2,}/", strtolower($text), $matches);

        return collect($matches[0] ?? [])
            ->reject(fn (string $token) => in_array($token, $this->stopWords, true))
            ->unique()
            ->values()
            ->all();
    }

    protected function buildExplanation(string $questionType, string $correctAnswer, string $correctOptionText, ?string $sourceExcerpt): string
    {
        $parts = [
            'The best answer is '.$correctAnswer.'. '.$correctOptionText.'.',
        ];

        if ($questionType === 'Detail' && filled($sourceExcerpt)) {
            $parts[] = 'This detail is supported directly by the original sentence shown below.';
        } elseif (filled($sourceExcerpt)) {
            $parts[] = 'The source sentence below provides the clearest support for this choice.';
        } else {
            $parts[] = 'This choice matches the key idea expressed in the passage.';
        }

        return implode(' ', $parts);
    }
}
