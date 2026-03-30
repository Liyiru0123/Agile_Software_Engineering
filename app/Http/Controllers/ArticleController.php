<?php

namespace App\Http\Controllers;

use App\ListeningExerciseService;
use App\Models\Article;
use App\Models\Exercise;
use App\Services\ArticleTextProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleTextProcessor $processor,
        protected ListeningExerciseService $listeningExerciseService
    ) {
    }

    public function index(): View
    {
        $articles = Article::all();

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        return view('articles.show', $this->buildPageData($article));
    }

    public function listening(Article $article): View
    {
        $data = $this->buildPageData($article);
        $data['listeningExercise'] = $this->listeningExerciseService->generateForArticle($article);
        $data['geminiReady'] = filled(config('services.gemini.api_key'));

        return view('articles.listening', $data);
    }

    public function speaking(Article $article): View
    {
        $data = $this->buildPageData($article);
        $exercise = $article->exercises()->where('type', 'speaking')->first();
        $data['speakingPrompts'] = $this->buildSpeakingPrompts($exercise);

        return view('articles.speaking', $data);
    }

    public function reading(Article $article): View
    {
        $data = $this->buildPageData($article);
        $data['readingExercise'] = $article->exercises()->where('type', 'reading')->first();
        $data['readingQuestions'] = $this->buildReadingQuestions();
        $data['keywords'] = $this->extractKeywords($article->content);

        return view('articles.reading', $data);
    }

    public function writing(Article $article): View
    {
        $data = $this->buildPageData($article);
        $exercise = $article->exercises()->where('type', 'writing')->first();
        $data['writingTask'] = $this->buildWritingTask($article, $exercise);

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
                'title' => 'Listening',
                'description' => 'AI 生成语义重点填空、逻辑词训练和主旨题，并给出错因反馈。',
                'status' => $article->audio_url ? 'Audio ready' : 'Text preview only',
                'route' => route('articles.listening', $article),
                'cta' => 'Start listening',
            ],
            [
                'title' => 'Speaking',
                'description' => '围绕文章做复述、观点表达和 shadowing 练习，支持浏览器录音。',
                'status' => 'Practice prompts ready',
                'route' => route('articles.speaking', $article),
                'cta' => 'Start speaking',
            ],
            [
                'title' => 'Reading',
                'description' => '阅读原文、抓关键词并完成理解问题，适合精读和学术词汇积累。',
                'status' => 'Reading tasks ready',
                'route' => route('articles.reading', $article),
                'cta' => 'Start reading',
            ],
            [
                'title' => 'Writing',
                'description' => '根据文章完成 summary 和 response 写作，控制字数并检查结构。',
                'status' => 'Writing prompt ready',
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
            'instruction' => '用 45 秒概括文章的核心论点、支持细节和结论。',
        ];

        $prompts[] = [
            'title' => 'Critical Response',
            'instruction' => '挑一个你最认同或最质疑的观点，说明原因并给出一个现实例子。',
        ];

        return collect($prompts)->unique('title')->values()->all();
    }

    protected function buildReadingQuestions(): array
    {
        return [
            'What is the central claim of the article? Summarize it in one sentence.',
            'Which supporting detail is the most important, and why?',
            'Which connector or academic term best signals the author’s logic?',
        ];
    }

    protected function buildWritingTask(Article $article, ?Exercise $exercise): array
    {
        $questionData = $exercise?->question_data ?? [];

        return [
            'instruction' => $questionData['instruction'] ?? 'Write a short academic response based on the article.',
            'requirement' => $questionData['requirement'] ?? '先用 80-120 词概括文章，再用 60-100 词表达你的评价或应用场景。',
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
}
