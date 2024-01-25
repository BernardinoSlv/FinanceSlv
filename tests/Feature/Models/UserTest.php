<?php

namespace Tests\Feature\Models;

use App\Models\Entity;
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
    public function test_entities_method_without_entity(): void
    {
        Entity::factory(10)->create();
        $user = User::factory()->create();

        $this->assertCount(0, $user->entities);
    }

    /**
     * deve retornar uma coleção com 4 items
     */
    public function test_entities_method(): void
    {
        Entity::factory(10)->create();
        $user = User::factory()->create();
        Entity::factory(4)->create([
            "user_id" => $user
        ]);

        $this->assertCount(4, $user->entities);
    }
}
