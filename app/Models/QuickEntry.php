<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        "identifier_id",
        "title",
        "description",
        "amount"
    ];
}
