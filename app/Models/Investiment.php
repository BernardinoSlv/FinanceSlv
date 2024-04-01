<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investiment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "identifier_id",
        "title",
        "description"
    ];

    public function leaves(): MorphMany
    {
        return $this->morphMany(Leave::class, "leaveable");
    }

    public function entries(): MorphMany
    {
        return $this->morphMany(Entry::class, "entryable");
    }
}
