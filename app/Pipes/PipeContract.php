<?php

declare(strict_types=1);

namespace App\Pipes;

use Closure;

interface PipeContract
{
    public function handle($query, Closure $next);
}
