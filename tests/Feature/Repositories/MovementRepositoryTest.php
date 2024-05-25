<?php

namespace Tests\Feature\Repositories;

use App\Models\Debtor;
use App\Models\Entry;
use App\Models\Movement;
use App\Models\User;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovementRepositoryTest extends TestCase
{
    protected function _repository(): MovementRepositoryContract
    {
        return app(MovementRepositoryContract::class);
    }
}
