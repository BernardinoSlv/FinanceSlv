<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Collection;

interface EntryRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId,  array $attributes): Entry;

    /**
     * remove registros polimorficos
     */
    public function deletePolymorph(string $entryableType, int $entryableId): int;
}
