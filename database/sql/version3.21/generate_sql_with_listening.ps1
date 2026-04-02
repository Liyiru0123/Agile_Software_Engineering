$jsonPath = Join-Path (Get-Location) "database/sql/version3.21/generated_article_exercise_dataset.json"
$outPath = Join-Path (Get-Location) "database/sql/version3.21/generated_article_exercise_dataset.sql"

$raw = Get-Content -Raw $jsonPath
$data = $raw | ConvertFrom-Json

function SqlString([object]$v) {
    if ($null -eq $v) { return 'NULL' }
    $s = [string]$v
    $s = $s.Replace('\\', '\\\\')
    $s = $s -replace "'", "''"
    return "'" + $s + "'"
}

function SqlJson([object]$v) {
    if ($null -eq $v) { return 'NULL' }
    $j = $v | ConvertTo-Json -Depth 100 -Compress
    $j = $j.Replace('\\', '\\\\')
    $j = $j -replace "'", "''"
    return "'" + $j + "'"
}

function SplitSentences([string]$text) {
    return ($text -split '(?<=[.!?])\s+') | Where-Object { $_.Trim().Length -gt 0 }
}

$stop = @(
    'the','and','that','with','from','this','have','been','were','they','their','there',
    'into','about','would','could','should','which','while','where','when','then','than',
    'also','over','under','only','more','most','some','such','very','many','much','your',
    'these','those','because','through','between','after','before','during','across','around',
    'being','using','used','make','made','does','did','done','just','it','its','are','was',
    'had','has','for','you','our'
)

function NewListeningExercise([string]$content) {
    $sentences = SplitSentences $content
    $items = @()
    $answerMap = @{}
    $used = New-Object 'System.Collections.Generic.HashSet[string]'
    $blankId = 1

    foreach ($s in $sentences) {
        if ($blankId -gt 10) { break }

        $sentence = $s.Trim()
        $sentence = $sentence.Replace('"', "'")
        $sentence = $sentence.Replace("`r", ' ').Replace("`n", ' ')
        $sentence = [regex]::Replace($sentence, '\s+', ' ').Trim()
        if ($sentence.Length -lt 35) { continue }

        $matches = [regex]::Matches($sentence, "\b[A-Za-z][A-Za-z'-]{3,}\b")
        if ($matches.Count -eq 0) { continue }

        $picked = $null
        foreach ($m in $matches) {
            $w = $m.Value
            $wl = $w.ToLower()
            if ($stop -contains $wl) { continue }
            if ($used.Contains($wl)) { continue }
            $picked = $w
            break
        }

        if ($null -eq $picked) { continue }

        $pattern = "\b" + [regex]::Escape($picked) + "\b"
        $replaced = [regex]::Replace($sentence, $pattern, '_____', 1)
        if (([regex]::Matches($replaced, '_____')).Count -ne 1) { continue }

        $id = [string]$blankId
        $items += @{
            id = $id
            label = "Blank $blankId"
            context = $replaced
            answer = $picked
            accepted_answers = @($picked)
        }
        $answerMap[$id] = $picked
        [void]$used.Add($picked.ToLower())
        $blankId++
    }

    return @{
        question_data = @{
            instruction = 'Listen to the source audio and fill in the missing words from the full article passage.'
            note = 'Each blank is selected from the full article content. Listen carefully and type the exact missing word.'
            items = $items
        }
        answer = @{
            items = $answerMap
        }
    }
}

$sb = New-Object System.Text.StringBuilder
[void]$sb.AppendLine('-- Auto-generated from generated_article_exercise_dataset.json')
[void]$sb.AppendLine('-- Includes auto-generated listening cloze items for every article')
[void]$sb.AppendLine('SET NAMES utf8mb4;')
[void]$sb.AppendLine('SET FOREIGN_KEY_CHECKS=0;')
[void]$sb.AppendLine('START TRANSACTION;')
[void]$sb.AppendLine('')
[void]$sb.AppendLine('INSERT INTO ai_prompts (id, type, prompt) VALUES')
[void]$sb.AppendLine("(1, 'speaking', 'Evaluate this speaking response based on: 1) Fluency and coherence, 2) Pronunciation, 3) Vocabulary range, 4) Grammatical accuracy. Provide a score from 0-100 and specific feedback for improvement.'),")
[void]$sb.AppendLine("(2, 'writing', 'Evaluate this writing task based on: 1) Task achievement, 2) Coherence and cohesion, 3) Lexical resource, 4) Grammatical range and accuracy. Provide a score from 0-100 and detailed feedback.')")
[void]$sb.AppendLine('ON DUPLICATE KEY UPDATE type=VALUES(type), prompt=VALUES(prompt);')
[void]$sb.AppendLine('')
[void]$sb.AppendLine('TRUNCATE TABLE exercises;')
[void]$sb.AppendLine('TRUNCATE TABLE articles;')
[void]$sb.AppendLine('')

foreach ($item in $data) {
    $a = $item.article
    $articleId = [int]$a.article_id

    [void]$sb.AppendLine("INSERT INTO articles (id, title, content, audio_url, difficulty, word_count) VALUES ($articleId, $(SqlString $a.title), $(SqlString $a.content), $(SqlString $a.audio_url), $([int]$a.difficulty), $([int]$a.word_count));")

    $listen = NewListeningExercise -content ([string]$a.content)
    [void]$sb.AppendLine("INSERT INTO exercises (article_id, type, question_data, answer, ai_prompt_id) VALUES ($articleId, 'listening', $(SqlJson $listen.question_data), $(SqlJson $listen.answer), NULL);")

    foreach ($ex in $item.exercises) {
        $promptId = if ($null -eq $ex.ai_prompt_id) { 'NULL' } else { [string][int]$ex.ai_prompt_id }
        [void]$sb.AppendLine("INSERT INTO exercises (article_id, type, question_data, answer, ai_prompt_id) VALUES ($articleId, $(SqlString $ex.type), $(SqlJson $ex.question_data), $(SqlJson $ex.answer), $promptId);")
    }

    [void]$sb.AppendLine('')
}

[void]$sb.AppendLine('COMMIT;')
[void]$sb.AppendLine('SET FOREIGN_KEY_CHECKS=1;')

[System.IO.File]::WriteAllText($outPath, $sb.ToString(), [System.Text.UTF8Encoding]::new($false))
Write-Output "SQL regenerated with listening exercises: $outPath"
