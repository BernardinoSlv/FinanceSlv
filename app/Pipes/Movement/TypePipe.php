<?php

declare(strict_types=1);

namespace App\Pipes\Movement;

use App\Enums\MovementTypeEnum;
use App\Pipes\PipeContract;
use Closure;

class TypePipe implements PipeContract
{
    public function handle($query, Closure $next)
    {
        $type = request("type");

        if ($type === "in")
            $query->where("movements.type", MovementTypeEnum::IN->value);
        else if ($type === "out")
            $query->where("movements.type", MovementTypeEnum::OUT->value);

        return $next($query);
    }
}
