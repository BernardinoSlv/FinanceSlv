<?php

namespace Tests\Feature\Repositories;

use App\Models\Identifier;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use Tests\TestCase;

class IdentifierRepositoryTest extends TestCase
{
    public function test_create_method(): void
    {
        $data = Identifier::factory()->make()->toArray();
        $user = $this->_user();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas('identifiers', [
            ...$data,
            'user_id' => $user->id,
        ]);
    }

    protected function _repository(): IdentifierRepositoryContract
    {
        return app(IdentifierRepositoryContract::class);
    }
}
