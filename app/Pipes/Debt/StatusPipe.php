<?php

declare(strict_types=1);

namespace App\Pipes\Debt;

use App\Pipes\PipeContract;
use Closure;
use Illuminate\Support\Facades\DB;

class StatusPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $status = request("status");

        switch ($status) {
            case "paid":
                $query->where(DB::raw("(
                    select sum(movements.amount) from movements
                    where movements.movementable_type = \"App\\\\Models\\\\Debt\"
                    and movements.movementable_id = debts.id
                )"), ">=", DB::raw("debts.amount"));
                break;
            case "paying":
                $query
                    ->where(DB::raw("(
                        select sum(movements.amount) from movements
                        where movements.movementable_type = \"App\\\\Models\\\\Debt\"
                        and movements.movementable_id = debts.id
                    )"), "<", DB::raw("debts.amount"))
                    ->where(DB::raw("(
                        select sum(movements.amount) from movements
                        where movements.movementable_type = \"App\\\\Models\\\\Debt\"
                        and movements.movementable_id = debts.id
                    )"), "!=", 0);
                break;
            case "no-paying";
                $query->where(DB::raw("(
                    select sum(movements.amount) from movements
                    where movements.movementable_type = \"App\\\\Models\\\\Debt\"
                    and movements.movementable_id = debts.id
                )"), "=", null);
                break;
        }


        return $next($query);
    }
}
