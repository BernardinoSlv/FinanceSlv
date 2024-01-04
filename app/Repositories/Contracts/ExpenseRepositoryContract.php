<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Collection;

interface ExpenseRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;

    public function create(int $userId, array $attributes): Expense;
}
