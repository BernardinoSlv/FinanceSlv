<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Need;
use Illuminate\Database\Eloquent\Collection;

interface NeedRepositoryContract
{
    public function create(int $userId, array $attributes): Need;
}
