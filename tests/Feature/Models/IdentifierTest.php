<?php

namespace Tests\Feature\Models;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdentifierTest extends TestCase
{
    use RefreshDatabase;
    /**
     * deve retornar o User
     */
    public function test_user_method(): void
    {
        User::factory(2)->create();

        $user = User::factory()->create();
        $identifier = Identifier::factory()->create(['user_id' => $user]);

        $this->assertEquals($user->id, $identifier->user->id);
    }

    /**
     * deve retornar o User mesmo deletado
     */
    public function test_user_method_trashed_user(): void
    {
        User::factory(2)->create();
        User::factory(2)->trashed()->create();

        $user = User::factory()->trashed()->create();
        $identifier = Identifier::factory()->create(['user_id' => $user]);

        $this->assertEquals($user->id, $identifier->user->id);
    }
}
