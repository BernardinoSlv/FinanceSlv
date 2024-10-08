<?php

namespace App\Models;

use App\Support\Models\Traits\HasMovements as TraitsHasMovements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes, TraitsHasMovements;

    protected $fillable = [
        'identifier_id',
        'title',
        'description',
        'amount',
        'installments',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function identifier(): BelongsTo
    {
        return $this->belongsTo(Identifier::class, 'identifier_id', 'id')->withTrashed();
    }
}
