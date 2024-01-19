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
}
