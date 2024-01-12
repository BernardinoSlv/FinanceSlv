<?php

namespace Tests\Feature\Repositories;

use App\Models\Debtor;
use App\Repositories\Contracts\DebtorRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class DebtorRepositoryTest extends TestCase
{
    public function test_create(): void
    {
        $user = $this->_user();
        $data = Debtor::factory()->make()->toArray();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas("debtors", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): DebtorRepositoryContract
    {
        return App::make(DebtorRepositoryContract::class);
    }
}
