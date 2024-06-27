<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;

class Movement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "user_id",
        "identifier_id",
        "movementable_type",
        "movementable_id",
        "type",
        "amount"
    ];

    public $casts = [
        "effetive_at" => "date"
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id")->withTrashed();
    }

    public function movementable(): MorphTo
    {
        return $this->morphTo("movementable")->withTrashed();
    }

    public function identifier(): BelongsTo
    {
        return $this->belongsTo(
            Identifier::class,
            "identifier_id",
            "id"
        )->withTrashed();
    }
}
