<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Identifier;
use Illuminate\Database\Eloquent\Collection;

interface IdentifierRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;

    public function create(int $userId, array $attribures): Identifier;

    public function update(int $id, array $attributes): bool;

    public function delete(int $id): bool;
}
