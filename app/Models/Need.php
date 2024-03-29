<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Need extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        "identifier_id",
        "title",
        "amount",
        "description",
        "completed"
    ];
}
