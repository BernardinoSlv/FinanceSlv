<?php

declare(strict_types=1);

namespace App\Support\Models\Traits;

use App\Models\Log;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasLogs
{
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, "loggable");
    }
}
