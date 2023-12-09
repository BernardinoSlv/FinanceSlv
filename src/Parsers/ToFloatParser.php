<?php

declare(strict_types=1);

namespace Src\Parsers;

/**
 * passar o valor para float
 */
class ToFloatParser implements ParserContract
{
    /**
     * @param mixed $value
     * @return float
     */
    public static function parse(mixed $value): mixed
    {
        $value = strval($value);
        // 100.00
        if (strpos(",", $value) && strpos(".", $value)) {
            return (float) str_replace(",", ".", str_replace(".", "", strval($value)));
        }
        return (float) str_replace(",", ".", $value);
    }
}
