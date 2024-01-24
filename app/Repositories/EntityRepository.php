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

    public function create(int $userId, array $attribures): Entity
    {
        $entity = $this->_model->newInstance($attribures);
        $entity->user_id = $userId;
        $entity->save();
        return $entity;
    }
}
