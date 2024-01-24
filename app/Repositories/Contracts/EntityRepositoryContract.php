<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EntityRepositoryContract
{
    public function allByUser(int $userId, bool $onlyCurrentMonth = false): Collection;
}
