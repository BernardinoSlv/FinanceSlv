<?php

declare(strict_types=1);

namespace App\Pipes\Quick;

use App\Enums\MovementTypeEnum;
use App\Models\Quick;
use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Query\JoinClause;

class TypePipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $type = request('type');

        if ($type === 'in') {
            $query->leftJoin('movements', function (JoinClause $join) {
                $join->on('movements.movementable_id', '=', 'quicks.id')
                    ->where('movements.movementable_type', Quick::class);
            })
                ->where('movements.type', MovementTypeEnum::IN->value);
        } elseif ($type === 'out') {
            $query->leftJoin('movements', function (JoinClause $join) {
                $join->on('movements.movementable_id', '=', 'quicks.id')
                    ->where('movements.movementable_type', Quick::class);
            })
                ->where('movements.type', MovementTypeEnum::OUT->value);
        }

        return $next($query);
    }
}
