<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Need;
use Illuminate\Database\Eloquent\Collection;

interface NeedRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;

    public function create(int $userId, array $attributes): Need;

    public function update(int $id, array $attributes): bool;
}
