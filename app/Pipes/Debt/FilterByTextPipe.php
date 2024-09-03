<?php

declare(strict_types=1);

namespace App\Pipes\Debt;

use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByTextPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $text = request('text');
        $searchBy = request('search_by');

        if ($searchBy === 'title') {
            $query->where('debts.title', 'LIKE', "%{$text}%");
        } elseif ($searchBy === 'identifier') {
            // não verifiquei se há left join
            $query->leftJoin(
                'identifiers',
                'identifiers.id',
                '=',
                'debts.identifier_id'
            )->where('identifiers.name', 'LIKE', "%{$text}%");
        } else {
            $query->leftJoin(
                'identifiers',
                'identifiers.id',
                '=',
                'debts.identifier_id'
            )->where(function (Builder $query) use ($text): void {
                $query->where('debts.title', 'LIKE', "%{$text}%")
                    ->orWhere('identifiers.name', 'LIKE', "%{$text}%");
            });
        }

        return $next($query);
    }
}
