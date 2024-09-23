<?php

declare(strict_types=1);

namespace App\Pipes\Movement;

use App\Models\Debt;
use App\Models\Quick;
use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class OrderByPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $orderBy = request('order_by');
        $orderType = request('order_type');
        $orderType = $orderType === 'a' ? 'ASC' : 'DESC';

        switch ($orderBy) {
            case 'title':
                $query
                    ->leftJoin('quicks', function (JoinClause $join): void {
                        $join->on('quicks.id', '=', 'movements.movementable_id')
                            ->where('movements.movementable_type', Quick::class);
                    })
                    ->leftJoin('debts', function (JoinClause $join): void {
                        $join->on('debts.id', '=', 'movements.movementable_id')
                            ->where('movements.movementable_type', Debt::class);
                    })
                    ->orderBy(DB::raw('
                    (
                        case
                        when quicks.title is not null THEN quicks.title
                        else debts.title
                        end
                    )'), $orderType);
                break;
            case 'amount':
                $query->orderBy('movements.amount', $orderType);
                break;
            case 'identifier':
                $query->leftJoin(
                    'identifiers',
                    'identifiers.id',
                    '=',
                    'movements.identifier_id'
                )->orderBy('identifiers.name', $orderType);
                break;
            default:
                $query->orderBy(
                    DB::raw('COALESCE(movements.closed_date, movements.effetive_date)'),
                    $orderType
                );
        }
        $query->orderBy('movements.id', 'DESC');

        return $next($query);
    }
}
