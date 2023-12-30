<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Support\Models\Traits\DatetimeFormated;

class Entry extends Model
{
    use HasFactory;
    use DatetimeFormated;

    protected $fillable = [
        "title",
        "description",
        "amount",
    ];
}
