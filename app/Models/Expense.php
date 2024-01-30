<?php

namespace App\Models;

use App\Support\Models\Traits\DatetimeFormated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use SoftDeletes;
    use DatetimeFormated;

    protected $fillable = [
        "identifier_id",
        "title",
        "amount",
        "quantity",
        "description",
        "effetive_at",
    ];
}
