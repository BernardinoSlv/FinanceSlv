<?php

declare(strict_types=1);

namespace App\Pipes\Movement;

use App\Models\Debt;
use App\Models\Quick;
use App\Pipes\PipeContract;
use Closure;

class OperationTypePipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $operationType = request('operation_type');

        if ($operationType === 'quick') {
            $query->where('movements.movementable_type', Quick::class);
        } elseif ($operationType === 'debt') {
            $query->where('movements.movementable_type', Debt::class);
        }

        return $next($query);
    }
}
