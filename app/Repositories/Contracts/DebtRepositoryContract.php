<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Collection;

interface DebtRepositoryContract
{
    public function create(int $userId, array $attributes): Debt;
}
