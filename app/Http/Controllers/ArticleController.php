<?php

namespace App\Http\Controllers;

use App\ListeningExerciseService;
use App\Models\Article;
use App\Models\Exercise;
use App\Models\ReadingHistory;
use App\Models\Submission;
use App\Services\ArticleTextProcessor;
use App\Services\CompanionService;
use App\Services\GeminiAudioService;
use App\Services\OllamaSpeakingService;
use App\Services\QwenOmniAudioService;
use App\Services\ReadingExerciseService;
use App\Services\SkillPlanCompletionService;
use App\Services\SpeakingExerciseService;
use App\WritingExerciseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleTextProcessor $processor,
        protected ListeningExerciseService $listeningExerciseService,
        protected ReadingExerciseService $readingExerciseService,
        protected SkillPlanCompletionService $skillPlanCompletionService,
        protected SpeakingExerciseService $speakingExerciseService,
        protected WritingExerciseService $writingExerciseService,
        protected CompanionService $companionService,
        protected GeminiAudioService $geminiAudioService,
        protected OllamaSpeakingService $ollamaSpeakingService,
        protected QwenOmniAudioService $qwenOmniAudioService
    ) {
    }

    public function index(): View
    {
        $articles = Article::all();

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        $this->recordHistory($article, 'article');

        return view('articles.show', $this->buildPageData($article));
    }

    public function listening(Article $article): View
    {
        $this->recordHistory($article, 'listening');

        $data = $this->buildPageData($article);
        $data['listeningExercise'] = $this->listeningExerciseService->getForArticle(
            $article,
            request()->user()?->id
        );

        $writingPayload = $this->writingExerciseService->buildPagePayload(
            article: $article,
            userId: auth()->id(),
        );
        $writingTasks = $writingPayload['writingTasks'] ?? [];

        $data['listeningSummaryTask'] = collect($writingTasks)->firstWhere('task_type', 'summary_response')
            ?? ($writingTasks[0] ?? null);
        $data['listeningReadingQuestions'] = $this->readingExerciseService->getPublicQuestions($article);
        $data['articleSentenceMap'] = $this->buildArticleSentenceMap($data['articleParagraphs']);

        $latestReadingResult = null;
        if (auth()->id()) {
            $readingExerciseIds = $article->exercises()
                ->where('type', 'reading')
                ->pluck('id');

            if ($readingExerciseIds->isNotEmpty()) {
                $latestReadingSubmission = Submission::query()
                    ->where('user_id', auth()->id())
                    ->where('article_id', $article->id)
                    ->whereIn('exercise_id', $readingExerciseIds)
                    ->orderByDesc('id')
                    ->first();

                $latestReadingResult = $latestReadingSubmission?->ai_advice;
            }
        }

        $data['latestReadingResult'] = $latestReadingResult;

        return view('articles.listening', $data);
    }

    public function speaking(Article $article): View
    {
        $this->recordHistory($article, 'speaking');

        $data = $this->buildPageData($article);
        $exercises = $article->exercises()
            ->where('type', 'speaking')
            ->get()
            ->reject(fn (Exercise $exercise) => data_get($exercise->question_data, 'mode') === 'shadowing')
            ->values();
        $data['speakingExercises'] = $exercises;
        $data['shadowingClips'] = filled($data['audioUrl'])
            ? $this->speakingExerciseService->buildShadowingClips($article)
            : [];

        return view('articles.speaking', $data);
    }

    public function speakingHub(): View
    {
        return view('speaking.hub', [
            'entryCards' => [
                [
                    'title' => 'Article Speaking Learning',
                    'description' => 'Choose an article first, then enter the existing speaking training flow with shadowing clips and open-response practice.',
                    'status' => 'Available now',
                    'route' => route('articles.index', ['skill' => 'speaking']),
                    'cta' => 'Open Article Speaking',
                ],
                [
                    'title' => 'AI Conversation',
                    'description' => 'Talk to the centered Live2D companion with text or browser voice input. Replies appear on-screen as subtitles and can be read aloud.',
                    'status' => 'Available now',
                    'route' => route('speaking.live2d'),
                    'cta' => 'Open AI Conversation',
                ],
                [
                    'title' => 'Video Call',
                    'description' => 'Enter the LiveKit-powered room for random matching and online-friend video speaking practice.',
                    'status' => 'Available now',
                    'route' => route('speaking.video-call'),
                    'cta' => 'Open Video Call',
                ],
            ],
        ]);
    }

    public function speakingLive2d(): View
    {
        $live2dInterface = [
            'status' => 'available',
            'conversation_endpoint' => route('speaking.live2d.interface'),
            'audio_input_supported' => true,
            'message_input_supported' => true,
            'notes' => [
                'Voice capture uses the browser SpeechRecognition API first to keep running costs low.',
                'Replies are generated from text input and shown as subtitles under the centered Live2D character.',
            ],
        ];

        return view('speaking.live2d', compact('live2dInterface'));
    }

    public function speakingLive2dInterface(Request $request): JsonResponse
    {
        $sessionKey = 'live2d_ai_dialogue.history';

        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'available',
                'conversation_mode' => 'live2d_ai_dialogue',
                'message' => 'Live2D AI conversation is ready.',
                'history_count' => count($request->session()->get($sessionKey, [])),
            ]);
        }

        $payload = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
            'reset' => ['sometimes', 'boolean'],
        ]);

        if (($payload['reset'] ?? false) === true) {
            $request->session()->forget($sessionKey);

            return response()->json([
                'success' => true,
                'conversation_mode' => 'live2d_ai_dialogue',
                'assistant_text' => 'Conversation cleared. I am ready for a new speaking round.',
                'provider' => 'system',
                'history_count' => 0,
            ]);
        }

        $message = trim((string) ($payload['message'] ?? ''));

        if ($message === '') {
            return response()->json([
                'message' => 'Please type something or use the microphone before sending.',
            ], 422);
        }

        $history = $request->session()->get($sessionKey, []);
        $history = is_array($history) ? $history : [];
        $result = $this->generateLive2dReply(
            $message,
            $history,
            $request->user()?->name
        );

        $updatedHistory = collect([
            ...$history,
            ['role' => 'user', 'content' => $message],
            ['role' => 'assistant', 'content' => $result['assistant_text']],
        ])
            ->filter(fn ($item) => is_array($item) && filled($item['role'] ?? null) && filled($item['content'] ?? null))
            ->take(-12)
            ->values()
            ->all();

        $request->session()->put($sessionKey, $updatedHistory);

        return response()->json([
            'success' => true,
            'status' => $result['status'] ?? 'available',
            'conversation_mode' => 'live2d_ai_dialogue',
            'user_text' => $message,
            'assistant_text' => (string) $result['assistant_text'],
            'provider' => $result['provider'] ?? 'unknown',
            'history_count' => count($updatedHistory),
            'notice' => $result['notice'] ?? null,
        ]);
    }

    public function speakingVideoCall(): View
    {
        $videoCallInterface = [
            'status' => 'available',
            'video_call_endpoint' => route('speaking.video-call.status'),
            'camera_input_supported' => true,
            'microphone_input_supported' => true,
            'notes' => [
                'This page now uses LiveKit rooms for random speaking matches and friend video calls.',
                'The Laravel side manages queueing, friend availability, and call-session state.',
            ],
        ];

        return view('speaking.video-call', compact('videoCallInterface'));
    }

    public function speakingVideoCallInterface(): JsonResponse
    {
        return response()->json([
            'status' => 'pending_integration',
            'conversation_mode' => 'video_call_ai_dialogue',
            'message' => 'Video call speaking is reserved for future integration.',
            'expected_payload' => [
                'message' => 'string|null',
                'audio' => 'uploaded audio blob|null',
                'video' => 'uploaded video blob|null',
                'conversation_id' => 'string|null',
            ],
        ], 501);
    }

    public function submitSpeaking(Request $request, Article $article)
    {
        $payload = $request->validate([
            'practice_mode' => ['sometimes', 'string', Rule::in(['open_response', 'shadowing'])],
            'exercise_id' => [
                Rule::requiredIf(fn () => $request->input('practice_mode', 'open_response') !== 'shadowing'),
                'nullable',
                Rule::exists('exercises', 'id')
                    ->where('article_id', $article->id)
                    ->where('type', 'speaking'),
            ],
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a,ogg|max:10240',
            'page_opened_at' => 'sometimes|integer|min:1',
            'shadowing_clip_id' => [
                Rule::requiredIf(fn () => $request->input('practice_mode', 'open_response') === 'shadowing'),
                'nullable',
                'string',
            ],
        ]);

        $practiceMode = (string) ($payload['practice_mode'] ?? 'open_response');
        $exercise = null;
        $shadowingClip = null;

        if ($practiceMode === 'shadowing') {
            $shadowingClip = $this->speakingExerciseService->findShadowingClip(
                $article,
                (string) ($payload['shadowing_clip_id'] ?? '')
            );

            if (! $shadowingClip) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected shadowing clip could not be found.',
                ], 422);
            }
        } else {
            $exercise = Exercise::findOrFail($payload['exercise_id']);
        }

        $taskContext = $shadowingClip
            ? [
                'mode' => 'shadowing',
                'instruction' => 'Repeat the target excerpt as accurately and naturally as possible.',
                'target_text' => $shadowingClip['transcript'],
                'title' => $shadowingClip['title'],
            ]
            : [
                'mode' => 'open_response',
                'instruction' => $exercise->question_data['instruction']
                    ?? ($exercise->question_data['topic'] ?? 'No specific instruction.'),
                'title' => (string) ($exercise->question_data['title'] ?? 'Speaking Task'),
            ];
        $exercise ??= $this->resolveShadowingExercise($article);

        $provider = strtolower((string) config('services.speaking.provider', 'gemini'));
        $path = null;

        try {
            // Save the uploaded recording for later review.
            $path = $request->file('audio')->store('submissions/speaking', 'public');

            $evaluation = match ($provider) {
                'qwen_omni' => $this->qwenOmniAudioService->evaluateAudio(
                    $request->file('audio'),
                    $taskContext['instruction'],
                    Str::limit($article->content, 5000)
                ),
                'ollama' => $this->ollamaSpeakingService->evaluateSpeaking(
                    $request->file('audio'),
                    $taskContext['instruction'],
                    Str::limit($article->content, 3000)
                ),
                default => $this->geminiAudioService->evaluateAudio(
                    $request->file('audio'),
                    $taskContext,
                    Str::limit($article->content, 5000)
                ),
            };

            $evaluation['provider'] = $provider;
            $evaluation['practice_mode'] = $taskContext['mode'];
            $evaluation['task_title'] = $taskContext['title'];

            if ($shadowingClip) {
                $evaluation['target_text'] = $shadowingClip['transcript'];
                $evaluation['clip_id'] = $shadowingClip['id'];
            }

            if (isset($evaluation['error'])) {
                Storage::disk('public')->delete($path);

                return response()->json([
                    'success' => false,
                    'message' => $evaluation['error'],
                    'evaluation' => $evaluation,
                ], 422);
            }

            $timeSpent = 0;
            if ($request->filled('page_opened_at')) {
                $openedAtMs = (int) $request->input('page_opened_at');
                $nowMs = now()->valueOf();
                $timeSpent = max(0, (int) floor(($nowMs - $openedAtMs) / 1000));
            }

            $submission = Submission::create([
                'user_id' => auth()->id(),
                'exercise_id' => $exercise->id,
                'article_id' => $article->id,
                'user_answer' => [
                    'audio_path' => $path,
                    'transcript' => $evaluation['transcript'] ?? null,
                    'practice_mode' => $taskContext['mode'],
                    'shadowing_clip_id' => $shadowingClip['id'] ?? null,
                    'target_text' => $shadowingClip['transcript'] ?? null,
                ],
                'score' => $evaluation['score'] ?? 0,
                'time_spent' => $timeSpent,
                'ai_advice' => $evaluation,
                'created_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        $reward = null;
        $completedSkillPlans = 0;
        if ($request->user()) {
            $reward = $this->companionService->grantLearningReward($request->user(), 'speaking', $article->id);
            $completedSkillPlans = $this->skillPlanCompletionService->syncForSubmission($request->user()->id, 'speaking', $submission->created_at);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your recording has been submitted and evaluated successfully.',
            'submission_id' => $submission->id,
            'evaluation' => $evaluation,
            'companion_reward' => $reward,
            'skill_plan_auto_completed' => $completedSkillPlans > 0,
        ]);
    }

    public function reading(Article $article): View
    {
        $this->recordHistory($article, 'reading');

        $data = $this->buildPageData($article);
        $data['readingExercise'] = $article->exercises()->where('type', 'reading')->first();
        $data['readingQuestions'] = $this->readingExerciseService->getPublicQuestions($article);
        $data['articleSentenceMap'] = $this->buildArticleSentenceMap($data['articleParagraphs']);
        $data['keywords'] = $this->extractKeywords($article->content);

        return view('articles.reading', $data);
    }

    public function writing(Article $article): View
    {
        $this->recordHistory($article, 'writing');

        $data = $this->buildPageData($article);
        $data = array_merge($data, $this->writingExerciseService->buildPagePayload(
            article: $article,
            userId: auth()->id(),
        ));

        return view('articles.writing', $data);
    }

    protected function generateLive2dReply(string $message, array $history = [], ?string $userName = null): array
    {
        $history = $this->normalizeLive2dHistory($history);
        $apiKey = (string) config('services.gemini.api_key', '');
        $model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $baseUrl = rtrim((string) config('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta/models'), '/');

        if ($apiKey === '') {
            return $this->buildLive2dFallbackReply($message, true);
        }

        try {
            if (! str_contains($baseUrl, 'generativelanguage.googleapis.com')) {
                $messages = [
                    [
                        'role' => 'system',
                        'content' => $this->live2dSystemPrompt($userName),
                    ],
                ];

                foreach ($history as $item) {
                    $messages[] = [
                        'role' => $item['role'],
                        'content' => $item['content'],
                    ];
                }

                $messages[] = [
                    'role' => 'user',
                    'content' => $message,
                ];

                $response = Http::timeout(60)
                    ->withToken($apiKey)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($baseUrl.'/chat/completions', [
                        'model' => $model,
                        'messages' => $messages,
                        'temperature' => 0.7,
                    ]);

                if ($response->failed()) {
                    throw new \RuntimeException((string) ($response->json('error.message') ?? 'Failed to reach the conversation model.'));
                }

                $assistantText = trim((string) ($response->json('choices.0.message.content') ?? ''));
                if ($assistantText === '') {
                    throw new \RuntimeException('The conversation model returned an empty reply.');
                }

                return [
                    'status' => 'available',
                    'provider' => 'gemini-compatible',
                    'assistant_text' => $assistantText,
                ];
            }

            $parts = [
                ['text' => $this->live2dSystemPrompt($userName)],
            ];

            foreach ($history as $item) {
                $speaker = $item['role'] === 'assistant' ? 'Hiyori' : 'Learner';
                $parts[] = ['text' => $speaker.': '.$item['content']];
            }

            $parts[] = ['text' => 'Learner: '.$message];

            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$baseUrl}/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => $parts,
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                    ],
                ]);

            if ($response->failed()) {
                throw new \RuntimeException((string) ($response->json('error.message') ?? 'Failed to reach the conversation model.'));
            }

            $assistantText = trim((string) ($response->json('candidates.0.content.parts.0.text') ?? ''));
            if ($assistantText === '') {
                throw new \RuntimeException('The conversation model returned an empty reply.');
            }

            return [
                'status' => 'available',
                'provider' => 'gemini-native',
                'assistant_text' => $assistantText,
            ];
        } catch (\Throwable $exception) {
            report($exception);

            return $this->buildLive2dFallbackReply($message, false);
        }
    }

    protected function normalizeLive2dHistory(array $history): array
    {
        return collect($history)
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) {
                $role = (string) ($item['role'] ?? 'user');

                return [
                    'role' => in_array($role, ['user', 'assistant'], true) ? $role : 'user',
                    'content' => trim((string) ($item['content'] ?? '')),
                ];
            })
            ->filter(fn (array $item) => $item['content'] !== '')
            ->take(-10)
            ->values()
            ->all();
    }

    protected function live2dSystemPrompt(?string $userName): string
    {
        $learnerName = trim((string) $userName) !== '' ? trim((string) $userName) : 'the learner';

        return <<<PROMPT
You are Hiyori, a warm English-speaking practice companion helping {$learnerName}.
Reply in natural English.
Keep most replies within 2 to 4 short sentences.
When the learner makes a grammar mistake, gently model a cleaner version without sounding like a harsh examiner.
Prioritize practical speaking, listening, reading, and writing advice.
Do not use markdown, bullet lists, or emojis.
PROMPT;
    }

    protected function buildLive2dFallbackReply(string $message, bool $unconfigured): array
    {
        $normalized = Str::lower($message);

        $assistantText = match (true) {
            Str::contains($normalized, ['hello', 'hi']) => 'Hello. I am ready. Tell me which skill you want to practice, and I will keep the next step simple.',
            Str::contains($normalized, ['listening', 'listen']) => 'For listening, replay one short part and catch the keywords before trying the whole sentence again.',
            Str::contains($normalized, ['reading', 'read']) => 'For reading, paraphrase each paragraph in one short sentence before you move on.',
            Str::contains($normalized, ['writing', 'write']) => 'For writing, get the main idea down first, then revise grammar only after the draft is complete.',
            Str::contains($normalized, ['speaking', 'speak', 'shadowing']) => 'For speaking, keep the clip short. Match rhythm first, then fix individual words on the next try.',
            Str::contains($normalized, ['tired', 'busy']) => 'Then choose the smallest useful task. One finished round is better than waiting for perfect energy.',
            default => 'I heard you. Ask me about listening, reading, writing, or speaking practice, and I will answer in short coaching steps.',
        };

        return [
            'status' => $unconfigured ? 'degraded' : 'fallback',
            'provider' => 'local-fallback',
            'assistant_text' => $assistantText,
            'notice' => $unconfigured
                ? 'Gemini API key is not configured, so the page is using the low-cost local fallback reply mode.'
                : 'The live model request failed, so the page fell back to the local reply mode.',
        ];
    }

    protected function buildPageData(Article $article): array
    {
        $article->loadMissing(['segments' => fn ($query) => $query
            ->orderBy('paragraph_index')
            ->orderBy('sentence_index')
            ->orderBy('id')]);

        $articleParagraphs = $this->resolveArticleParagraphs($article);
        $paragraphs = collect($articleParagraphs)
            ->pluck('text')
            ->values()
            ->all();
        $sentences = collect($paragraphs)
            ->flatMap(fn (string $paragraph) => $this->processor->splitSentences($paragraph))
            ->values();

        return [
            'article' => $article,
            'articleParagraphs' => $articleParagraphs,
            'paragraphs' => $paragraphs,
            'sentences' => $sentences,
            'audioUrl' => $this->resolveAudioUrl($article->audio_url),
            'difficultyLabel' => $this->difficultyLabel((int) $article->difficulty),
            'estimatedMinutes' => max(1, (int) ceil(($article->word_count ?: $this->processor->countWords($article->content)) / 180)),
            'articleSummary' => Str::limit($sentences->take(2)->implode(' '), 220),
            'trainingCards' => $this->buildTrainingCards($article),
        ];
    }

    protected function buildTrainingCards(Article $article): array
    {
        return [
            [
                'title' => 'Listening Hub',
                'description' => 'Complete one integrated flow: fill-in-the-blank dictation, listening multiple-choice, summary writing, and transcript review.',
                'status' => $article->audio_url ? 'Audio ready' : 'Text preview only',
                'route' => route('articles.listening', $article),
                'cta' => 'Start listening',
            ],
            [
                'title' => 'Reading',
                'description' => 'Answer comprehension questions with inline explanations, source locating, and article-side navigation.',
                'status' => 'Questions ready',
                'route' => route('articles.reading', $article),
                'cta' => 'Start reading',
            ],
            [
                'title' => 'Speaking',
                'description' => 'Retell the article, express your opinion, and do shadowing practice with in-browser recording support.',
                'status' => 'Practice prompts ready',
                'route' => route('articles.speaking', $article),
                'cta' => 'Start speaking',
            ],
            [
                'title' => 'Writing',
                'description' => 'Practice summary, paraphrase, and opinion writing with saved drafts and AI-based feedback.',
                'status' => 'Tasks ready',
                'route' => route('articles.writing', $article),
                'cta' => 'Start writing',
            ],
        ];
    }

    protected function buildSpeakingPrompts(?Exercise $exercise): array
    {
        $questionData = $exercise?->question_data ?? [];
        $prompts = [];

        if (filled($questionData['topic'] ?? null)) {
            $prompts[] = [
                'title' => 'Opinion Prompt',
                'instruction' => $questionData['topic'],
            ];
        }

        $prompts[] = [
            'title' => '45-Second Summary',
            'instruction' => 'Summarize the article\'s main claim, supporting details, and conclusion in 45 seconds.',
        ];

        $prompts[] = [
            'title' => 'Critical Response',
            'instruction' => 'Choose one idea you agree with most or question most, explain why, and give one real-world example.',
        ];

        return collect($prompts)->unique('title')->values()->all();
    }

    protected function buildWritingTask(Article $article, ?Exercise $exercise): array
    {
        $questionData = $exercise?->question_data ?? [];

        return [
            'instruction' => $questionData['instruction'] ?? 'Write a short academic response based on the article.',
            'requirement' => $questionData['requirement'] ?? 'First write an 80-120 word summary of the article, then add a 60-100 word evaluation or application.',
            'source_text' => $questionData['source_text'] ?? Str::limit($article->content, 220),
            'word_limit' => $questionData['word_limit'] ?? ['min' => 140, 'max' => 220],
        ];
    }

    protected function extractKeywords(string $content): array
    {
        $stopwords = [
            'about', 'after', 'also', 'been', 'being', 'between', 'could', 'does',
            'from', 'have', 'however', 'into', 'more', 'must', 'only', 'other',
            'should', 'that', 'their', 'there', 'these', 'they', 'this', 'through',
            'using', 'with', 'would', 'which', 'while', 'where',
        ];

        preg_match_all("/[A-Za-z][A-Za-z'-]{4,}/", strtolower($content), $matches);

        return collect($matches[0] ?? [])
            ->reject(fn (string $word) => in_array($word, $stopwords, true))
            ->countBy()
            ->sortDesc()
            ->keys()
            ->take(8)
            ->values()
            ->all();
    }

    protected function buildArticleSentenceMap(array $paragraphs): array
    {
        return collect($paragraphs)
            ->map(function (array|string $paragraph, int $paragraphIndex) {
                $paragraphText = is_array($paragraph)
                    ? (string) ($paragraph['text'] ?? '')
                    : (string) $paragraph;
                $displayIndex = is_array($paragraph) && isset($paragraph['display_index'])
                    ? (int) $paragraph['display_index']
                    : ($paragraphIndex + 1);

                return [
                    'paragraph_index' => $paragraphIndex,
                    'display_index' => $displayIndex,
                    'label' => 'Paragraph '.$displayIndex,
                    'time_range_label' => is_array($paragraph) ? ($paragraph['time_range_label'] ?? null) : null,
                    'sentences' => collect($this->processor->splitSentences($paragraphText))
                        ->map(fn (string $sentence, int $sentenceIndex) => [
                            'anchor' => 'p'.$paragraphIndex.'-s'.$sentenceIndex,
                            'label' => 'Paragraph '.$displayIndex.', sentence '.($sentenceIndex + 1),
                            'text' => $sentence,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected function resolveArticleParagraphs(Article $article): array
    {
        $segments = $article->segments ?? collect();

        if ($segments->isNotEmpty()) {
            return $segments
                ->groupBy('paragraph_index')
                ->map(function ($group, $paragraphIndex) {
                    $ordered = $group
                        ->sortBy([
                            ['sentence_index', 'asc'],
                            ['id', 'asc'],
                        ])
                        ->values();

                    $text = $ordered
                        ->pluck('content_en')
                        ->filter(fn ($value) => filled($value))
                        ->map(fn ($value) => trim((string) $value))
                        ->implode(' ');

                    $startTime = $ordered
                        ->pluck('start_time')
                        ->filter(fn ($value) => $value !== null)
                        ->map(fn ($value) => (float) $value)
                        ->min();
                    $endTime = $ordered
                        ->pluck('end_time')
                        ->filter(fn ($value) => $value !== null)
                        ->map(fn ($value) => (float) $value)
                        ->max();

                    return [
                        'display_index' => (int) $paragraphIndex,
                        'text' => trim($text),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'time_range_label' => $startTime !== null && $endTime !== null && $endTime > $startTime
                            ? $this->formatTimeRange($startTime, $endTime)
                            : null,
                    ];
                })
                ->filter(fn (array $paragraph) => $paragraph['text'] !== '')
                ->values()
                ->all();
        }

        return collect($this->processor->splitParagraphs($article->content))
            ->values()
            ->map(fn (string $paragraph, int $index) => [
                'display_index' => $index + 1,
                'text' => $paragraph,
                'start_time' => null,
                'end_time' => null,
                'time_range_label' => null,
            ])
            ->all();
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

    protected function resolveShadowingExercise(Article $article): Exercise
    {
        $existing = $article->exercises()
            ->where('type', 'speaking')
            ->get()
            ->first(fn (Exercise $exercise) => data_get($exercise->question_data, 'mode') === 'shadowing');

        if ($existing) {
            return $existing;
        }

        return Exercise::query()->create([
            'article_id' => $article->id,
            'type' => 'speaking',
            'question_data' => [
                'mode' => 'shadowing',
                'title' => 'Shadowing Practice',
                'instruction' => 'Repeat the selected target excerpt as accurately and naturally as possible.',
            ],
            'answer' => [],
        ]);
    }

    protected function resolveAudioUrl(?string $audioUrl): ?string
    {
        if (! filled($audioUrl)) {
            return null;
        }

        if (Str::startsWith($audioUrl, ['http://', 'https://'])) {
            return $audioUrl;
        }

        return Storage::url(ltrim($audioUrl, '/'));
    }

    protected function difficultyLabel(int $difficulty): string
    {
        return match ($difficulty) {
            1 => 'Foundation',
            2 => 'Intermediate',
            3 => 'Advanced',
            default => 'General',
        };
    }

    protected function recordHistory(Article $article, string $page): void
    {
        $userId = auth()->id();

        if (! $userId) {
            return;
        }

        $history = ReadingHistory::query()->firstOrNew([
            'user_id' => $userId,
            'article_id' => $article->id,
        ]);

        $history->last_page = $page;
        $history->last_viewed_at = now();
        $history->visit_count = $history->exists ? ((int) $history->visit_count + 1) : 1;
        $history->first_viewed_at ??= now();
        $history->save();
    }
}
