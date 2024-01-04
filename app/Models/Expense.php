<?php

namespace App\Models;

use App\Support\Models\Traits\DatetimeFormated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    use DatetimeFormated;

    protected $fillable = [
        "title",
        "amount",
        "quantity",
        "description",
        "effetive_at",
    ];
}
