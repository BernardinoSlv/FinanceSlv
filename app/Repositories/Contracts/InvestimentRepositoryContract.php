<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Investiment;
use Illuminate\Database\Eloquent\Collection;

interface InvestimentRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;

    public function create(int $userId, array $attributes): Investiment;

    public function update(int $id, array $attributes): bool;
}
