<?php

namespace Tests\Feature\Repositories;

use App\Models\QuickLeave;
use App\Models\User;
use App\Repositories\Contracts\QuickLeaveRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuickLeaveRepositoryTest extends TestCase
{
    /**
     * deve criar o registro
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $data = QuickLeave::factory()->make()->toArray();

        $this->assertInstanceOf(
            QuickLeave::class,
            $this->_repository()->create($user->id, $data)
        );
        $this->assertDatabaseHas("quick_leaves", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): QuickLeaveRepositoryContract
    {
        return app(QuickLeaveRepositoryContract::class);
    }
}
