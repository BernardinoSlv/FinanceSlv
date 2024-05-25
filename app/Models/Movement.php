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
        "movementable_type",
        "movementable_id",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id")->withTrashed();
    }

    public function movementable(): MorphTo
    {
        return $this->morphTo("movementable")->withTrashed();
    }

    /**
     * obtém as movimentação de um identifier
     */
    public function scopeByIdentifier(Builder $query, int $identifierId)
    {
        $query
            // join with quicks
            ->leftJoin(
                "quicks",
                fn (JoinClause $join) => $join
                    ->on("movements.movementable_id", "=", "quicks.id")
                    ->where("movementable_type", Quick::class)
            )
            ->where("quicks.identifier_id", $identifierId);
    }
}
