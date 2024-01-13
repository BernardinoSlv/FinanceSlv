<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Investiment;
use App\Repositories\Contracts\InvestimentRepositoryContract;

class InvestimentRepository extends BaseRepository implements InvestimentRepositoryContract
{
    public function __construct(Investiment $investiment)
    {
        parent::__construct($investiment);
    }

    public function create(int $userId, array $attributes): Investiment
    {
        $this->_model->fill($attributes);
        $this->_model->user_id = $userId;
        $this->_model->save();
        return $this->_model;
    }
}
