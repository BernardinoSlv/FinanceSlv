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
    public function test_create_method(): void
    {
        $user = User::factory()->create();
        $data = Movement::factory()->make([
            "movementable_type" => Entry::class,
            "movementable_id" => Entry::factory([
                "entryable_type" => Debtor::class,
                "entryable_id" => Debtor::factory()->create()
            ])->create()
        ])->toArray();

        $this->_repository()->create($user->id, $data);

        $this->assertDatabaseHas("movements", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): MovementRepositoryContract
    {
        return app(MovementRepositoryContract::class);
    }
}
