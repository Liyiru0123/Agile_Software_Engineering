<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ListeningExerciseDemoSeeder extends Seeder
{
    public function run(): void
    {
        $article = Article::query()->updateOrCreate(
            ['title' => 'Demo Listening Practice: Academic Research'],
            [
                'content' => 'Academic research often begins with a clear question. Researchers then review previous studies, collect evidence, and analyze their findings carefully. However, strong research does not only report data. It also explains why the evidence matters and how the results can be applied in real contexts. Therefore, students must learn to connect details with larger arguments when they read and listen to academic material.',
                'audio_url' => 'https://cdn.jsdelivr.net/gh/jiojioYize/pipeline-audio@master/03_underwater-tunnel.mp3',
                'difficulty' => 2,
                'word_count' => 62,
            ]
        );

        Exercise::query()->updateOrCreate(
            [
                'article_id' => $article->id,
                'type' => 'listening',
            ],
            [
                'question_data' => [
                    'instruction' => 'Listen to the audio and fill in the missing words directly in the passage.',
                    'note' => 'Demo listening exercise loaded from the database for page testing.',
                    'blanks' => [
                        [
                            'index' => 1,
                            'context' => 'Academic research often begins with a clear _____.',
                            'hint' => 'A noun meaning the problem to investigate.',
                        ],
                        [
                            'index' => 2,
                            'context' => 'Researchers then review previous studies, collect evidence, and _____ their findings carefully.',
                            'hint' => 'A verb meaning to examine in detail.',
                        ],
                        [
                            'index' => 3,
                            'context' => '_____, strong research does not only report data.',
                            'hint' => 'A logic connector showing contrast.',
                        ],
                        [
                            'index' => 4,
                            'context' => '_____, students must learn to connect details with larger arguments.',
                            'hint' => 'A logic connector showing conclusion.',
                        ],
                    ],
                ],
                'answer' => [
                    '1' => ['question', 'Question'],
                    '2' => ['analyze', 'analyse'],
                    '3' => ['However', 'however'],
                    '4' => ['Therefore', 'therefore'],
                ],
            ]
        );

        $this->command?->info('Listening demo article ready: ID '.$article->id.' | Title: '.$article->title);
    }
}
