<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Debtor;
use App\Repositories\Contracts\DebtorRepositoryContract;

class DebtorRepository extends BaseRepository implements DebtorRepositoryContract
{
    public function __construct(Debtor $debtor)
    {
        parent::__construct($debtor);
    }
}
