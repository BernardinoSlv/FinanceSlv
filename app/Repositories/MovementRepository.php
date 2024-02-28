<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Movement;
use App\Repositories\Contracts\MovementRepositoryContract;

class MovementRepository extends BaseRepository implements MovementRepositoryContract
{
    public function create(int $userId, array $attributes): Movement
    {
        $movement =
    }
}
