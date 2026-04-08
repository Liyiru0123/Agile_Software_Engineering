<?php

namespace Tests\Unit;

use App\Services\ArticleTextProcessor;
use PHPUnit\Framework\TestCase;

class ArticleTextProcessorTest extends TestCase
{
    public function test_it_keeps_titles_and_country_abbreviations_inside_the_sentence(): void
    {
        $processor = new ArticleTextProcessor();

        $sentences = $processor->splitSentences('Mr. Smith met Dr. Brown in the U.S. They discussed bridge safety.');

        $this->assertSame([
            'Mr. Smith met Dr. Brown in the U.S.',
            'They discussed bridge safety.',
        ], $sentences);
    }

    public function test_it_does_not_split_decimal_numbers_or_dates(): void
    {
        $processor = new ArticleTextProcessor();

        $sentences = $processor->splitSentences('The beam is 3.14 meters long. It was checked on Jan. 5.');

        $this->assertSame([
            'The beam is 3.14 meters long.',
            'It was checked on Jan. 5.',
        ], $sentences);
    }

    public function test_it_keeps_initials_together(): void
    {
        $processor = new ArticleTextProcessor();

        $sentences = $processor->splitSentences('J. K. Rowling wrote the foreword. Prof. Adams reviewed it.');

        $this->assertSame([
            'J. K. Rowling wrote the foreword.',
            'Prof. Adams reviewed it.',
        ], $sentences);
    }

    public function test_it_splits_paragraphs_on_blank_lines_and_normalizes_whitespace(): void
    {
        $processor = new ArticleTextProcessor();

        $paragraphs = $processor->splitParagraphs(" First paragraph has\nextra space. \n\n\n Second\tparagraph stays together. ");

        $this->assertSame([
            'First paragraph has extra space.',
            'Second paragraph stays together.',
        ], $paragraphs);
    }

    public function test_it_builds_segment_metadata_for_each_sentence(): void
    {
        $processor = new ArticleTextProcessor();

        $segments = $processor->buildSegments("Paragraph one. Another sentence.\n\nParagraph two!");

        $this->assertSame([
            [
                'paragraph_index' => 0,
                'sentence_index' => 0,
                'content_en' => 'Paragraph one.',
                'content_cn' => null,
                'start_time' => null,
                'end_time' => null,
            ],
            [
                'paragraph_index' => 0,
                'sentence_index' => 1,
                'content_en' => 'Another sentence.',
                'content_cn' => null,
                'start_time' => null,
                'end_time' => null,
            ],
            [
                'paragraph_index' => 1,
                'sentence_index' => 0,
                'content_en' => 'Paragraph two!',
                'content_cn' => null,
                'start_time' => null,
                'end_time' => null,
            ],
        ], $segments);
    }
}
