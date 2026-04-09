<?php

namespace Database\Seeders;

use App\Models\AiPrompt;
use App\Services\ReadingQuestionMetadataBuilder;
use App\Services\WritingTaskMetadataBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    protected array $listeningStopWords = [
        'the', 'and', 'that', 'with', 'from', 'this', 'have', 'been', 'were', 'they', 'their', 'there',
        'into', 'about', 'would', 'could', 'should', 'which', 'while', 'where', 'when', 'then', 'than',
        'also', 'over', 'under', 'only', 'more', 'most', 'some', 'such', 'very', 'many', 'much', 'your',
        'these', 'those', 'because', 'through', 'between', 'after', 'before', 'during', 'across', 'around',
        'being', 'using', 'used', 'make', 'made', 'does', 'did', 'done', 'just', 'it', 'its', 'are', 'was',
        'had', 'has', 'for', 'you', 'our',
    ];

    public function run(): void
    {
        $datasetPath = database_path('sql/version3.21/generated_article_exercise_dataset.json');

        if (! file_exists($datasetPath)) {
            throw new \RuntimeException("Dataset file not found: {$datasetPath}");
        }

        $dataset = json_decode(file_get_contents($datasetPath), true);

        if (! is_array($dataset)) {
            throw new \RuntimeException('Dataset file is not valid JSON.');
        }

        $speakingPromptId = AiPrompt::query()->where('type', 'speaking')->value('id');
        $writingPromptId = AiPrompt::query()->where('type', 'writing')->value('id');
        $readingMetadataBuilder = app(ReadingQuestionMetadataBuilder::class);
        $writingTaskBuilder = app(WritingTaskMetadataBuilder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('submissions')->truncate();
            DB::table('user_favorites')->truncate();
            DB::table('exercises')->truncate();
            DB::table('articles')->truncate();

            foreach ($dataset as $entry) {
                $article = $entry['article'] ?? null;
                $exercises = $entry['exercises'] ?? [];

                if (! is_array($article) || ! isset($article['article_id'], $article['title'], $article['content'])) {
                    continue;
                }

                DB::table('articles')->insert([
                    'id' => (int) $article['article_id'],
                    'title' => (string) $article['title'],
                    'content' => (string) $article['content'],
                    'audio_url' => $article['audio_url'] ?? null,
                    'cover_image_url' => $article['cover_image_url']
                        ?? $this->defaultCoverImageUrl(
                            (int) $article['article_id'],
                            $article['subject'] ?? null,
                            (string) $article['title'],
                            (string) $article['content']
                        ),
                    'difficulty' => (int) ($article['difficulty'] ?? 1),
                    'word_count' => (int) ($article['word_count'] ?? 0),
                ]);

                $listeningExercise = $this->buildListeningExercise((string) $article['content']);

                DB::table('exercises')->insert([
                    'article_id' => (int) $article['article_id'],
                    'type' => 'listening',
                    'question_data' => json_encode($listeningExercise['question_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'answer' => json_encode($listeningExercise['answer'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'ai_prompt_id' => null,
                ]);

                $existingWritingTypes = [];

                foreach ($exercises as $exercise) {
                    if (! is_array($exercise) || ! isset($exercise['type'], $exercise['question_data'])) {
                        continue;
                    }

                    $questionData = $exercise['question_data'];

                    if (($exercise['type'] ?? null) === 'reading' && is_array($questionData)) {
                        $questionData = $readingMetadataBuilder->enrichQuestionData(
                            (string) $article['content'],
                            $questionData,
                            $exercise['answer'] ?? null,
                        );
                    }

                    if (($exercise['type'] ?? null) === 'writing' && is_array($questionData)) {
                        $questionData = $writingTaskBuilder->enrichTaskData(
                            (string) $article['content'],
                            $questionData,
                            'database',
                        );
                        $existingWritingTypes[] = $questionData['task_type'];
                    }

                    $promptId = $exercise['ai_prompt_id'] ?? null;

                    if ($promptId === null) {
                        $promptId = match ($exercise['type']) {
                            'speaking' => $speakingPromptId,
                            'writing' => $writingPromptId,
                            default => null,
                        };
                    }

                    DB::table('exercises')->insert([
                        'article_id' => (int) $article['article_id'],
                        'type' => (string) $exercise['type'],
                        'question_data' => json_encode($questionData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'answer' => $exercise['answer'] === null
                            ? null
                            : json_encode($exercise['answer'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'ai_prompt_id' => $promptId,
                    ]);
                }

                foreach ($writingTaskBuilder->buildMissingTasks((string) $article['content'], array_values(array_unique($existingWritingTypes))) as $task) {
                    DB::table('exercises')->insert([
                        'article_id' => (int) $article['article_id'],
                        'type' => 'writing',
                        'question_data' => json_encode($task, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        'answer' => null,
                        'ai_prompt_id' => $writingPromptId,
                    ]);
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command?->info('Articles and exercises imported from JSON dataset successfully.');
        $this->command?->info('Articles: '.DB::table('articles')->count());
        $this->command?->info('Exercises: '.DB::table('exercises')->count());
    }

    protected function buildListeningExercise(string $content): array
    {
        $items = [];
        $answerMap = [];
        $usedWords = [];
        $blankId = 1;

        foreach ($this->splitSentences($content) as $sentence) {
            if ($blankId > 10) {
                break;
            }

            $normalizedSentence = $this->normalizeSentence($sentence);

            if (mb_strlen($normalizedSentence) < 35) {
                continue;
            }

            preg_match_all("/\b[A-Za-z][A-Za-z'-]{3,}\b/", $normalizedSentence, $matches);
            $candidate = null;

            foreach ($matches[0] ?? [] as $word) {
                $lowerWord = strtolower($word);

                if (in_array($lowerWord, $this->listeningStopWords, true) || isset($usedWords[$lowerWord])) {
                    continue;
                }

                $candidate = $word;
                break;
            }

            if ($candidate === null) {
                continue;
            }

            $context = preg_replace('/\b'.preg_quote($candidate, '/').'\b/', '_____', $normalizedSentence, 1);

            if (! is_string($context) || substr_count($context, '_____') !== 1) {
                continue;
            }

            $id = (string) $blankId;

            $items[] = [
                'id' => $id,
                'label' => 'Blank '.$blankId,
                'context' => $context,
                'answer' => $candidate,
                'accepted_answers' => [$candidate],
            ];
            $answerMap[$id] = $candidate;
            $usedWords[strtolower($candidate)] = true;
            $blankId++;
        }

        return [
            'question_data' => [
                'instruction' => 'Listen to the source audio and fill in the missing words from the full article passage.',
                'note' => 'Each blank is selected from the full article content. Listen carefully and type the exact missing word.',
                'items' => $items,
            ],
            'answer' => [
                'items' => $answerMap,
            ],
        ];
    }

    protected function splitSentences(string $content): array
    {
        $parts = preg_split('/(?<=[.!?])\s+/', trim($content)) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }

    protected function normalizeSentence(string $sentence): string
    {
        $sentence = str_replace('"', "'", $sentence);
        $sentence = str_replace(["\r", "\n"], ' ', $sentence);

        return trim((string) preg_replace('/\s+/', ' ', $sentence));
    }

    protected function defaultCoverImageUrl(int $articleId, ?string $subject, string $title, string $content): string
    {
        $subjectText = strtolower(trim((string) $subject));
        $text = strtolower($title.' '.$content.' '.$subjectText);

        $theme = 'general';

        if (str_contains($subjectText, 'computer')) {
            $theme = 'computer_science';
        } elseif (str_contains($subjectText, 'mathematics') || str_contains($subjectText, 'math')) {
            $theme = 'mathematics';
        } elseif (str_contains($subjectText, 'civil')) {
            $theme = 'civil_engineering';
        } elseif (str_contains($subjectText, 'mechanical') && str_contains($subjectText, 'transportation')) {
            $theme = 'transportation';
        } elseif (str_contains($subjectText, 'mechanical')) {
            $theme = 'mechanical_engineering';
        }

        if ($theme === 'general') {
            if (preg_match('/\b(algorithm|software|database|network|ai|machine learning|code|programming|cyber)\b/i', $text)) {
                $theme = 'computer_science';
            } elseif (preg_match('/\b(calculus|algebra|geometry|equation|statistics|probability|theorem)\b/i', $text)) {
                $theme = 'mathematics';
            } elseif (preg_match('/\b(bridge|concrete|building|foundation|construction|structural|infrastructure)\b/i', $text)) {
                $theme = 'civil_engineering';
            } elseif (preg_match('/\b(engine|manufacturing|robot|gear|machine|thermodynamics)\b/i', $text)) {
                $theme = 'mechanical_engineering';
            } elseif (preg_match('/\b(transport|rail|vehicle|traffic|aerospace|logistics|ship|aviation)\b/i', $text)) {
                $theme = 'transportation';
            } elseif (preg_match('/\b(energy|solar|wind|carbon|climate|sustainability|environment)\b/i', $text)) {
                $theme = 'energy_environment';
            }
        }

        $seed = rawurlencode('eaplus-'.$theme.'-'.$articleId.'-'.substr(md5($title.'|'.$content), 0, 12));

        return 'https://picsum.photos/seed/'.$seed.'/1200/800';
    }
}
