<?php

namespace Database\Seeders;

use App\Models\Vocabulary;
use Illuminate\Database\Seeder;

class VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            ['word' => 'process', 'phonetic' => '/ˈprɒses/', 'definition' => 'n. process; procedure'],
            ['word' => 'analysis', 'phonetic' => '/əˈnæləsɪs/', 'definition' => 'n. analysis'],
            ['word' => 'methodology', 'phonetic' => '/ˌmeθəˈdɒlədʒi/', 'definition' => 'n. methodology'],
            ['word' => 'hypothesis', 'phonetic' => '/haɪˈpɒθəsɪs/', 'definition' => 'n. hypothesis'],
            ['word' => 'empirical', 'phonetic' => '/ɪmˈpɪrɪkəl/', 'definition' => 'adj. empirical'],
            ['word' => 'academic', 'phonetic' => '/ˌækəˈdemɪk/', 'definition' => 'adj. academic'],
            ['word' => 'research', 'phonetic' => '/rɪˈsɜːtʃ/', 'definition' => 'n. research'],
            ['word' => 'significant', 'phonetic' => '/sɪɡˈnɪfɪkənt/', 'definition' => 'adj. significant'],
            ['word' => 'framework', 'phonetic' => '/ˈfreɪmwɜːk/', 'definition' => 'n. framework'],
            ['word' => 'context', 'phonetic' => '/ˈkɒntekst/', 'definition' => 'n. context'],
            ['word' => 'approach', 'phonetic' => '/əˈprəʊtʃ/', 'definition' => 'n. approach'],
            ['word' => 'concept', 'phonetic' => '/ˈkɒnsept/', 'definition' => 'n. concept'],
            ['word' => 'theory', 'phonetic' => '/ˈθɪəri/', 'definition' => 'n. theory'],
            ['word' => 'evidence', 'phonetic' => '/ˈevɪdəns/', 'definition' => 'n. evidence'],
            ['word' => 'conclusion', 'phonetic' => '/kənˈkluːʒən/', 'definition' => 'n. conclusion'],
            ['word' => 'argument', 'phonetic' => '/ˈɑːɡjumənt/', 'definition' => 'n. argument'],
            ['word' => 'perspective', 'phonetic' => '/pəˈspektɪv/', 'definition' => 'n. perspective'],
            ['word' => 'phenomenon', 'phonetic' => '/fəˈnɒmɪnən/', 'definition' => 'n. phenomenon'],
            ['word' => 'variable', 'phonetic' => '/ˈveəriəbəl/', 'definition' => 'n. variable'],
            ['word' => 'factor', 'phonetic' => '/ˈfæktə/', 'definition' => 'n. factor'],
            ['word' => 'interpretation', 'phonetic' => '/ɪnˌtɜːprɪˈteɪʃən/', 'definition' => 'n. interpretation'],
            ['word' => 'correlation', 'phonetic' => '/ˌkɒrəˈleɪʃən/', 'definition' => 'n. correlation'],
            ['word' => 'validity', 'phonetic' => '/vəˈlɪdəti/', 'definition' => 'n. validity'],
            ['word' => 'reliability', 'phonetic' => '/rɪˌlaɪəˈbɪləti/', 'definition' => 'n. reliability'],
            ['word' => 'criteria', 'phonetic' => '/kraɪˈtɪəriə/', 'definition' => 'n. criteria'],
            ['word' => 'assumption', 'phonetic' => '/əˈsʌmpʃən/', 'definition' => 'n. assumption'],
            ['word' => 'implication', 'phonetic' => '/ˌɪmplɪˈkeɪʃən/', 'definition' => 'n. implication'],
            ['word' => 'synthesize', 'phonetic' => '/ˈsɪnθəsaɪz/', 'definition' => 'v. synthesize'],
            ['word' => 'evaluate', 'phonetic' => '/ɪˈvæljueɪt/', 'definition' => 'v. evaluate'],
            ['word' => 'demonstrate', 'phonetic' => '/ˈdemənstreɪt/', 'definition' => 'v. demonstrate'],
            ['word' => 'illustrate', 'phonetic' => '/ˈɪləstreɪt/', 'definition' => 'v. illustrate'],
            ['word' => 'establish', 'phonetic' => '/ɪˈstæblɪʃ/', 'definition' => 'v. establish'],
            ['word' => 'investigate', 'phonetic' => '/ɪnˈvestɪɡeɪt/', 'definition' => 'v. investigate'],
            ['word' => 'examine', 'phonetic' => '/ɪɡˈzæmɪn/', 'definition' => 'v. examine'],
            ['word' => 'assess', 'phonetic' => '/əˈses/', 'definition' => 'v. assess'],
            ['word' => 'identify', 'phonetic' => '/aɪˈdentɪfaɪ/', 'definition' => 'v. identify'],
            ['word' => 'indicate', 'phonetic' => '/ˈɪndɪkeɪt/', 'definition' => 'v. indicate'],
            ['word' => 'suggest', 'phonetic' => '/səˈdʒest/', 'definition' => 'v. suggest'],
            ['word' => 'propose', 'phonetic' => '/prəˈpəʊz/', 'definition' => 'v. propose'],
            ['word' => 'contribute', 'phonetic' => '/kənˈtrɪbjuːt/', 'definition' => 'v. contribute'],
            ['word' => 'attribute', 'phonetic' => '/əˈtrɪbjuːt/', 'definition' => 'v. attribute'],
            ['word' => 'comprehensive', 'phonetic' => '/ˌkɒmprɪˈhensɪv/', 'definition' => 'adj. comprehensive'],
            ['word' => 'systematic', 'phonetic' => '/ˌsɪstəˈmætɪk/', 'definition' => 'adj. systematic'],
            ['word' => 'theoretical', 'phonetic' => '/ˌθɪəˈretɪkəl/', 'definition' => 'adj. theoretical'],
            ['word' => 'practical', 'phonetic' => '/ˈpræktɪkəl/', 'definition' => 'adj. practical'],
            ['word' => 'relevant', 'phonetic' => '/ˈreləvənt/', 'definition' => 'adj. relevant'],
            ['word' => 'appropriate', 'phonetic' => '/əˈprəʊpriət/', 'definition' => 'adj. appropriate'],
            ['word' => 'consistent', 'phonetic' => '/kənˈsɪstənt/', 'definition' => 'adj. consistent'],
            ['word' => 'subsequent', 'phonetic' => '/ˈsʌbsɪkwənt/', 'definition' => 'adj. subsequent'],
        ];

        foreach ($words as $item) {
            Vocabulary::query()->updateOrCreate(
                ['word' => $item['word']],
                [
                    'phonetic' => $item['phonetic'],
                    'definition' => $item['definition'],
                    'audio_url' => null,
                ]
            );
        }

        $this->command?->info('Vocabulary seeded successfully: '.count($words).' entries processed.');
    }
}
