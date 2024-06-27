<?php

declare(strict_types=1);

namespace App\Pipes\Debt;

use App\Models\Debt;
use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class OrderByPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $orderBy = request("order_by");
        $orderType = request("order_type");
        $orderType = $orderType === "a" ? "ASC" : "DESC";

        switch ($orderBy) {
            case "title":
                $query->orderBy("debts.title", $orderType);
                break;
            case "amount":
                $query->orderBy("debts.amount", $orderType);
                break;
            case "amount_paid":
                $query->orderBy(
                    DB::raw("(select sum(`movements`.`amount`) from `movements` where `movements`.`movementable_type` = 'App\\\\Models\\\\Debt' and `movements`.`movementable_id` = `debts`.`id`)"),
                    $orderType
                );
                break;
            case "due_date":
                $query->orderBy("debts.due_date", $orderType);
                break;
            case "identifier":
                // !! Não checkei se já houve junção
                $query
                    ->orderBy("identifiers.name", $orderType);
                break;
            default:
                $query->orderBy("debts.created_at", $orderType);
        }
        $query->orderBy("debts.id", "desc");
        // $query->ddRawSql();

        return $next($query);
    }
}
