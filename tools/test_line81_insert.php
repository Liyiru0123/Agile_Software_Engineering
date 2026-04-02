<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$path = __DIR__.'/../database/sql/version3.21/generated_article_exercise_dataset.sql';
$lines = file($path);
$line = $lines[80] ?? null; // line 81 (0-based)

if ($line === null) {
    echo "Line 81 not found.\n";
    exit(1);
}

try {
    DB::beginTransaction();
    DB::statement('DELETE FROM exercises WHERE article_id = 5 AND type = "listening";');
    DB::statement(trim($line));
    DB::rollBack();
    echo "Line 81 insert is executable in current DB connection.\n";
} catch (Throwable $e) {
    DB::rollBack();
    echo "Line 81 failed: ".$e->getMessage()."\n";
    exit(1);
}
