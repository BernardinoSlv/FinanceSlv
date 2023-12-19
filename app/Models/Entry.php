<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Src\Parsers\RealToFloatParser;
use Src\Parsers\ToFloatParser;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "amount",
    ];

    public function amount(): Attribute
    {
        return Attribute::make(
            set: function (mixed $value) {
                $this->attributes["amount"] = RealToFloatParser::parse($value);
            }
        );
    }
}
