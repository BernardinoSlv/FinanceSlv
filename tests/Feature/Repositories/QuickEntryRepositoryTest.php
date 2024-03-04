<?php

namespace Tests\Feature\Repositories;

use App\Models\QuickEntry;
use App\Models\User;
use App\Repositories\Contracts\QuickEntryRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuickEntryRepositoryTest extends TestCase
{
    /**
     * deve criar o registro
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $data = QuickEntry::factory()->make()->toArray();

        $this->assertInstanceOf(
            QuickEntry::class,
            $this->_repository()->create($user->id, $data)
        );
        $this->assertDatabaseHas("quick_entries", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    protected function _repository(): QuickEntryRepositoryContract
    {
        return app(QuickEntryRepositoryContract::class);
    }
}
