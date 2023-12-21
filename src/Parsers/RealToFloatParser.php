<?php

declare(strict_types=1);

namespace Src\Parsers;

/**
 * passar o valor para float
 */
class RealToFloatParser implements ParserContract
{
    /**
     * @param string $value
     * @return float
     */
    public static function parse(mixed $value): mixed
    {
        return (float) str_replace(",", ".", str_replace(".", "", strval($value)));
    }
}
