<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;

class UserRepository extends BaseRepository implements UserRepositoryContract
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function create(array $attributes): ?User
    {
        return $this->_model->query()->create($attributes);
    }
}
