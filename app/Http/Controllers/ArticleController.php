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
use App\Services\SpeakingExerciseService;
use App\WritingExerciseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleTextProcessor $processor,
        protected ListeningExerciseService $listeningExerciseService,
        protected ReadingExerciseService $readingExerciseService,
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
        $data['articleSentenceMap'] = $this->buildArticleSentenceMap($data['paragraphs']);

        return view('articles.listening', $data);
    }

    public function speaking(Article $article): View
    {
        $this->recordHistory($article, 'speaking');

        $data = $this->buildPageData($article);
        $exercises = $article->exercises()->where('type', 'speaking')->get();
        $data['speakingExercises'] = $exercises;
        $data['shadowingClips'] = filled($data['audioUrl'])
            ? $this->speakingExerciseService->buildShadowingClips($article)
            : [];

        return view('articles.speaking', $data);
    }

    public function submitSpeaking(Request $request, Article $article)
    {
        $request->validate([
            'exercise_id' => [
                'required',
                Rule::exists('exercises', 'id')
                    ->where('article_id', $article->id)
                    ->where('type', 'speaking'),
            ],
            'audio' => 'required|file|mimes:webm,wav,mp3,m4a,ogg|max:10240',
            'page_opened_at' => 'sometimes|integer|min:1',
        ]);

        $exercise = Exercise::findOrFail($request->exercise_id);

        // Save the uploaded recording for later review.
        $path = $request->file('audio')->store('submissions/speaking', 'public');

        $instructions = $exercise->question_data['instruction']
            ?? ($exercise->question_data['topic'] ?? 'No specific instruction.');
        $practiceMode = (string) $request->input('practice_mode', 'open_response');
        $shadowingClip = null;

        if ($practiceMode === 'shadowing' && $request->filled('shadowing_clip_id')) {
            $shadowingClip = $this->speakingExerciseService->findShadowingClip(
                $article,
                (string) $request->input('shadowing_clip_id')
            );

            if (! $shadowingClip) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected shadowing clip could not be found.',
                ], 422);
            }
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
                'instruction' => $instructions,
                'title' => (string) ($exercise->question_data['title'] ?? 'Speaking Task'),
            ];

        $provider = strtolower((string) config('services.speaking.provider', 'gemini'));

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

        $reward = null;
        if ($request->user()) {
            $reward = $this->companionService->grantLearningReward($request->user(), 'speaking', $article->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your recording has been submitted and evaluated successfully.',
            'submission_id' => $submission->id,
            'evaluation' => $evaluation,
            'companion_reward' => $reward,
        ]);
    }

    public function reading(Article $article): View
    {
        $this->recordHistory($article, 'reading');

        $data = $this->buildPageData($article);
        $data['readingExercise'] = $article->exercises()->where('type', 'reading')->first();
        $data['readingQuestions'] = $this->readingExerciseService->getPublicQuestions($article);
        $data['articleSentenceMap'] = $this->buildArticleSentenceMap($data['paragraphs']);
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

    protected function buildPageData(Article $article): array
    {
        $paragraphs = $this->processor->splitParagraphs($article->content);
        $sentences = collect($paragraphs)
            ->flatMap(fn (string $paragraph) => $this->processor->splitSentences($paragraph))
            ->values();

        return [
            'article' => $article,
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
                'title' => 'Speaking',
                'description' => 'Retell the article, express your opinion, and do shadowing practice with in-browser recording support.',
                'status' => 'Practice prompts ready',
                'route' => route('articles.speaking', $article),
                'cta' => 'Start speaking',
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
            ->map(function (string $paragraph, int $paragraphIndex) {
                return [
                    'paragraph_index' => $paragraphIndex,
                    'sentences' => collect($this->processor->splitSentences($paragraph))
                        ->map(fn (string $sentence, int $sentenceIndex) => [
                            'anchor' => 'p'.$paragraphIndex.'-s'.$sentenceIndex,
                            'label' => 'Paragraph '.($paragraphIndex + 1).', sentence '.($sentenceIndex + 1),
                            'text' => $sentence,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected function resolveAudioUrl(?string $audioUrl): ?string
    {
        if (! filled($audioUrl)) {
            return null;
        }

        if (Str::startsWith($audioUrl, ['http://', 'https://'])) {
            return $audioUrl;
        }

        return asset($audioUrl);
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




