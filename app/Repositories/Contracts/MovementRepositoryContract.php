<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Movement;

interface MovementRepositoryContract
{
    public function create(int $userId, array $attributes): Movement;
}
