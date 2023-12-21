<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $_model;

    public function __construct(Model $model)
    {
        $this->_model = $model;
    }

    public function update(int $id, array $attributes): bool
    {
        if (!($entity = $this->_model->find($id))) {
            return false;
        }
        $entity->fill($attributes);
        $entity->save();
        return true;
    }
}
