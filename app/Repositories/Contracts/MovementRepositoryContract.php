<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Movement;

interface MovementRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId, array $attributes): Movement;

    public function deletePolymorph(string $movementableType, int $movementableId): int;

}
