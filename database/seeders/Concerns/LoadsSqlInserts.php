<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;
use RuntimeException;

trait LoadsSqlInserts
{
    /**
     * Execute INSERT statements for the given table names from database/sql/english_reading.sql.
     *
     * @param array<int, string> $tableNames
     */
    protected function seedTablesFromSql(array $tableNames): void
    {
        $sqlPath = database_path('sql/english_reading.sql');

        if (!is_file($sqlPath)) {
            throw new RuntimeException('SQL file not found: '.$sqlPath);
        }

        $sql = file_get_contents($sqlPath);

        if ($sql === false) {
            throw new RuntimeException('Unable to read SQL file: '.$sqlPath);
        }

        $executed = 0;

        foreach ($tableNames as $tableName) {
            $insertBlocks = $this->extractInsertBlocksForTable($sql, $tableName);

            foreach ($insertBlocks as $insertSql) {
                $statement = trim($insertSql);

                if (DB::getDriverName() === 'sqlite') {
                    $statement = $this->normalizeSqliteStringLiterals($statement);
                }

                DB::unprepared($statement);
                $executed++;
            }
        }

        if ($executed === 0) {
            throw new RuntimeException('No INSERT statements found in english_reading.sql for tables: '.implode(', ', $tableNames));
        }
    }

    /**
     * Extract all INSERT blocks for one table from the SQL dump.
     *
     * @return array<int, string>
     */
    private function extractInsertBlocksForTable(string $sql, string $tableName): array
    {
        $pattern = '/INSERT\s+INTO\s+`?'.preg_quote($tableName, '/').'`?.*?;\s*(?=(?:--|LOCK\s+TABLES|DROP\s+TABLE|UNLOCK\s+TABLES|SET\s+@@SESSION|$))/is';

        preg_match_all($pattern, $sql, $matches);

        return $matches[0] ?? [];
    }

    /**
     * Normalize unescaped apostrophes in SQL string literals for SQLite.
     *
     * The source SQL is authored for MySQL and includes natural-language text
     * with apostrophes inside quoted literals; SQLite is stricter here.
     */
    private function normalizeSqliteStringLiterals(string $statement): string
    {
        $result = '';
        $inString = false;
        $length = strlen($statement);

        for ($i = 0; $i < $length; $i++) {
            $char = $statement[$i];

            if (!$inString) {
                if ($char === "'") {
                    $inString = true;
                }

                $result .= $char;
                continue;
            }

            if ($char !== "'") {
                $result .= $char;
                continue;
            }

            $next = $i + 1 < $length ? $statement[$i + 1] : '';

            // Already-escaped single quote in SQL literal.
            if ($next === "'") {
                $result .= "''";
                $i++;
                continue;
            }

            // Treat quote as string terminator only when the next non-space
            // token is a SQL value delimiter; otherwise keep it in the text.
            if ($this->isClosingStringQuote($statement, $i)) {
                $result .= "'";
                $inString = false;
                continue;
            }

            $result .= "''";
        }

        return $result;
    }

    private function isClosingStringQuote(string $statement, int $quoteIndex): bool
    {
        $length = strlen($statement);

        for ($j = $quoteIndex + 1; $j < $length; $j++) {
            $char = $statement[$j];

            if ($char === ' ' || $char === "\t" || $char === "\n" || $char === "\r") {
                continue;
            }

            if ($char === ')' || $char === ';') {
                return true;
            }

            if ($char !== ',') {
                return false;
            }

            // After a value-separating comma, the next non-space token should
            // look like the next SQL value, not plain prose.
            for ($k = $j + 1; $k < $length; $k++) {
                $next = $statement[$k];

                if ($next === ' ' || $next === "\t" || $next === "\n" || $next === "\r") {
                    continue;
                }

                if ($next === "'" || $next === '(' || $next === ')' || ctype_digit($next)) {
                    return true;
                }

                if ($next === 'N') {
                    return true; // NULL
                }

                return false;
            }

            return true;
        }

        return true;
    }
}
