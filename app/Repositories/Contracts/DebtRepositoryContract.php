<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface DebtRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;
}
