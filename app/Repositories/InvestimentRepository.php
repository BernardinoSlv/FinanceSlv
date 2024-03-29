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
        $investiment = new Investiment($attributes);
        $investiment->user_id = $userId;
        $investiment->save();
        return $investiment;
    }
}
