<?php

namespace Tests\Feature\Models;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar uma coleção vazia
     */
    public function test_identifiers_method_without_identifier(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->create();

        $this->assertCount(0, $user->identifiers);
    }

    /**
     * deve retornar uma coleção com 2 Identifier
     */
    public function test_identifiers_method(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->create();
        Identifier::factory(2)->create(["user_id" => $user]);

        $this->assertCount(2, $user->identifiers);
    }
}
