<?php

namespace Tests\Feature\Repositories;

use App\Models\Need;
use App\Models\User;
use App\Repositories\Contracts\NeedRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NeedRepositoryTest extends TestCase
{
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $data = Need::factory()->make()->toArray();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas("needs", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): NeedRepositoryContract
    {
        return app(NeedRepositoryContract::class);
    }
}
