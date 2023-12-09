<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Collection;

interface EntryRepositoryContract
{
    /**
     *
     * @param integer $id
     * @return Collection
     */
    public function allByUser(int $id): Collection;

    /**
     * @param integer $userId
     * @param array $attributes
     * @return Entry
     */
    public function create(int $userId,  array $attributes): Entry;
}
