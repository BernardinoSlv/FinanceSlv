<?php

namespace Tests\Unit\Src\Parsers;

use PHPUnit\Framework\TestCase;
use Src\Parsers\DateToDateBrParser;

class DateToDateBrParserTest extends TestCase
{
    /**
     * @dataProvider DatesProvider
     *
     * @return void
     */
    public function test_parse(string $value, string $expected): void
    {
        $this->assertEquals(
            $expected,
            DateToDateBrParser::parse($value)
        );
    }

    public static function DatesProvider(): array
    {
        return [
            ["2001-01-18 13:00:00", "18/01/2001 13:00:00"],
            ["2001-12-07 14:00:00", "07/12/2001 14:00:00"],
            ["2022-07-30 11:00:00", "30/07/2022 11:00:00"]
        ];
    }
}
