<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Collection;

interface LeaveRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId,  array $attributes): Leave;

    /**
     * remove registros polimorficos
     */
    public function deletePolymorph(string $leaveableType, int $leaveableId): int;
}
