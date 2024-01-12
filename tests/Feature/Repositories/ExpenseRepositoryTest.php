<?php

namespace Tests\Feature\Repositories;

use App\Models\Expense;
use App\Models\User;
use App\Repositories\Contracts\ExpenseRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;


    public function test_create_method(): void
    {
        $user = User::factory()->create();
        $data = Expense::factory()->make()->toArray();

        $this->assertNotNull($this->_repository()->create($user->id, $data));
        $this->assertDatabaseHas("expenses", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): ExpenseRepositoryContract
    {
        return app(ExpenseRepositoryContract::class);
    }
}
