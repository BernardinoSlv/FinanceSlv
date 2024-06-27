<?php

declare(strict_types=1);

namespace App\Pipes\Quick;

use App\Models\Quick;
use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Query\JoinClause;

class OrderByPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $orderBy = request("order_by");
        $orderType = request("order_type");
        $orderType = $orderType === "a" ? "ASC" : "DESC";

        switch ($orderBy) {
            case "title":
                $query->orderBy("quicks.title", $orderType);
                break;
            case "amount":
                // !! não checkei se já houve junção
                $query->leftJoin("movements", function (JoinClause $join) {
                    $join->on("movements.movementable_id", "=", "quicks.id")
                        ->where("movements.movementable_type", Quick::class);
                })
                    ->orderBy("movements.amount", $orderType);
                break;
            case "identifier":
                // !! Não checkei se já houve junção
                $query->leftJoin("identifiers", "identifiers.id", "=", "quicks.identifier_id")
                    ->orderBy("identifiers.name", $orderType);
                break;
            default:
                $query->orderBy("quicks.created_at", $orderType);
        }
        $query->orderBy("id", "desc");

        return $next($query);
    }
}
