<?php

declare(strict_types=1);

namespace Src\Parsers;

use Error;

interface ParserContract
{
    /**
     * @return mixed
     */
    public static function parse(mixed $value): mixed;
}
