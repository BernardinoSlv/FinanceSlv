<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryContract;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryContract
{
    public function __construct(Expense $expense)
    {
        parent::__construct($expense);
    }
}
