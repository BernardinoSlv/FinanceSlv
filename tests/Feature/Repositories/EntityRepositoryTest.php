<?php

namespace Tests\Feature\Repositories;

use App\Models\Entity;
use App\Repositories\Contracts\EntityRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntityRepositoryTest extends TestCase
{
    public function test_create_method(): void
    {
        $data = Entity::factory()->make()->toArray();
        $user = $this->_user();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas("entities", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): EntityRepositoryContract
    {
        return app(EntityRepositoryContract::class);
    }
}
