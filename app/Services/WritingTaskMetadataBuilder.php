<?php

namespace App\Services;

use Illuminate\Support\Str;

class WritingTaskMetadataBuilder
{
    public const CORE_TASK_TYPES = [
        'summary_response',
        'paraphrase',
        'opinion',
    ];

    public function __construct(
        protected ArticleTextProcessor $processor
    ) {
    }

    public function detectTaskType(array $questionData): string
    {
        $rawTaskType = (string) ($questionData['task_type'] ?? '');

        if (in_array($rawTaskType, self::CORE_TASK_TYPES, true)) {
            return $rawTaskType;
        }

        $text = Str::lower(trim(($questionData['instruction'] ?? '').' '.($questionData['requirement'] ?? '')));

        if ($text !== '') {
            if (Str::contains($text, ['rewrite', 'paraphrase', 'similarity', 'reduce similarity'])) {
                return 'paraphrase';
            }

            if (Str::contains($text, ['opinion', 'agree', 'disagree', 'your view', 'your own'])) {
                return 'opinion';
            }
        }

        return 'summary_response';
    }

    public function enrichTaskData(string $articleContent, array $questionData, string $provider = 'database'): array
    {
        $taskType = $this->detectTaskType($questionData);
        $defaults = $this->defaultTaskBlueprints($articleContent)[$taskType];
        $currentSource = (string) ($questionData['source_text'] ?? '');

        $questionData['task_type'] = $taskType;
        $questionData['title'] = $questionData['title'] ?? $defaults['title'];
        $questionData['badge'] = $questionData['badge'] ?? $defaults['badge'];
        $questionData['instruction'] = $questionData['instruction'] ?? $defaults['instruction'];
        $questionData['requirement'] = $questionData['requirement'] ?? $defaults['requirement'];
        $questionData['source_text'] = $this->shouldReplaceSourceText($taskType, $currentSource)
            ? $defaults['source_text']
            : $currentSource;
        $questionData['word_limit'] = $this->resolveWordLimit(
            $taskType,
            $questionData['source_text'],
            $questionData['word_limit'] ?? null,
            $defaults['word_limit']
        );
        $questionData['checkpoints'] = array_values($questionData['checkpoints'] ?? $defaults['checkpoints']);
        $questionData['rubric_focus'] = array_values($questionData['rubric_focus'] ?? $defaults['rubric_focus']);
        $questionData['provider'] = $questionData['provider'] ?? $provider;

        return $questionData;
    }

    public function buildMissingTasks(string $articleContent, array $existingTypes): array
    {
        $missingTypes = array_values(array_diff(self::CORE_TASK_TYPES, $existingTypes));
        $defaults = $this->defaultTaskBlueprints($articleContent);

        return collect($missingTypes)
            ->map(function (string $taskType) use ($defaults) {
                $task = $defaults[$taskType];
                $task['provider'] = 'generated-fallback';

                return $task;
            })
            ->values()
            ->all();
    }

    protected function defaultTaskBlueprints(string $articleContent): array
    {
        $paragraphs = $this->processor->splitParagraphs($articleContent);
        $sentences = collect($paragraphs)
            ->flatMap(fn (string $paragraph) => $this->processor->splitSentences($paragraph))
            ->filter()
            ->values();

        $summaryExcerpt = $this->buildExcerpt($sentences, minWords: 72, maxChars: 760, fallback: $articleContent, preferredSentenceCount: 4);
        $opinionExcerpt = $this->buildExcerpt($sentences, minWords: 56, maxChars: 560, fallback: $articleContent, preferredSentenceCount: 3);
        $paraphraseExcerpt = $this->buildExcerpt($sentences, minWords: 22, maxChars: 260, fallback: $articleContent, preferredSentenceCount: 1);

        return [
            'summary_response' => [
                'task_type' => 'summary_response',
                'title' => 'Summary + Response',
                'badge' => 'Integrated writing',
                'instruction' => 'Summarize the article clearly, then explain your own judgment or application of the main idea.',
                'requirement' => 'Write one concise summary paragraph and one short response paragraph. Mention the main claim, one supporting detail, and your own evaluation.',
                'source_text' => $summaryExcerpt,
                'word_limit' => $this->buildSummaryWordLimit($summaryExcerpt),
                'checkpoints' => [
                    'Open with the article topic and central claim.',
                    'Include at least one concrete supporting detail from the source.',
                    'Finish with your own response, evaluation, or real-world application.',
                ],
                'rubric_focus' => [
                    'Task achievement',
                    'Clear organization',
                    'Relevant support',
                    'Accurate academic language',
                ],
            ],
            'paraphrase' => [
                'task_type' => 'paraphrase',
                'title' => 'Paraphrase Studio',
                'badge' => 'Low-similarity rewrite',
                'instruction' => 'Rewrite the source excerpt in your own words while keeping the meaning accurate and reducing similarity.',
                'requirement' => 'Change sentence structure, vocabulary, and transitions. Do not copy long chunks from the source text.',
                'source_text' => $paraphraseExcerpt,
                'word_limit' => $this->buildParaphraseWordLimit($paraphraseExcerpt),
                'checkpoints' => [
                    'Keep the original meaning accurate.',
                    'Avoid copying long phrases directly from the source.',
                    'Use at least one new connector or sentence pattern.',
                ],
                'rubric_focus' => [
                    'Meaning accuracy',
                    'Paraphrase depth',
                    'Vocabulary variation',
                    'Sentence control',
                ],
            ],
            'opinion' => [
                'task_type' => 'opinion',
                'title' => 'Opinion Builder',
                'badge' => 'Argument writing',
                'instruction' => 'Take a position on the article topic and support it with reasons or examples.',
                'requirement' => 'State your view clearly, connect it to the article, and develop at least one supporting reason or example.',
                'source_text' => $opinionExcerpt,
                'word_limit' => $this->buildOpinionWordLimit($opinionExcerpt),
                'checkpoints' => [
                    'State a clear opinion early in the response.',
                    'Refer to the article topic or one of its key ideas.',
                    'Support your position with explanation, evidence, or an example.',
                ],
                'rubric_focus' => [
                    'Clear stance',
                    'Logical support',
                    'Coherent flow',
                    'Academic tone',
                ],
            ],
        ];
    }

