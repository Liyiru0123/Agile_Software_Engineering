<?php

namespace App;

use App\Models\AiPrompt;
use App\Models\Article;
use App\Models\Exercise;
use App\Models\Submission;
use App\Services\ArticleTextProcessor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WritingExerciseService
{
    protected array $lastAiFailures = [];

    private const CORE_TASK_TYPES = [
        'summary_response',
        'paraphrase',
        'opinion',
    ];

    private const CONNECTORS = [
        'however', 'therefore', 'moreover', 'furthermore', 'because', 'although',
        'for example', 'for instance', 'as a result', 'in addition', 'overall',
        'meanwhile', 'instead', 'consequently', 'while', 'thus',
    ];

    private const OPINION_MARKERS = [
        'i think', 'i believe', 'in my view', 'from my perspective', 'personally',
        'i would argue', 'i agree', 'i disagree', 'this shows', 'this suggests',
        'should', 'could', 'in practice',
    ];

    private const STOPWORDS = [
        'about', 'after', 'also', 'because', 'been', 'being', 'between', 'could',
        'does', 'from', 'have', 'however', 'into', 'just', 'more', 'most', 'must',
        'only', 'other', 'over', 'should', 'some', 'such', 'than', 'that', 'their',
        'there', 'these', 'they', 'this', 'through', 'using', 'were', 'which',
        'while', 'with', 'would',
    ];

    public function __construct(
        protected ArticleTextProcessor $processor
    ) {
    }

    public function buildPagePayload(Article $article, ?int $userId = null): array
    {
        $exercises = $this->getOrCreateExercises($article);

        $latestSubmissions = collect();
        $recentSubmissions = [];

        if ($userId) {
            $exerciseIds = $exercises->pluck('id');
            $latestSubmissions = Submission::query()
                ->where('user_id', $userId)
                ->where('article_id', $article->id)
                ->whereIn('exercise_id', $exerciseIds)
                ->orderByDesc('id')
                ->get()
                ->unique('exercise_id')
                ->keyBy('exercise_id');

            $recentSubmissions = Submission::query()
                ->where('user_id', $userId)
                ->where('article_id', $article->id)
                ->whereIn('exercise_id', $exerciseIds)
                ->orderByDesc('id')
                ->take(6)
                ->get()
                ->map(function (Submission $submission) use ($exercises) {
                    $exercise = $exercises->firstWhere('id', $submission->exercise_id);
                    $task = $exercise ? $this->presentExercise($exercise->article, $exercise) : null;
                    $advice = $submission->ai_advice ?? [];

                    return [
                        'submission_id' => $submission->id,
                        'exercise_id' => $submission->exercise_id,
                        'task_title' => $task['title'] ?? 'Writing Task',
                        'score' => (float) ($submission->score ?? 0),
                        'provider' => $advice['provider'] ?? 'local-rubric',
                        'attempt_count' => (int) ($submission->attempt_count ?? 1),
                        'summary' => $advice['summary'] ?? 'Feedback saved for this attempt.',
                        'submitted_at' => optional($submission->created_at)->format('Y-m-d H:i'),
                    ];
                })
                ->values()
                ->all();
        }

        return [
            'writingTasks' => $exercises
                ->map(fn (Exercise $exercise) => $this->presentExercise(
                    article: $article,
                    exercise: $exercise,
                    latestSubmission: $latestSubmissions->get($exercise->id),
                ))
                ->values()
                ->all(),
            'recentWritingSubmissions' => $recentSubmissions,
            'aiReady' => $this->resolveAiProvider() !== null,
            'aiProviderLabel' => $this->resolveAiProviderLabel(),
        ];
    }

    public function evaluateSubmission(
        Article $article,
        Exercise $exercise,
        string $draft,
        ?int $userId = null,
        int $timeSpent = 0
    ): array {
        $this->lastAiFailures = [];

        $task = $this->presentExercise($article, $exercise);
        $cleanDraft = trim($draft);
        $wordCount = $this->countWords($cleanDraft);

        $result = $this->evaluateWithGemini($article, $task, $cleanDraft, $wordCount);

        if (! $result) {
            $result = $this->evaluateLocally($article, $task, $cleanDraft, $wordCount);
            $result['ai_diagnostics'] = $this->buildAiDiagnostics();
        }

        $result['exercise_id'] = $exercise->id;
        $result['task_type'] = $task['task_type'];
        $result['task_title'] = $task['title'];
        $result['submitted_text'] = $cleanDraft;
        $result['word_count'] = $wordCount;
        $result['time_spent'] = $timeSpent;

        if ($userId) {
            $attemptCount = (int) Submission::query()
                ->where('user_id', $userId)
                ->where('exercise_id', $exercise->id)
                ->max('attempt_count');

            $submission = Submission::query()->create([
                'user_id' => $userId,
                'exercise_id' => $exercise->id,
                'article_id' => $article->id,
                'user_answer' => [
                    'text' => $cleanDraft,
                    'task_type' => $task['task_type'],
                    'word_count' => $wordCount,
                ],
                'score' => $result['score'],
                'time_spent' => $timeSpent,
                'attempt_count' => $attemptCount + 1,
                'ai_advice' => $result,
            ]);

            $result['submission_id'] = $submission->id;
            $result['attempt_count'] = $submission->attempt_count;
            $result['submitted_at'] = optional($submission->created_at)->format('Y-m-d H:i');
        }

        return $result;
    }

    protected function getOrCreateExercises(Article $article): Collection
    {
        $exercises = $article->exercises()
            ->where('type', 'writing')
            ->orderBy('id')
            ->get();

        $existingTypes = $exercises
            ->map(fn (Exercise $exercise) => $this->detectTaskType($exercise->question_data ?? []))
            ->unique()
            ->values()
            ->all();

        $missingTypes = array_values(array_diff(self::CORE_TASK_TYPES, $existingTypes));

        if ($missingTypes !== []) {
            $aiPromptId = AiPrompt::query()
                ->where('type', 'writing')
                ->value('id');

            $blueprints = $this->defaultTaskBlueprints($article);

            foreach ($missingTypes as $taskType) {
                $task = $blueprints[$taskType];

                Exercise::query()->create([
                    'article_id' => $article->id,
                    'type' => 'writing',
                    'question_data' => $task + ['provider' => 'generated-fallback'],
                    'answer' => null,
                    'ai_prompt_id' => $aiPromptId,
                ]);
            }

            $exercises = $article->exercises()
                ->where('type', 'writing')
                ->orderBy('id')
                ->get();
        }

        return $exercises;
    }

    protected function presentExercise(Article $article, Exercise $exercise, ?Submission $latestSubmission = null): array
    {
        $questionData = $exercise->question_data ?? [];
        $taskType = $this->detectTaskType($questionData);
        $defaults = $this->defaultTaskBlueprints($article)[$taskType];
        $wordLimit = $this->normalizeWordLimit($questionData['word_limit'] ?? $defaults['word_limit']);

        return [
            'id' => $exercise->id,
            'task_type' => $taskType,
            'title' => $questionData['title'] ?? $defaults['title'],
            'badge' => $questionData['badge'] ?? $defaults['badge'],
            'instruction' => $questionData['instruction'] ?? $defaults['instruction'],
            'requirement' => $questionData['requirement'] ?? $defaults['requirement'],
            'source_text' => $questionData['source_text'] ?? $defaults['source_text'],
            'word_limit' => $wordLimit,
            'checkpoints' => array_values($questionData['checkpoints'] ?? $defaults['checkpoints']),
            'rubric_focus' => array_values($questionData['rubric_focus'] ?? $defaults['rubric_focus']),
            'provider' => $questionData['provider'] ?? 'database',
            'draft_key' => 'writing-draft-'.$article->id.'-'.$exercise->id,
            'latest_result' => $latestSubmission ? $this->formatStoredResult($latestSubmission) : null,
        ];
    }

    protected function defaultTaskBlueprints(Article $article): array
    {
        $paragraphs = $this->processor->splitParagraphs($article->content);
        $sentences = collect($paragraphs)
            ->flatMap(fn (string $paragraph) => $this->processor->splitSentences($paragraph))
            ->filter()
            ->values();

        $summaryExcerpt = Str::limit($sentences->take(3)->implode(' '), 320);
        $paraphraseExcerpt = Str::limit($sentences->first() ?: $article->content, 220);

        return [
            'summary_response' => [
                'task_type' => 'summary_response',
                'title' => 'Summary + Response',
                'badge' => 'Integrated writing',
                'instruction' => 'Summarize the article clearly, then explain your own judgment or application of the main idea.',
                'requirement' => 'Write one concise summary paragraph and one short response paragraph. Mention the main claim, one supporting detail, and your own evaluation.',
                'source_text' => $summaryExcerpt,
                'word_limit' => ['min' => 130, 'max' => 190],
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
                'word_limit' => ['min' => 45, 'max' => 110],
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
                'source_text' => $summaryExcerpt,
                'word_limit' => ['min' => 120, 'max' => 180],
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

    protected function detectTaskType(array $questionData): string
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

    protected function normalizeWordLimit(mixed $wordLimit): array
    {
        $min = max(20, (int) data_get($wordLimit, 'min', 120));
        $max = max($min, (int) data_get($wordLimit, 'max', max($min + 40, 180)));

        return ['min' => $min, 'max' => $max];
    }

    protected function evaluateLocally(Article $article, array $task, string $draft, int $wordCount): array
    {
        $wordLimit = $task['word_limit'];
        $sentences = $this->splitSentences($draft);
        $paragraphs = preg_split("/\r\n\r\n|\n\n/", trim($draft)) ?: [];
        $paragraphCount = max(1, count(array_filter(array_map('trim', $paragraphs))));
        $keywordCoverage = $this->keywordCoverage($article, $task, $draft);
        $connectorCount = $this->countConnectors($draft);
        $lexicalDiversity = $this->lexicalDiversity($draft);
        $similarity = $this->similarity($task['source_text'], $draft);
        $opinionSignal = $this->containsAny($draft, self::OPINION_MARKERS) ? 1.0 : 0.45;
        $sentenceCount = max(1, count($sentences));
        $wordRangeScore = $this->wordRangeScore($wordCount, $wordLimit['min'], $wordLimit['max']);

        $taskSignal = match ($task['task_type']) {
            'paraphrase' => $this->scoreFromBands($similarity, [
                0.23 => 1.0,
                0.35 => 0.82,
                0.48 => 0.62,
                1.00 => 0.35,
            ]),
            'opinion' => ($opinionSignal + min(1, $sentenceCount / 4) + $keywordCoverage) / 3,
            default => ($keywordCoverage + $opinionSignal + min(1, $sentenceCount / 4)) / 3,
        };

        $taskAchievement = round((($wordRangeScore * 0.25) + ($keywordCoverage * 0.4) + ($taskSignal * 0.35)) * 25, 1);

        $coherence = round(((
            $this->scoreFromBands($sentenceCount, [
                2 => 0.35,
                4 => 0.82,
                7 => 1.0,
                20 => 0.7,
            ]) * 0.35
        ) + (
            $this->scoreFromBands($connectorCount, [
                0 => 0.32,
                1 => 0.7,
                2 => 0.9,
                10 => 1.0,
            ]) * 0.35
        ) + (
            $this->scoreFromBands($paragraphCount, [
                1 => 0.7,
                2 => 1.0,
                3 => 0.92,
                10 => 0.68,
            ]) * 0.3
        )) * 25, 1);

        $longWordRatio = $this->longWordRatio($draft);
        $repetitionPenalty = $this->repetitionPenalty($draft);
        $lexical = round(((
            $this->scoreFromBands($lexicalDiversity, [
                0.20 => 0.25,
                0.35 => 0.65,
                0.50 => 1.0,
                0.90 => 0.82,
            ]) * 0.55
        ) + (
            $this->scoreFromBands($longWordRatio, [
                0.08 => 0.3,
                0.18 => 0.7,
                0.30 => 1.0,
                1.00 => 0.92,
            ]) * 0.25
        ) + ((1 - $repetitionPenalty) * 0.2)) * 25, 1);

        $grammar = round(((
            $this->capitalizationRatio($sentences) * 0.3
        ) + (
            $this->punctuationRatio($sentences) * 0.3
        ) + (
            $this->scoreFromBands($this->averageSentenceLength($draft), [
                4 => 0.32,
                8 => 0.75,
                18 => 1.0,
                35 => 0.72,
            ]) * 0.25
        ) + (
            $this->scoreFromBands($wordCount, [
                20 => 0.25,
                60 => 0.72,
                120 => 1.0,
                260 => 0.88,
            ]) * 0.15
        )) * 25, 1);

        $score = round($taskAchievement + $coherence + $lexical + $grammar, 2);
        $breakdown = [
            [
                'key' => 'task_achievement',
                'label' => 'Task Achievement',
                'score' => $taskAchievement,
                'max' => 25,
                'feedback' => $this->taskAchievementFeedback($task, $keywordCoverage, $similarity, $wordRangeScore),
            ],
            [
                'key' => 'coherence',
                'label' => 'Coherence & Cohesion',
                'score' => $coherence,
                'max' => 25,
                'feedback' => $connectorCount >= 1
                    ? 'The response has some visible structure and linking.'
                    : 'Add clearer transitions and paragraphing to make the ideas easier to follow.',
            ],
            [
                'key' => 'lexical_resource',
                'label' => 'Lexical Resource',
                'score' => $lexical,
                'max' => 25,
                'feedback' => $lexicalDiversity >= 0.42
                    ? 'Word choice is reasonably varied for this draft length.'
                    : 'Try replacing repeated common words with more precise academic vocabulary.',
            ],
            [
                'key' => 'grammar',
                'label' => 'Grammar & Accuracy',
                'score' => $grammar,
                'max' => 25,
                'feedback' => $this->punctuationRatio($sentences) >= 0.8
                    ? 'Most sentences are punctuated in a readable way.'
                    : 'Review sentence boundaries, capitalization, and end punctuation.',
            ],
        ];

        $strengths = [];
        $improvements = [];

        if ($taskAchievement >= 18) {
            $strengths[] = 'The draft addresses the task directly and stays close to the article topic.';
        }

        if ($coherence >= 18) {
            $strengths[] = 'The writing has a visible beginning, development, and conclusion.';
        }

        if ($lexical >= 18) {
            $strengths[] = 'Vocabulary range is solid for this level of writing practice.';
        }

        if ($grammar >= 18) {
            $strengths[] = 'Sentence control is generally stable and readable.';
        }

        if ($wordRangeScore < 0.95) {
            $improvements[] = "Adjust the length to stay within {$wordLimit['min']}-{$wordLimit['max']} words.";
        }

        if ($task['task_type'] === 'paraphrase' && $similarity > 0.35) {
            $improvements[] = 'Change more sentence patterns and vocabulary so the paraphrase is less dependent on the source wording.';
        }

        if ($task['task_type'] !== 'paraphrase' && $keywordCoverage < 0.45) {
            $improvements[] = 'Refer more directly to the article\'s key ideas so the response feels grounded in the source.';
        }

        if ($connectorCount === 0) {
            $improvements[] = 'Use connectors such as however, therefore, or for example to improve flow.';
        }

        if ($grammar < 17) {
            $improvements[] = 'Proofread for sentence boundaries, capitalization, and punctuation before submitting.';
        }

        if ($strengths === []) {
            $strengths[] = 'The draft gives you a workable base to revise from.';
        }

        if ($improvements === []) {
            $improvements[] = 'The next step is to polish vocabulary choice and tighten a few sentences for a more academic tone.';
        }

        $summary = match (true) {
            $score >= 85 => 'A strong response with good control of the task and language.',
            $score >= 70 => 'A solid draft that communicates clearly, with a few areas to refine.',
            $score >= 55 => 'The main idea is understandable, but clearer support and editing would raise the score.',
            default => 'The draft needs more development, structure, and language control to meet the task well.',
        };

        return [
            'provider' => 'local-rubric',
            'score' => $score,
            'summary' => $summary,
            'breakdown' => $breakdown,
            'strengths' => array_values(array_slice(array_unique($strengths), 0, 3)),
            'improvements' => array_values(array_slice(array_unique($improvements), 0, 4)),
            'suggested_revision' => $this->buildRevisionAdvice($task, $wordLimit, $similarity),
            'word_range' => [
                'min' => $wordLimit['min'],
                'max' => $wordLimit['max'],
                'in_range' => $wordCount >= $wordLimit['min'] && $wordCount <= $wordLimit['max'],
            ],
            'metrics' => [
                'keyword_coverage' => round($keywordCoverage, 2),
                'source_similarity' => round($similarity, 2),
                'connector_count' => $connectorCount,
                'lexical_diversity' => round($lexicalDiversity, 2),
            ],
        ];
    }

    protected function evaluateWithGemini(Article $article, array $task, string $draft, int $wordCount): ?array
    {
        if (! filled(config('services.gemini.api_key'))) {
            return null;
        }

        $exercisePromptId = Exercise::query()->whereKey($task['id'])->value('ai_prompt_id');
        $aiPrompt = AiPrompt::query()
            ->where('id', $exercisePromptId)
            ->orWhere('type', 'writing')
            ->first();

        $prompt = <<<PROMPT
{$aiPrompt?->prompt}

Return valid JSON only with this shape:
{
  "score": 0,
  "summary": "string",
  "breakdown": [
    {"key": "task_achievement", "label": "Task Achievement", "score": 0, "max": 25, "feedback": "string"},
    {"key": "coherence", "label": "Coherence & Cohesion", "score": 0, "max": 25, "feedback": "string"},
    {"key": "lexical_resource", "label": "Lexical Resource", "score": 0, "max": 25, "feedback": "string"},
    {"key": "grammar", "label": "Grammar & Accuracy", "score": 0, "max": 25, "feedback": "string"}
  ],
  "strengths": ["string"],
  "improvements": ["string"],
  "suggested_revision": "string"
}

Writing task type: {$task['task_type']}
Writing task title: {$task['title']}
Instruction: {$task['instruction']}
Requirement: {$task['requirement']}
Word limit: {$task['word_limit']['min']}-{$task['word_limit']['max']}
Source text:
{$task['source_text']}

Article title: {$article->title}
Article content:
{$article->content}

Student response ({$wordCount} words):
{$draft}
PROMPT;

        try {
            if ($this->usesCompatibleGeminiApi()) {
                return $this->evaluateWithCompatibleGeminiApi($task, $prompt, $wordCount);
            }

            $response = Http::timeout(60)
                ->post(
                    rtrim((string) config('services.gemini.base_url'), '/').'/'.config('services.gemini.model').':generateContent?key='.config('services.gemini.api_key'),
                    [
                        'contents' => [
                            ['parts' => [['text' => $prompt]]],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2,
                            'responseMimeType' => 'application/json',
                        ],
                    ]
                )
                ->throw();

            $this->storeRawAiResponse(
                provider: 'gemini',
                task: $task,
                status: $response->status(),
                body: $response->body(),
            );

            $payload = $response->json();
            $text = data_get($payload, 'candidates.0.content.parts.0.text');
            if (! is_string($text) || trim($text) === '') {
                $this->recordAiProviderFailure(
                    'gemini',
                    ...$this->diagnoseAiPayloadFailure($payload, 'The provider returned no review text.')
                );

                return null;
            }

            $decoded = $this->decodeJsonPayload($text);
            if (! is_array($decoded)) {
                $this->recordAiProviderFailure(
                    'gemini',
                    'invalid_json',
                    'The provider response was not valid JSON.',
                    $this->summarizeRawText($text)
                );

                return null;
            }

            $normalized = $this->normalizeAiEvaluation($decoded, 'gemini', $task, $wordCount);
            if (! $normalized) {
                $this->recordAiProviderFailure('gemini', 'invalid_schema', 'The provider response was missing required scoring fields.');

                return null;
            }

            return $normalized;
        } catch (\Throwable $exception) {
            $this->logAiProviderFailure('gemini', $task, $exception);
            report($exception);

            return null;
        }
    }

    protected function evaluateWithCompatibleGeminiApi(array $task, string $prompt, int $wordCount): ?array
    {
        $response = Http::timeout(90)
            ->withToken((string) config('services.gemini.api_key'))
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post(
                rtrim((string) config('services.gemini.base_url'), '/').'/chat/completions',
                [
                    'model' => (string) config('services.gemini.model', 'gemini-2.5-flash'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a strict but fair English writing examiner.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.2,
                ]
            )
            ->throw();

        $this->storeRawAiResponse(
            provider: 'gemini-compatible',
            task: $task,
            status: $response->status(),
            body: $response->body(),
        );

        $payload = $response->json();
        $text = $this->extractResponsesText($payload);

        if (! is_string($text) || trim($text) === '') {
            $this->recordAiProviderFailure(
                'gemini',
                ...$this->diagnoseAiPayloadFailure($payload, 'The provider returned no review text.')
            );

            return null;
        }

        $decoded = $this->decodeJsonPayload($text);
        if (! is_array($decoded)) {
            $this->recordAiProviderFailure(
                'gemini',
                'invalid_json',
                'The provider response was not valid JSON.',
                $this->summarizeRawText($text)
            );

            return null;
        }

        $normalized = $this->normalizeAiEvaluation($decoded, 'gemini', $task, $wordCount);
        if (! $normalized) {
            $this->recordAiProviderFailure('gemini', 'invalid_schema', 'The provider response was missing required scoring fields.');

            return null;
        }

        return $normalized;
    }

    protected function logAiProviderFailure(string $provider, array $task, \Throwable $exception): void
    {
        $reason = $exception instanceof \Illuminate\Http\Client\ConnectionException
            ? 'connection_error'
            : 'request_error';

        $this->recordAiProviderFailure($provider, $reason, $exception->getMessage());

        Log::warning('Writing AI provider failed and fell back to local rubric.', [
            'provider' => $provider,
            'task_id' => $task['id'] ?? null,
            'task_type' => $task['task_type'] ?? null,
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'raw_excerpt' => null,
        ]);
    }

    protected function recordAiProviderFailure(string $provider, string $reason, string $message, ?string $rawExcerpt = null): void
    {
        $this->lastAiFailures[] = [
            'provider' => $provider,
            'reason' => $reason,
            'message' => $message,
            'raw_excerpt' => $rawExcerpt,
        ];
    }

    protected function buildAiDiagnostics(): array
    {
        if ($this->lastAiFailures === []) {
            return [
                'summary' => 'No remote AI provider is configured, so this review used the local rubric.',
                'attempts' => [],
            ];
        }

        $attempts = collect($this->lastAiFailures)
            ->map(function (array $failure) {
                return [
                    'provider' => (string) ($failure['provider'] ?? 'unknown'),
                    'reason' => (string) ($failure['reason'] ?? 'unknown'),
                    'message' => (string) ($failure['message'] ?? 'Unknown AI provider failure.'),
                    'raw_excerpt' => $failure['raw_excerpt'] ?? null,
                ];
            })
            ->values()
            ->all();

        $firstFailure = $attempts[0];

        return [
            'summary' => sprintf(
                'AI review fallback: %s failed with %s.',
                strtoupper((string) $firstFailure['provider']),
                str_replace('_', ' ', (string) $firstFailure['reason'])
            ),
            'attempts' => $attempts,
        ];
    }

    protected function normalizeAiEvaluation(?array $decoded, string $provider, array $task, int $wordCount): ?array
    {
        if (! is_array($decoded)) {
            return null;
        }

        $decoded['score'] = round((float) ($decoded['score'] ?? 0), 2);
        $decoded['provider'] = $provider;
        $decoded['strengths'] = array_values(array_slice(array_filter($decoded['strengths'] ?? [], 'is_string'), 0, 4));
        $decoded['improvements'] = array_values(array_slice(array_filter($decoded['improvements'] ?? [], 'is_string'), 0, 5));
        $decoded['breakdown'] = collect($decoded['breakdown'] ?? [])
            ->map(function ($row) {
                return [
                    'key' => (string) ($row['key'] ?? Str::slug((string) ($row['label'] ?? 'criterion'), '_')),
                    'label' => (string) ($row['label'] ?? 'Criterion'),
                    'score' => round((float) ($row['score'] ?? 0), 1),
                    'max' => round((float) ($row['max'] ?? 25), 1),
                    'feedback' => (string) ($row['feedback'] ?? ''),
                ];
            })
            ->filter(fn (array $row) => $row['label'] !== '')
            ->take(4)
            ->values()
            ->all();

        if (($decoded['summary'] ?? '') === '' || count($decoded['breakdown']) !== 4) {
            return null;
        }

        $decoded['word_range'] = [
            'min' => $task['word_limit']['min'],
            'max' => $task['word_limit']['max'],
            'in_range' => $wordCount >= $task['word_limit']['min'] && $wordCount <= $task['word_limit']['max'],
        ];

        return $decoded;
    }

    protected function diagnoseAiPayloadFailure(array $payload, string $fallbackMessage): array
    {
        $providerMessage = trim((string) data_get($payload, 'error.message', ''));
        if ($providerMessage !== '') {
            return ['provider_error', $providerMessage, $this->summarizePayload($payload)];
        }

        return ['empty_response', $fallbackMessage, $this->summarizePayload($payload)];
    }

    protected function summarizePayload(array $payload): ?string
    {
        if ($payload === []) {
            return null;
        }

        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $this->summarizeRawText($encoded ?: null);
    }

    protected function summarizeRawText(?string $text): ?string
    {
        if (! is_string($text) || trim($text) === '') {
            return null;
        }

        return Str::limit(preg_replace('/\s+/', ' ', trim($text)) ?? trim($text), 240);
    }

    protected function storeRawAiResponse(string $provider, array $task, int $status, string $body): void
    {
        $path = storage_path('logs/writing-ai-raw.log');
        $directory = dirname($path);

        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $entry = [
            'timestamp' => now()->toDateTimeString(),
            'provider' => $provider,
            'task_id' => $task['id'] ?? null,
            'task_type' => $task['task_type'] ?? null,
            'status' => $status,
            'body' => $body,
        ];

        File::append($path, json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL);
    }

    protected function extractResponsesText(array $payload): ?string
    {
        $outputText = data_get($payload, 'output_text');
        if (is_string($outputText) && trim($outputText) !== '') {
            return $outputText;
        }

        $outputs = data_get($payload, 'output', []);
        if (is_array($outputs)) {
            foreach ($outputs as $output) {
                $contentItems = data_get($output, 'content', []);
                if (! is_array($contentItems)) {
                    continue;
                }

                foreach ($contentItems as $item) {
                    $text = data_get($item, 'text')
                        ?? data_get($item, 'output_text')
                        ?? data_get($item, 'content.0.text');

                    if (is_string($text) && trim($text) !== '') {
                        return $text;
                    }
                }
            }
        }

        $messageText = data_get($payload, 'choices.0.message.content');
        if (is_string($messageText) && trim($messageText) !== '') {
            return $messageText;
        }

        if (is_array($messageText)) {
            foreach ($messageText as $item) {
                $text = data_get($item, 'text');
                if (is_string($text) && trim($text) !== '') {
                    return $text;
                }
            }
        }

        return null;
    }

    protected function resolveAiProvider(): ?string
    {
        if (filled(config('services.gemini.api_key'))) {
            return 'gemini';
        }

        return null;
    }

    protected function resolveAiProviderLabel(): string
    {
        return match ($this->resolveAiProvider()) {
            'gemini' => 'Gemini review ready',
            default => 'Local rubric fallback',
        };
    }

    protected function usesCompatibleGeminiApi(): bool
    {
        return ! str_contains((string) config('services.gemini.base_url'), 'generativelanguage.googleapis.com');
    }

    protected function formatStoredResult(Submission $submission): array
    {
        $advice = $submission->ai_advice ?? [];
        $answer = $submission->user_answer ?? [];

        return [
            'submission_id' => $submission->id,
            'score' => (float) ($submission->score ?? data_get($advice, 'score', 0)),
            'provider' => $advice['provider'] ?? 'local-rubric',
            'summary' => $advice['summary'] ?? 'Feedback saved for this attempt.',
            'breakdown' => array_values($advice['breakdown'] ?? []),
            'strengths' => array_values($advice['strengths'] ?? []),
            'improvements' => array_values($advice['improvements'] ?? []),
            'suggested_revision' => $advice['suggested_revision'] ?? '',
            'word_range' => $advice['word_range'] ?? null,
            'metrics' => $advice['metrics'] ?? null,
            'ai_diagnostics' => $advice['ai_diagnostics'] ?? null,
            'submitted_text' => (string) ($answer['text'] ?? ''),
            'word_count' => (int) ($answer['word_count'] ?? 0),
            'attempt_count' => (int) ($submission->attempt_count ?? 1),
            'submitted_at' => optional($submission->created_at)->format('Y-m-d H:i'),
        ];
    }

    protected function splitSentences(string $text): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', trim($text)) ?: [];

        return array_values(array_filter(array_map('trim', $sentences)));
    }

    protected function countWords(string $text): int
    {
        preg_match_all("/[A-Za-z][A-Za-z0-9'-]*/", $text, $matches);

        return count($matches[0] ?? []);
    }

    protected function extractKeywords(string $text): array
    {
        preg_match_all("/[A-Za-z][A-Za-z'-]{4,}/", Str::lower($text), $matches);

        return collect($matches[0] ?? [])
            ->reject(fn (string $word) => in_array($word, self::STOPWORDS, true))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(8)
            ->values()
            ->all();
    }

    protected function keywordCoverage(Article $article, array $task, string $draft): float
    {
        $keywords = collect($this->extractKeywords($task['source_text'].' '.$article->content))
            ->take(6)
            ->values();

        if ($keywords->isEmpty()) {
            return 0.6;
        }

        $normalizedDraft = ' '.Str::lower($draft).' ';
        $matches = $keywords->filter(function (string $keyword) use ($normalizedDraft) {
            return str_contains($normalizedDraft, ' '.Str::lower($keyword).' ');
        })->count();

        return round($matches / max(1, $keywords->count()), 2);
    }

    protected function countConnectors(string $draft): int
    {
        $lowerDraft = Str::lower($draft);
        $count = 0;

        foreach (self::CONNECTORS as $connector) {
            if (str_contains($lowerDraft, $connector)) {
                $count++;
            }
        }

        return $count;
    }

    protected function lexicalDiversity(string $draft): float
    {
        preg_match_all("/[A-Za-z][A-Za-z'-]*/", Str::lower($draft), $matches);
        $words = $matches[0] ?? [];

        if ($words === []) {
            return 0.0;
        }

        return round(count(array_unique($words)) / count($words), 2);
    }

    protected function longWordRatio(string $draft): float
    {
        preg_match_all("/[A-Za-z][A-Za-z'-]*/", $draft, $matches);
        $words = $matches[0] ?? [];

        if ($words === []) {
            return 0.0;
        }

        $longWords = array_filter($words, fn (string $word) => strlen($word) >= 7);

        return round(count($longWords) / count($words), 2);
    }

    protected function repetitionPenalty(string $draft): float
    {
        preg_match_all("/[A-Za-z][A-Za-z'-]*/", Str::lower($draft), $matches);
        $words = $matches[0] ?? [];

        if (count($words) < 6) {
            return 0.0;
        }

        $duplicates = count($words) - count(array_unique($words));

        return min(0.6, round($duplicates / count($words), 2));
    }

    protected function capitalizationRatio(array $sentences): float
    {
        if ($sentences === []) {
            return 0.0;
        }

        $good = collect($sentences)->filter(function (string $sentence) {
            $trimmed = ltrim($sentence);

            return $trimmed !== '' && strtoupper($trimmed[0]) === $trimmed[0];
        })->count();

        return round($good / count($sentences), 2);
    }

    protected function punctuationRatio(array $sentences): float
    {
        if ($sentences === []) {
            return 0.0;
        }

        $good = collect($sentences)->filter(fn (string $sentence) => preg_match('/[.!?]$/', trim($sentence)) === 1)->count();

        return round($good / count($sentences), 2);
    }

    protected function averageSentenceLength(string $draft): float
    {
        $sentences = $this->splitSentences($draft);

        if ($sentences === []) {
            return 0.0;
        }

        $lengths = array_map(fn (string $sentence) => max(1, $this->countWords($sentence)), $sentences);

        return round(array_sum($lengths) / count($lengths), 2);
    }

    protected function similarity(string $source, string $draft): float
    {
        $sourceTokens = $this->normalizedTokens($source);
        $draftTokens = $this->normalizedTokens($draft);

        if ($sourceTokens === [] || $draftTokens === []) {
            return 0.0;
        }

        $sourceSet = array_unique($sourceTokens);
        $draftSet = array_unique($draftTokens);
        $intersection = array_intersect($sourceSet, $draftSet);
        $union = array_unique(array_merge($sourceSet, $draftSet));

        return round(count($intersection) / max(1, count($union)), 2);
    }

    protected function normalizedTokens(string $text): array
    {
        preg_match_all("/[A-Za-z][A-Za-z'-]{3,}/", Str::lower($text), $matches);

        return array_values(array_filter(
            $matches[0] ?? [],
            fn (string $word) => ! in_array($word, self::STOPWORDS, true)
        ));
    }

    protected function containsAny(string $text, array $needles): bool
    {
        $text = Str::lower($text);

        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function wordRangeScore(int $wordCount, int $min, int $max): float
    {
        if ($wordCount >= $min && $wordCount <= $max) {
            return 1.0;
        }

        $distance = $wordCount < $min ? $min - $wordCount : $wordCount - $max;
        $span = max(20, (int) round(($max - $min) * 0.5));

        return max(0.2, round(1 - ($distance / ($distance + $span)), 2));
    }

    protected function scoreFromBands(float|int $value, array $bands): float
    {
        foreach ($bands as $threshold => $score) {
            if ($value <= $threshold) {
                return $score;
            }
        }

        return (float) end($bands);
    }

    protected function taskAchievementFeedback(array $task, float $keywordCoverage, float $similarity, float $wordRangeScore): string
    {
        if ($task['task_type'] === 'paraphrase') {
            return $similarity <= 0.3
                ? 'The rewrite keeps a reasonable distance from the source wording.'
                : 'The draft still borrows noticeable wording from the source. Push the paraphrase further.';
        }

        if ($keywordCoverage >= 0.5 && $wordRangeScore >= 0.9) {
            return 'The response stays relevant to the source and covers the task in an appropriate length.';
        }

        return 'Develop the article-based support more fully and align the draft more tightly with the task instructions.';
    }

    protected function buildRevisionAdvice(array $task, array $wordLimit, float $similarity): string
    {
        return match ($task['task_type']) {
            'paraphrase' => $similarity > 0.35
                ? 'Revise by changing the sentence order, replacing repeated source vocabulary, and combining ideas in your own structure.'
                : 'Revise by tightening awkward phrases and checking that every sentence still matches the original meaning.',
            'opinion' => 'Revise by sharpening your thesis sentence, then add one more reason or example that clearly supports your position.',
            default => "Revise in two passes: first confirm the summary covers the source accurately, then strengthen the response section while staying within {$wordLimit['min']}-{$wordLimit['max']} words.",
        };
    }

    protected function decodeJsonPayload(?string $text): ?array
    {
        if (! is_string($text) || trim($text) === '') {
            return null;
        }

        $trimmed = trim($text);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```json|^```|```$/m', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        $decoded = json_decode($trimmed, true);

        return is_array($decoded) ? $decoded : null;
    }
}
