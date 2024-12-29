<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "type",
        "description",
        "data"
    ];

    public function __construct(array $attributes = [])
    {
        $this->attributes["ip_address"] = request()->ip();
        if (auth()->check())
            $this->attributes["user_id"] = auth()->id();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id")->withTrashed();
    }
}
