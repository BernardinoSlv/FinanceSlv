<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryContract
{
    public function create(array $attributes): ?User;
}
