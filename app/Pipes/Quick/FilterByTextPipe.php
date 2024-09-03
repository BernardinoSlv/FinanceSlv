<?php

declare(strict_types=1);

namespace App\Pipes\Quick;

use App\Pipes\PipeContract;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterByTextPipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $text = request('text');
        $searchBy = request('search_by', null);

        if ($text) {
            if ($searchBy === 'identifier') {
                $query
                    ->leftJoin(
                        'identifiers',
                        'identifiers.id',
                        '=',
                        'quicks.identifier_id'
                    )
                    ->where('identifiers.name', 'LIKE', "%{$text}%");
            } elseif ($searchBy === 'title') {
                $query->where('quicks.title', 'LIKE', "%{$text}%");
            } else {
                $query
                    ->leftJoin(
                        'identifiers',
                        'identifiers.id',
                        '=',
                        'quicks.identifier_id'
                    )
                    ->where(function (Builder $query) use ($text) {
                        $query->where('identifiers.name', 'LIKE', "%{$text}%")
                            ->orWhere('quicks.title', 'LIKE', "%{$text}%");
                    });
            }
        }

        return $next($query);
    }
}
