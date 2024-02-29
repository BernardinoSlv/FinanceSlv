<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Models\Traits\DatetimeFormated;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
    use HasFactory;
    use DatetimeFormated;
    use SoftDeletes;

    protected $fillable = [
        "identifier_id",
        "title",
        "description",
        "amount",
        "entryable_type",
        "entryable_id"
    ];
}
