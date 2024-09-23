<?php

namespace App\Models;

use App\Support\Models\Traits\HasMovements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quick extends Model
{
    use HasFactory, HasMovements, SoftDeletes;

    protected $fillable = [
        'user_id',
        'identifier_id',
        'title',
        'description',
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
