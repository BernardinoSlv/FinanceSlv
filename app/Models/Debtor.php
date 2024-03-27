<?php

namespace App\Models;

use App\Support\Models\Traits\DatetimeFormated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debtor extends Model
{
    use HasFactory;
    use SoftDeletes;
    use DatetimeFormated;

    protected $fillable = [
        "identifier_id",
        "title",
        "amount",
        "description",
    ];

    public function entries(): MorphMany
    {
        return $this->morphMany(Entry::class, "entryable");
    }
}
