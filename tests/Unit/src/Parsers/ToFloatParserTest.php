<?php

namespace Tests\Unit\Parsers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Src\Parsers\ToFloatParser;
use Tests\TestCase;

class ToFloatParserTest extends TestCase
{
    public static function valuesProvider(): array
    {
        return [
            [10.10, 10.1],
            [10.13, 10.13],
            [20, 20.0],
            [0.9, .9],
            [.9, .9],

            ["10.10", 10.1],
            ["10.13", 10.13],
            ["20", 20.0],
            ["0.9", .9],
            [".9", .9],

            ["10,10", 10.1],
            ["10,13", 10.13],
            ["20", 20.0],
            ["0,9", .9],
            [",9", .9],
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
        $this->assertEquals($expected, ToFloatParser::parse($value));
    }
}
