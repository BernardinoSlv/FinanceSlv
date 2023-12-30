<?php

declare(strict_types=1);

namespace App\Support\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Src\Parsers\DateToDateBrParser;

/**
 * adicionar created_at_formated and updated_at_update attributos
 */
trait DatetimeFormated
{
    public function createdAtFormated(): Attribute
    {
        return Attribute::make(
            fn () => DateToDateBrParser::parse($this->attributes["created_at"])
        );
    }

    public function updatedAtFormated(): Attribute
    {
        return Attribute::make(
            fn () => DateToDateBrParser::parse($this->attributes["updated_at"]),
        );
    }
}
