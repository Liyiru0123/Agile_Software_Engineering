<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Exercise;
use App\Models\AiPrompt;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // 获取 AI 提示
        $speakingPrompt = AiPrompt::where('type', 'speaking')->first();
        $writingPrompt = AiPrompt::where('type', 'writing')->first();
        
        // ===== 文章 1：AI 与学术写作 =====
        $article1 = Article::create([  // ✅ 使用 Article::create()
            'title' => 'How AI Is Changing Academic Writing',
            'content' => 'Artificial intelligence is transforming academic writing. Researchers now use AI tools to check grammar, summarize sources, and even draft initial versions of papers. However, experts warn that students must learn to use these tools responsibly. The process of writing helps develop critical thinking skills. If AI does all the work, students may not gain these essential abilities. Universities are creating new policies to guide AI use in academic settings.',
            'audio_url' => 'https://learningenglish.voanews.com/Content/Video/2024/ai-writing.mp3',
            'difficulty' => 2,
            'word_count' => 85,
        ]);
        
        // 阅读题
        Exercise::create([
            'article_id' => $article1->id,
            'type' => 'reading',
            'question_data' => [
                'question' => 'What is the main concern experts have about AI in academic writing?',
                'options' => [
                    ['key' => 'A', 'text' => 'AI tools are too expensive for students'],
                    ['key' => 'B', 'text' => 'Students may not develop critical thinking skills'],
                    ['key' => 'C', 'text' => 'AI cannot check grammar accurately'],
                    ['key' => 'D', 'text' => 'Universities ban all AI tools'],
                ],
            ],
            'answer' => 'B',
        ]);
        
        // 听力题
        Exercise::create([
            'article_id' => $article1->id,
            'type' => 'listening',
            'question_data' => [
                'instruction' => 'Listen to the audio and fill in the blanks',
                'blanks' => [
                    ['index' => 1, 'position' => 0, 'context' => '_____ intelligence is transforming academic writing', 'hint' => 'adj. 人造的'],
                    ['index' => 2, 'position' => 45, 'context' => 'check grammar, _____ sources, and even draft', 'hint' => 'v. 总结'],
                    ['index' => 3, 'position' => 120, 'context' => 'develop critical _____ skills', 'hint' => 'n. 思考'],
                ],
            ],
            'answer' => [
                '1' => ['Artificial', 'artificial'],
                '2' => ['summarize', 'summarise'],
                '3' => ['thinking', 'Thinking'],
            ],
        ]);
        
        // 写作题
        Exercise::create([
            'article_id' => $article1->id,
            'type' => 'writing',
            'question_data' => [
                'instruction' => 'Rewrite the following paragraph to reduce similarity',
                'source_text' => 'Artificial intelligence is transforming academic writing. Researchers now use AI tools to check grammar, summarize sources, and even draft initial versions of papers.',
                'requirement' => 'Reduce similarity below 30% while keeping the original meaning',
                'word_limit' => ['min' => 40, 'max' => 100],
            ],
            'answer' => null,
            'ai_prompt_id' => $writingPrompt?->id,
        ]);
        
        // 口语题
        Exercise::create([
            'article_id' => $article1->id,
            'type' => 'speaking',
            'question_data' => [
                'instruction' => 'Record your response to the following topic',
                'topic' => 'Do you think AI should be allowed in academic writing? Why or why not?',
                'prep_time' => 60,
                'speak_time' => 120,
                'tips' => ['Consider both benefits and risks', 'Give specific examples', 'State your opinion clearly'],
            ],
            'answer' => null,
            'ai_prompt_id' => $speakingPrompt?->id,
        ]);
        
        // ===== 文章 2：研究方法 =====
        $article2 = Article::create([
            'title' => 'Understanding Research Methodology',
            'content' => 'Research methodology is the backbone of any academic study. It refers to the systematic approach used to conduct research. There are two main types: qualitative and quantitative. Qualitative research focuses on understanding concepts and experiences through interviews and observations. Quantitative research uses numerical data and statistical analysis. Both methods have their strengths and are often used together in mixed-methods research.',
            'audio_url' => null,
            'difficulty' => 3,
            'word_count' => 78,
        ]);
        
        Exercise::create([
            'article_id' => $article2->id,
            'type' => 'reading',
            'question_data' => [
                'question' => 'What are the two main types of research methodology?',
                'options' => [
                    ['key' => 'A', 'text' => 'Primary and secondary'],
                    ['key' => 'B', 'text' => 'Qualitative and quantitative'],
                    ['key' => 'C', 'text' => 'Theoretical and practical'],
                    ['key' => 'D', 'text' => 'Experimental and observational'],
                ],
            ],
            'answer' => 'B',
        ]);
        
        // ===== 文章 3：气候变化 =====
        $article3 = Article::create([
            'title' => 'Climate Change and Its Impact',
            'content' => 'Climate change is one of the most pressing challenges of our time. Rising global temperatures are causing extreme weather events, melting ice caps, and rising sea levels. Scientists agree that human activities, particularly burning fossil fuels, are the main cause. Governments worldwide are working to reduce carbon emissions through renewable energy and policy changes. Individual actions also matter, such as reducing energy consumption and using public transportation.',
            'audio_url' => null,
            'difficulty' => 2,
            'word_count' => 82,
        ]);
        
        Exercise::create([
            'article_id' => $article3->id,
            'type' => 'reading',
            'question_data' => [
                'question' => 'What is identified as the main cause of climate change?',
                'options' => [
                    ['key' => 'A', 'text' => 'Natural climate cycles'],
                    ['key' => 'B', 'text' => 'Solar radiation changes'],
                    ['key' => 'C', 'text' => 'Human activities and fossil fuels'],
                    ['key' => 'D', 'text' => 'Volcanic eruptions'],
                ],
            ],
            'answer' => 'C',
        ]);
        
        // ===== 文章 4：学术英语的重要性（简单）=====
        Article::create([
            'title' => 'The Importance of Academic English',
            'content' => 'Academic English is essential for success in higher education. It differs from everyday English in its formal tone, complex vocabulary, and structured arguments. Students must learn to read academic texts, write research papers, and present their ideas clearly. These skills take time to develop but are crucial for academic and professional success.',
            'audio_url' => null,
            'difficulty' => 1,
            'word_count' => 68,
        ]);
        
        // ===== 文章 5：数字时代的学习（中等）=====
        Article::create([
            'title' => 'Learning in the Digital Age',
            'content' => 'The digital age has revolutionized how we learn. Online platforms, educational apps, and digital resources make learning more accessible than ever. Students can access lectures from top universities, collaborate with peers globally, and learn at their own pace. However, digital learning requires self-discipline and good time management skills.',
            'audio_url' => null,
            'difficulty' => 2,
            'word_count' => 72,
        ]);
        
        $this->command->info('✅ Created 5 articles with exercises');
    }
}