<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Identifier;

interface IdentifierRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId, array $attribures): Identifier;
}
