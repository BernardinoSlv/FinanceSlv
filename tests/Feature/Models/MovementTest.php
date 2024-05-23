<?php

namespace Tests\Feature\Models;

use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MovementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar o User
     */
    public function test_user_method(): void
    {
        User::factory(2)->create();

        $user = User::factory()->create();
        $movement = Movement::factory()->create([
            "user_id" => $user,
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);

        $this->assertEquals($user->id, $movement->user->id);
    }

    /**
     * deve retornar o User mesmo deletado
     */
    public function test_user_method_trashed_user(): void
    {
        User::factory(2)->create();
        User::factory(2)->trashed()->create();

        $user = User::factory()->trashed()->create();
        $movement = Movement::factory()->create([
            "user_id" => $user,
            "movementable_type" => Quick::class,
            "movementable_id" => Quick::factory()->create()
        ]);

        $this->assertEquals($user->id, $movement->user->id);
    }

    /**
     * deve retornar o model Quick
     */
    public function test_movementable_method_using_quick(): void
    {
        Quick::factory(2)->create();

        $quick = Quick::factory()->create();
        $movement = Movement::factory()->create([
            "movementable_type" => Quick::class,
            "movementable_id" => $quick
        ]);

        $this->assertInstanceOf(Quick::class, $movement->movementable);
        $this->assertEquals($quick->id, $movement->movementable->id);
    }

    /**
     * deve retornar o model Quick mesmo deletado
     */
    public function test_movementable_method_using_trashed_quick(): void
    {
        Quick::factory(2)->create();
        Quick::factory(2)->trashed()->create();

        $quick = Quick::factory()->trashed()->create();
        $movement = Movement::factory()->create([
            "movementable_type" => Quick::class,
            "movementable_id" => $quick
        ]);

        $this->assertInstanceOf(Quick::class, $movement->movementable);
        $this->assertEquals($quick->id, $movement->movementable->id);
    }
}
