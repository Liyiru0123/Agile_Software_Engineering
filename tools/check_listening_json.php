<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$path = __DIR__.'/../database/sql/version3.21/generated_article_exercise_dataset.sql';
$lines = file($path);

foreach ($lines as $index => $line) {
    if (strpos($line, "'listening'") === false) {
        continue;
    }

    if (!preg_match("/VALUES \\((\\d+), 'listening', '(.*?)', '(.*?)', NULL\\);/", $line, $m)) {
        continue;
    }

    $articleId = (int) $m[1];
    $questionData = $m[2];

    $ok = (int) DB::scalar('SELECT JSON_VALID(?) AS ok', [$questionData]);

    if ($ok !== 1) {
        echo "INVALID at line ".($index + 1)." article_id={$articleId}\n";
        exit(1);
    }
}

echo "All listening question_data are valid by MySQL JSON_VALID().\n";
