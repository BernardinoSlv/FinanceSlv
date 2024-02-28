<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "identifier_id",
        "title",
        "description",
        "amount",
        "leaveable_type",
        "leaveable_id"
    ];
}
