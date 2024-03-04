<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\QuickEntry;

interface QuickEntryRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId, array $attributes): QuickEntry;
}
