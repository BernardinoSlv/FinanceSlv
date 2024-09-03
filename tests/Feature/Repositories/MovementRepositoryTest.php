<?php

namespace Tests\Feature\Repositories;

use App\Repositories\Contracts\MovementRepositoryContract;
use Tests\TestCase;

class MovementRepositoryTest extends TestCase
{
    protected function _repository(): MovementRepositoryContract
    {
        return app(MovementRepositoryContract::class);
    }
}
