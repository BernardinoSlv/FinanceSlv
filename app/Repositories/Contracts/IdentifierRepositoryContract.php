<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Identifier;
use Illuminate\Database\Eloquent\Collection;

interface IdentifierRepositoryContract extends BaseRepositoryContract
{
    public function create(int $userId, array $attribures): Identifier;
}
