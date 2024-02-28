<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Investiment;
use Illuminate\Database\Eloquent\Collection;

interface InvestimentRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId, array $attributes): Investiment;
}
