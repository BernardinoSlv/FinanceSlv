<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Debt;
use App\Repositories\Contracts\DebtRepositoryContract;

class DebtRepository extends BaseRepository implements DebtRepositoryContract
{
    public function __construct(Debt $debt)
    {
        parent::__construct($debt);
    }
}