    protected function buildExcerpt(
        \Illuminate\Support\Collection $sentences,
        int $minWords,
        int $maxChars,
        string $fallback,
        int $preferredSentenceCount = 3
    ): string {
        $excerptSentences = [];
        $wordCount = 0;

        foreach ($sentences as $index => $sentence) {
            $excerptSentences[] = trim((string) $sentence);
            $wordCount += $this->countWords((string) $sentence);

            if ($index + 1 >= $preferredSentenceCount && $wordCount >= $minWords) {
                break;
            }
        }

        $excerpt = trim(implode(' ', array_filter($excerptSentences)));

        if ($excerpt === '') {
            $excerpt = trim($fallback);
        }

        return Str::limit($excerpt, $maxChars);
    }

    protected function countWords(string $text): int
    {
        preg_match_all("/[A-Za-z][A-Za-z0-9'-]*/", $text, $matches);

        return count($matches[0] ?? []);
    }

    protected function normalizeWordLimit(mixed $wordLimit): array
    {
        $min = max(20, (int) data_get($wordLimit, 'min', 120));
        $max = max($min, (int) data_get($wordLimit, 'max', max($min + 40, 180)));

        return ['min' => $min, 'max' => $max];
    }

    protected function resolveWordLimit(string $taskType, string $sourceText, mixed $wordLimit, array $defaultWordLimit): array
    {
        return match ($taskType) {
            'paraphrase' => $this->buildParaphraseWordLimit($sourceText),
            'summary_response' => $this->buildSummaryWordLimit($sourceText),
            'opinion' => $this->buildOpinionWordLimit($sourceText),
            default => $this->normalizeWordLimit($wordLimit ?? $defaultWordLimit),
        };
    }

    protected function buildParaphraseWordLimit(string $sourceText): array
    {
        $sourceWords = max(1, $this->countWords($sourceText));
        $min = max(20, min($sourceWords, (int) floor($sourceWords * 0.8)));
        $max = max(
            $min + 6,
            min(90, (int) ceil($sourceWords * 1.25))
        );

        return ['min' => $min, 'max' => $max];
    }

    protected function buildSummaryWordLimit(string $sourceText): array
    {
        $sourceWords = max(1, $this->countWords($sourceText));
        $minFloor = $sourceWords < 50 ? 60 : 90;
        $min = max($minFloor, min(160, (int) round($sourceWords * 1.35)));
        $max = max(
            $min + ($sourceWords < 50 ? 25 : 30),
            min(220, (int) round($sourceWords * 1.8))
        );

        return ['min' => $min, 'max' => $max];
    }

    protected function buildOpinionWordLimit(string $sourceText): array
    {
        $sourceWords = max(1, $this->countWords($sourceText));
        $minFloor = $sourceWords < 45 ? 50 : 80;
        $min = max($minFloor, min(145, (int) round($sourceWords * 1.1)));
        $max = max(
            $min + ($sourceWords < 45 ? 25 : 30),
            min(205, (int) round($sourceWords * 1.45))
        );

        return ['min' => $min, 'max' => $max];
    }

    protected function shouldReplaceSourceText(string $taskType, string $currentSource): bool
    {
        $wordCount = $this->countWords($currentSource);

        if (trim($currentSource) === '') {
            return true;
        }

        return match ($taskType) {
            'summary_response' => $wordCount < 55 || $wordCount > 170,
            'opinion' => $wordCount < 45 || $wordCount > 140,
            'paraphrase' => $wordCount < 20 || $wordCount > 95,
            default => false,
        };
    }
}
