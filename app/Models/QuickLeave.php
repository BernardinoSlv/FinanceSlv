<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickLeave extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "identifier_id",
        "title",
        "description",
        "amount"
    ];

    public function leave(): MorphOne
    {
        return $this->morphOne(Leave::class, "leaveable");
    }
}
