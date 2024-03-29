<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickEntry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "identifier_id",
        "title",
        "description",
        "amount"
    ];

    public function entry(): MorphOne
    {
        return $this->morphOne(Entry::class, "entryable");
    }
}
