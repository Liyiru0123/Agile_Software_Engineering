<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Exercise;
use App\Models\ReadingQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ReadingExerciseService
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

    public function getPublicQuestions(Article $article): array
    {
        return collect($this->getQuestions($article))
            ->map(fn (array $question) => [
                'id' => $question['id'],
                'question_text' => $question['question_text'],
                'question_type' => $question['question_type'],
                'options' => $question['options'],
            ])
            ->values()
            ->all();
    }

    public function evaluate(Article $article, array $submittedAnswers): array
    {
        $questions = collect($this->getQuestions($article))->keyBy('id');
        $answerMap = collect($submittedAnswers)
            ->filter(fn ($item) => is_array($item) && isset($item['question_id']))
            ->keyBy(fn (array $item) => (string) $item['question_id']);

        $results = [];
        $correctCount = 0;

        foreach ($questions as $questionId => $question) {
            $selected = strtoupper(trim((string) ($answerMap->get($questionId)['selected'] ?? '')));
            $correctAnswer = strtoupper((string) $question['correct_answer']);
            $isCorrect = $selected !== '' && $selected === $correctAnswer;

            if ($isCorrect) {
                $correctCount++;
            }

            $results[] = [
                'question_id' => $question['id'],
                'question_text' => $question['question_text'],
                'question_type' => $question['question_type'],
                'your_answer' => $selected !== '' ? $selected : null,
                'correct_answer' => $question['correct_answer'],
                'correct_option_text' => $question['correct_option_text'],
                'is_correct' => $isCorrect,
                'explanation' => $question['explanation'],
                'source_excerpt' => $question['source_excerpt'],
                'source_anchor' => $question['source_anchor'],
                'source_label' => $question['source_label'],
            ];
        }

        $total = count($results);

        return [
            'article_id' => $article->id,
            'total' => $total,
            'correct_count' => $correctCount,
            'wrong_count' => $total - $correctCount,
            'score' => $total > 0 ? round(($correctCount / $total) * 100, 2) : 0,
            'results' => $results,
        ];
    }

    public function getQuestions(Article $article): array
    {
        $questions = $this->loadQuestionsTableRows($article);

        if ($questions !== []) {
            return $questions;
        }

        return $this->loadExerciseRows($article);
    }

    protected function loadQuestionsTableRows(Article $article): array
    {
        if (! Schema::hasTable('questions')) {
            return [];
        }

        $rows = ReadingQuestion::query()
            ->where('article_id', $article->id)
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $sentenceMap = $this->buildSentenceMap($article->content);

        return $rows->map(function (ReadingQuestion $question, int $index) use ($sentenceMap) {
            $options = $this->normalizeOptions($question->options ?? []);
            $correctAnswer = strtoupper(trim((string) $question->correct_answer));
            $correctOptionText = $this->resolveCorrectOptionText($options, $correctAnswer);
            $questionType = $this->inferQuestionType((string) $question->question_text, $question->getAttribute('question_type'));
            $source = $this->locateSourceSentence(
                $sentenceMap,
                (string) $question->question_text,
                $correctOptionText,
                $question->getAttribute('source_excerpt')
            );

            return $this->buildNormalizedQuestion(
                id: 'question-'.$question->id,
                questionText: (string) $question->question_text,
                questionType: $questionType,
                options: $options,
                correctAnswer: $correctAnswer,
                correctOptionText: $correctOptionText,
                explanation: $this->buildExplanation(
                    explanation: $question->explanation,
                    questionType: $questionType,
                    correctAnswer: $correctAnswer,
                    correctOptionText: $correctOptionText,
                    sourceExcerpt: $source['sentence'] ?? null
                ),
                source: $source,
                fallbackIndex: $index
            );
        })->values()->all();
    }

    protected function loadExerciseRows(Article $article): array
    {
        $readingExercises = $article->exercises()
            ->where('type', 'reading')
            ->orderBy('id')
            ->get();

        if ($readingExercises->isEmpty()) {
            return [];
        }

        $sentenceMap = $this->buildSentenceMap($article->content);
        $questions = [];

        foreach ($readingExercises as $exercise) {
            $questionData = is_array($exercise->question_data) ? $exercise->question_data : [];
            $rawQuestions = $this->extractRawQuestions($questionData);

            foreach ($rawQuestions as $index => $rawQuestion) {
                $questionText = trim((string) ($rawQuestion['question_text'] ?? $rawQuestion['question'] ?? ''));
                $options = $this->normalizeOptions($rawQuestion['options'] ?? $questionData['options'] ?? []);

                if ($questionText === '' || $options === []) {
                    continue;
                }

                $correctAnswer = $this->resolveCorrectAnswer($exercise, $rawQuestion, $index);

                if ($correctAnswer === null) {
                    continue;
                }

                $correctAnswer = strtoupper($correctAnswer);
                $correctOptionText = $this->resolveCorrectOptionText($options, $correctAnswer);
                $questionType = $this->inferQuestionType($questionText, $rawQuestion['question_type'] ?? $rawQuestion['type'] ?? null);
                $source = $this->locateSourceSentence(
                    $sentenceMap,
                    $questionText,
                    $correctOptionText,
                    $rawQuestion['source_excerpt'] ?? $rawQuestion['source_sentence'] ?? null
                );

                $questions[] = $this->buildNormalizedQuestion(
                    id: 'exercise-'.$exercise->id.'-'.($index + 1),
                    questionText: $questionText,
                    questionType: $questionType,
                    options: $options,
                    correctAnswer: $correctAnswer,
                    correctOptionText: $correctOptionText,
                    explanation: $this->buildExplanation(
                        explanation: $rawQuestion['explanation'] ?? null,
                        questionType: $questionType,
                        correctAnswer: $correctAnswer,
                        correctOptionText: $correctOptionText,
                        sourceExcerpt: $source['sentence'] ?? null
                    ),
                    source: $source,
                    fallbackIndex: $index
                );
            }
        }

        return array_values($questions);
    }

    protected function extractRawQuestions(array $questionData): array
    {
        if (is_array($questionData['questions'] ?? null)) {
            return array_values(array_filter($questionData['questions'], 'is_array'));
        }

        return [$questionData];
    }

    protected function resolveCorrectAnswer(Exercise $exercise, array $rawQuestion, int $index): ?string
    {
        $candidate = $rawQuestion['correct_answer'] ?? $rawQuestion['answer'] ?? null;

        if (is_string($candidate) && $candidate !== '') {
            return $candidate;
        }

        $answerData = $exercise->answer;

        if (is_string($answerData) && $answerData !== '') {
            return $answerData;
        }

        if (! is_array($answerData)) {
            return null;
        }

        $lookup = $answerData['questions'] ?? $answerData;

        foreach ([(string) ($index + 1), $index + 1, $rawQuestion['id'] ?? null] as $key) {
            if ($key === null || ! array_key_exists($key, $lookup)) {
                continue;
            }

            $value = $lookup[$key];

            if (is_string($value) && $value !== '') {
                return $value;
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

    protected function inferQuestionType(string $questionText, mixed $explicitType = null): string
    {
        if (is_string($explicitType) && trim($explicitType) !== '') {
            return Str::of($explicitType)->lower()->replace('_', ' ')->headline()->toString();
        }

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

    protected function buildExplanation(?string $explanation, string $questionType, string $correctAnswer, string $correctOptionText, ?string $sourceExcerpt): string
    {
        if (is_string($explanation) && trim($explanation) !== '') {
            return trim($explanation);
        }

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

    protected function buildNormalizedQuestion(
        string $id,
        string $questionText,
        string $questionType,
        array $options,
        string $correctAnswer,
        string $correctOptionText,
        string $explanation,
        array $source,
        int $fallbackIndex
    ): array {
        return [
            'id' => $id,
            'order' => $fallbackIndex + 1,
            'question_text' => $questionText,
            'question_type' => $questionType,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'correct_option_text' => $correctOptionText,
            'explanation' => $explanation,
            'source_excerpt' => $source['sentence'] ?? null,
            'source_anchor' => $source['anchor'] ?? null,
            'source_label' => $source['label'] ?? null,
        ];
    }
}
