<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EntryRepositoryContract
{
    /**
     *
     * @param integer $id
     * @return Collection
     */
    public function allByUser(int $id): Collection;
}
