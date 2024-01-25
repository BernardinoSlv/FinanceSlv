<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Identifier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "avatar",
        "name",
        "phone",
        "description"
    ];
}
