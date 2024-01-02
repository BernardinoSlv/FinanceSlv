<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Leave;
use Illuminate\Database\Eloquent\Collection;

interface LeaveRepositoryContract
{
    public function allByUser(int $id, bool $onlyCurrentMonth = false): Collection;

    public function create(int $userId,  array $attributes): Leave;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
