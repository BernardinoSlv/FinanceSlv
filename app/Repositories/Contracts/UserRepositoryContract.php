<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryContract
{
    /**
     * @param array $attributes
     * @return User|null
     */
    public function create(array $attributes): ?User;
}
