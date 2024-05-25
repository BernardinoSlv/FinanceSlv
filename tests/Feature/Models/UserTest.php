<?php

namespace Tests\Feature\Models;

use App\Models\Identifier;
use App\Models\Movement;
use App\Models\Quick;
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

    /**
     * deve retornar um coleção vazia
     */
    public function test_movements_method_without_movement(): void
    {
        Movement::factory(2)->create([
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);

        $user = User::factory()->create();

        $this->assertCount(0, $user->movements);
    }

    /**
     * deve retornar 2 Movement
     *
     * @return void
     */
    public function test_movements_method(): void
    {
        Movement::factory(2)->create([
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);

        $user = User::factory()->create();
        Movement::factory(2)->create([
            "user_id" => $user,
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);

        $this->assertCount(2, $user->movements);
    }
}
