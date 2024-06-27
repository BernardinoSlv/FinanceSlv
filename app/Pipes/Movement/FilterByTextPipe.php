<?php

declare(strict_types=1);

namespace App\Pipes\Movement;

use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class FilterByTextPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $text = request("text");
        $searchBy = request("search_by");

        if ($text) {
            // !! não verifiquei se já houve join
            if ($searchBy === "title") {
                $query->whereHas("movementable", function (Builder $query) use ($text): void {
                    $query->where("title", "LIKE", "%{$text}%");
                });
            } else if ($searchBy === "identifier") {
                $query->leftJoin(
                    "identifiers",
                    "identifiers.id",
                    "=",
                    "movements.identifier_id"
                )
                    ->where("identifiers.name", "LIKE", "%{$text}%");
            } else {
                $query
                    ->leftJoin(
                        "identifiers",
                        "identifiers.id",
                        "=",
                        "movements.identifier_id"
                    )
                    ->where(function (Builder $query) use ($text) {
                        $query

                            ->whereHas("movementable", function (Builder $query) use ($text): void {
                                $query->where("title", "LIKE", "%{$text}%");
                            })
                            ->orWhere("identifiers.name", "LIKE", "%{$text}%");
                    });
            }
        }
        return $next($query);
    }
}
