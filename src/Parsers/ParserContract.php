<?php

declare(strict_types=1);

namespace Src\Parsers;

interface ParserContract
{
    /**
     * @return mixed
     */
    public static function parse(mixed $value);
}
