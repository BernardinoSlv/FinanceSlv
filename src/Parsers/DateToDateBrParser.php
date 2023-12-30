<?php

declare(strict_types=1);

namespace Src\Parsers;

final class DateToDateBrParser implements ParserContract
{
    /**
     * @param string $value
     * @return string
     */
    public static function parse(mixed $value)
    {
        return date("d/m/Y H:i:s", strtotime($value));
    }
}
