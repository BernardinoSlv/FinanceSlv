<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Investiment;
use App\Repositories\Contracts\InvestimentRepositoryContract;

class InvestimentRepository extends BaseRepository implements InvestimentRepositoryContract
{
    public function __construct(Investiment $investiment)
    {
        parent::__construct($investiment);
    }
}
