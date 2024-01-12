<?php

namespace App\Models;

use App\Support\Models\Traits\DatetimeFormated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debtor extends Model
{
    use HasFactory;
    use SoftDeletes;
    use DatetimeFormated;

    protected $fillable = [
        "title",
        "amount",
        "description",
    ];
}
