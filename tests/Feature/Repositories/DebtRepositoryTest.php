<?php

namespace Tests\Feature\Repositories;

use App\Models\Debt;
use App\Models\User;
use App\Repositories\Contracts\DebtRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class DebtRepositoryTest extends TestCase
{
    public function test_create_method(): void
    {
        $user = User::factory()->create();
        $data = Debt::factory()->make()->toArray();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas("debts", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): DebtRepositoryContract
    {
        return App::make(DebtRepositoryContract::class);
    }
}
