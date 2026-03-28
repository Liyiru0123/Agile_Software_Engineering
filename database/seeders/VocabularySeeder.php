<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vocabulary;

class VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            ['word' => 'process', 'phonetic' => '/ˈprɒses/', 'definition' => 'n. 过程；步骤；程序'],
            ['word' => 'analysis', 'phonetic' => '/əˈnæləsɪs/', 'definition' => 'n. 分析；分解'],
            ['word' => 'methodology', 'phonetic' => '/ˌmeθəˈdɒlədʒi/', 'definition' => 'n. 方法论'],
            ['word' => 'hypothesis', 'phonetic' => '/haɪˈpɒθəsɪs/', 'definition' => 'n. 假设；前提'],
            ['word' => 'empirical', 'phonetic' => '/ɪmˈpɪrɪkəl/', 'definition' => 'adj. 经验主义的；实证的'],
            ['word' => 'academic', 'phonetic' => '/ˌækəˈdemɪk/', 'definition' => 'adj. 学术的'],
            ['word' => 'research', 'phonetic' => '/rɪˈsɜːtʃ/', 'definition' => 'n. 研究；调查'],
            ['word' => 'significant', 'phonetic' => '/sɪɡˈnɪfɪkənt/', 'definition' => 'adj. 重要的；显著的'],
            ['word' => 'framework', 'phonetic' => '/ˈfreɪmwɜːk/', 'definition' => 'n. 框架；体系'],
            ['word' => 'context', 'phonetic' => '/ˈkɒntekst/', 'definition' => 'n. 上下文；背景'],
            ['word' => 'approach', 'phonetic' => '/əˈprəʊtʃ/', 'definition' => 'n. 方法；途径'],
            ['word' => 'concept', 'phonetic' => '/ˈkɒnsept/', 'definition' => 'n. 概念；观念'],
            ['word' => 'theory', 'phonetic' => '/ˈθɪəri/', 'definition' => 'n. 理论；学说'],
            ['word' => 'evidence', 'phonetic' => '/ˈevɪdəns/', 'definition' => 'n. 证据；证明'],
            ['word' => 'conclusion', 'phonetic' => '/kənˈkluːʒən/', 'definition' => 'n. 结论'],
            ['word' => 'argument', 'phonetic' => '/ˈɑːɡjumənt/', 'definition' => 'n. 论点；论证'],
            ['word' => 'perspective', 'phonetic' => '/pəˈspektɪv/', 'definition' => 'n. 观点；视角'],
            ['word' => 'phenomenon', 'phonetic' => '/fəˈnɒmɪnən/', 'definition' => 'n. 现象'],
            ['word' => 'variable', 'phonetic' => '/ˈveəriəbl/', 'definition' => 'n. 变量'],
            ['word' => 'factor', 'phonetic' => '/ˈfæktə/', 'definition' => 'n. 因素'],
            ['word' => 'interpretation', 'phonetic' => '/ɪnˌtɜːprɪˈteɪʃən/', 'definition' => 'n. 解释'],
            ['word' => 'correlation', 'phonetic' => '/ˌkɒrəˈleɪʃən/', 'definition' => 'n. 相关'],
            ['word' => 'validity', 'phonetic' => '/vəˈlɪdəti/', 'definition' => 'n. 有效性'],
            ['word' => 'reliability', 'phonetic' => '/rɪˌlaɪəˈbɪləti/', 'definition' => 'n. 可靠性'],
            ['word' => 'criteria', 'phonetic' => '/kraɪˈtɪəriə/', 'definition' => 'n. 标准（复数）'],
            ['word' => 'assumption', 'phonetic' => '/əˈsʌmpʃən/', 'definition' => 'n. 假设'],
            ['word' => 'implication', 'phonetic' => '/ˌɪmplɪˈkeɪʃən/', 'definition' => 'n. 含义'],
            ['word' => 'synthesize', 'phonetic' => '/ˈsɪnθəsaɪz/', 'definition' => 'v. 综合'],
            ['word' => 'evaluate', 'phonetic' => '/ɪˈvæljueɪt/', 'definition' => 'v. 评估'],
            ['word' => 'demonstrate', 'phonetic' => '/ˈdemənstreɪt/', 'definition' => 'v. 证明'],
            ['word' => 'illustrate', 'phonetic' => '/ˈɪləstreɪt/', 'definition' => 'v. 说明'],
            ['word' => 'establish', 'phonetic' => '/ɪˈstæblɪʃ/', 'definition' => 'v. 建立'],
            ['word' => 'investigate', 'phonetic' => '/ɪnˈvestɪɡeɪt/', 'definition' => 'v. 调查'],
            ['word' => 'examine', 'phonetic' => '/ɪɡˈzæmɪn/', 'definition' => 'v. 检查'],
            ['word' => 'assess', 'phonetic' => '/əˈses/', 'definition' => 'v. 评估'],
            ['word' => 'identify', 'phonetic' => '/aɪˈdentɪfaɪ/', 'definition' => 'v. 识别'],
            ['word' => 'indicate', 'phonetic' => '/ˈɪndɪkeɪt/', 'definition' => 'v. 表明'],
            ['word' => 'suggest', 'phonetic' => '/səˈdʒest/', 'definition' => 'v. 建议'],
            ['word' => 'propose', 'phonetic' => '/prəˈpəʊz/', 'definition' => 'v. 提议'],
            ['word' => 'contribute', 'phonetic' => '/kənˈtrɪbjuːt/', 'definition' => 'v. 贡献'],
            ['word' => 'attribute', 'phonetic' => '/əˈtrɪbjuːt/', 'definition' => 'v. 归因于'],
            ['word' => 'comprehensive', 'phonetic' => '/ˌkɒmprɪˈhensɪv/', 'definition' => 'adj. 全面的'],
            ['word' => 'systematic', 'phonetic' => '/ˌsɪstəˈmætɪk/', 'definition' => 'adj. 系统的'],
            ['word' => 'theoretical', 'phonetic' => '/ˌθɪəˈretɪkəl/', 'definition' => 'adj. 理论的'],
            ['word' => 'practical', 'phonetic' => '/ˈpræktɪkəl/', 'definition' => 'adj. 实际的'],
            ['word' => 'relevant', 'phonetic' => '/ˈreləvənt/', 'definition' => 'adj. 相关的'],
            ['word' => 'appropriate', 'phonetic' => '/əˈprəʊpriət/', 'definition' => 'adj. 适当的'],
            ['word' => 'consistent', 'phonetic' => '/kənˈsɪstənt/', 'definition' => 'adj. 一致的'],
            ['word' => 'subsequent', 'phonetic' => '/ˈsʌbsɪkwənt/', 'definition' => 'adj. 随后的'],
        ];
        
        foreach ($words as $item) {
            // ✅ 使用 Vocabulary::create() + 正确的字段
            Vocabulary::create([
                'word' => $item['word'],
                'phonetic' => $item['phonetic'],
                'definition' => $item['definition'],
                'audio_url' => null,
            ]);
        }
        
        $this->command->info('✅ Created ' . count($words) . ' vocabulary words');
    }
}