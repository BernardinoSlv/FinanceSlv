<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Need;
use App\Repositories\Contracts\NeedRepositoryContract;

class NeedRepository extends BaseRepository implements NeedRepositoryContract
{
    public function __construct(Need $need)
    {
        parent::__construct($need);
    }

    public function create(int $userId, array $attributes): Need
    {
        $need = $this->_model->fill($attributes);
        $need->user_id = $userId;
        $need->save();
        return $need;
    }
}
