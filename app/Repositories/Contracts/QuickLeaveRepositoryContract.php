<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\QuickLeave;

interface QuickLeaveRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId, array $attributes): QuickLeave;
}
