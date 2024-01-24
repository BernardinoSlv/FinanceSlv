<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Entity;
use App\Repositories\Contracts\EntityRepositoryContract;

class EntityRepository extends BaseRepository implements EntityRepositoryContract
{
    public function __construct(Entity $entity)
    {
        parent::__construct($entity);
    }
}
