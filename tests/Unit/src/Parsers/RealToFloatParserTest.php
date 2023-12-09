<?php

namespace Tests\Unit\Parsers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Src\Parsers\RealToFloatParser;
use Tests\TestCase;

class RealToFloatParserTest extends TestCase
{
    public static function valuesProvider(): array
    {
        return [
            ["00,01", .01],
            ["00,10", .1],
            ["00,11", .11],
            ["01,00", 1],
            ["10,00", 10],
            ["11,00", 11]
        ];
    }

    /**
     * deve retonar 10.10
     *
     * @dataProvider valuesProvider
     *
     * @return void
     */
    public function test_parse_method(float|string $value, float $expected): void
    {
        $this->assertEquals($expected, RealToFloatParser::parse($value));
    }
}
