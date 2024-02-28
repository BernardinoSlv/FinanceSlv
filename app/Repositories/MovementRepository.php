<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Movement;
use App\Repositories\Contracts\MovementRepositoryContract;

class MovementRepository extends BaseRepository implements MovementRepositoryContract
{
    public function __construct(Movement $movement)
    {
        parent::__construct($movement);
    }


    public function create(int $userId, array $attributes): Movement
    {
        $movement = new Movement($attributes);
        $movement->user_id = $userId;
        $movement->save();
        return $movement;
    }
}
