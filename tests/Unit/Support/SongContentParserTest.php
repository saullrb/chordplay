<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Enums\SongLineContentType;
use App\Support\SongContentParser;
use PHPUnit\Framework\TestCase;

class SongContentParserTest extends TestCase
{
    private SongContentParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new SongContentParser;
    }

    public function test_parses_single_lyrics_line(): void
    {
        $content = 'Hello world';
        [$lines, $chords] = $this->parser->parse($content);

        $expectedLines = [
            [
                'line_number' => 0,
                'content_type' => SongLineContentType::LYRICS,
                'content' => 'Hello world',
            ],
        ];

        $this->assertEquals($expectedLines, $lines);
        $this->assertSame([], $chords);
    }

    public function test_parses_multiple_line_types(): void
    {
        $content = <<<'EOD'
        [C]This is a test
        [G]With multiple lines
        And lyrics only

        EOD;

        [$lines, $chords] = $this->parser->parse($content);

        $expectedLines = [
            [
                'line_number' => 0,
                'content_type' => SongLineContentType::CHORDS,
                'content' => '[C]   ',
            ],
            [
                'line_number' => 1,
                'content_type' => SongLineContentType::CHORDS,
                'content' => '[G]  ',
            ],
            [
                'line_number' => 2,
                'content_type' => SongLineContentType::LYRICS,
                'content' => 'And lyrics only',
            ],
        ];

        $this->assertEquals($expectedLines, $lines);
        $this->assertEquals(['C', 'G'], $chords);
    }

    public function test_extracts_multiple_chords_per_line(): void
    {
        $content = '[C][Am]Test line[F][G7]';
        [$lines, $chords] = $this->parser->parse($content);

        $expectedLines = [
            [
                'line_number' => 0,
                'content_type' => SongLineContentType::CHORDS,
                'content' => '[C][Am] [F][G7]',
            ],
        ];

        $this->assertEquals($expectedLines, $lines);
        $this->assertEquals(['C', 'Am', 'F', 'G7'], $chords);
    }

    public function test_handles_unclosed_chords(): void
    {
        $content = "[CThis is[Am] incomplete\nNew line";
        [$lines, $chords] = $this->parser->parse($content);

        $expectedLines = [
            [
                'line_number' => 0,
                'content_type' => SongLineContentType::CHORDS,
                'content' => '[Am] ',
            ],
            [
                'line_number' => 1,
                'content_type' => SongLineContentType::LYRICS,
                'content' => 'New line',
            ],
        ];

        $this->assertEquals($expectedLines, $lines);
        $this->assertEquals(['Am'], $chords);
    }

    public function test_ignores_non_chord_characters_in_chord_lines(): void
    {
        $content = "Before[C]text\n[ Dm ]after";
        [$lines, $chords] = $this->parser->parse($content);

        $this->assertSame('Before[C]text', $lines[0]['content']);
        $this->assertSame('[Dm]', $lines[1]['content']);
        $this->assertEquals(['Dm'], $chords);
    }

    public function test_preserves_spaces_in_chord_lines(): void
    {
        $content = '[C]  [G]  [Am]';
        [$lines, $chords] = $this->parser->parse($content);

        $this->assertSame('[C]  [G]  [Am]', $lines[0]['content']);
        $this->assertEquals(['C', 'G', 'Am'], $chords);
    }

    public function test_ignore_spaces_inside_brackets(): void
    {
        $content = '[C  ]  [   G]  [  Am  ]';
        [$lines, $chords] = $this->parser->parse($content);

        $this->assertSame('[C]  [G]  [Am]', $lines[0]['content']);
        $this->assertEquals(['C', 'G', 'Am'], $chords);
    }

    public function test_parses_complex_structure(): void
    {
        $content = <<<'EOD'
        [C] [G] [Am] [F]

        [C]Hello darkness, my old friend
        [G]
        I've come to talk with you again

        EOD;

        [$lines, $chords] = $this->parser->parse($content);

        $expectedChords = ['C', 'G', 'Am', 'F'];
        $expectedStructure = [
            ['line_number' => 0, 'content_type' => SongLineContentType::CHORDS, 'content' => '[C] [G] [Am] [F]'],
            ['line_number' => 1, 'content_type' => SongLineContentType::EMPTY, 'content' => ''],
            ['line_number' => 2, 'content_type' => SongLineContentType::CHORDS, 'content' => '[C]    '],
            ['line_number' => 3, 'content_type' => SongLineContentType::CHORDS, 'content' => '[G]'],
            ['line_number' => 4, 'content_type' => SongLineContentType::LYRICS, 'content' => "I've come to talk with you again"],
        ];

        $this->assertEquals($expectedChords, $chords);
        $this->assertEquals($expectedStructure, $lines);
    }
}
