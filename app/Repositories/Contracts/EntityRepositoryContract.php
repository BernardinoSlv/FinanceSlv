<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Collection;

interface EntityRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;

    public function create(int $userId, array $attribures): Entity;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
