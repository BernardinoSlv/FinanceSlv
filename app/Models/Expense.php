<?php

namespace App\Models;

use App\Support\Models\Traits\HasMovements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, HasMovements;

    public $fillable = [
        "identifier_id",
        "title",
        "description",
        "amount",
        "due_day"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id")->withTrashed();
    }

    public function identifier(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, "identifier_id", "id")->withTrashed();
    }
}
