<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Debtor;
use Illuminate\Database\Eloquent\Collection;

interface DebtorRepositoryContract
{
    public function create(int $userId, array $attributes): Debtor;
}
