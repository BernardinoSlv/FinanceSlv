<?php

declare(strict_types=1);

namespace App\Support\Models\Traits;

use App\Models\Movement;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMovements
{
    public function movements(): MorphMany
    {
        return $this->morphMany(Movement::class, 'movementable');
    }

    public function movement(): MorphOne
    {
        return $this->morphOne(Movement::class, 'movementable');
    }
}
