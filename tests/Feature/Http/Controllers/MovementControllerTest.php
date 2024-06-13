<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class MovementControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route('movements.index'))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view movements.index
     */
    public function test_index_action(): void
    {
        Quick::factory(2)->has(Movement::factory())->create();

        $user = User::factory()
            ->create();
        $user->factory()->has(
            Quick::factory(2)
                ->has(Movement::factory()->for($user))
        )->create();

        $this->actingAs($user)->get(route('movements.index'))
            ->assertOk()
            ->assertViewIs("movements.index")
            ->assertViewHas("movements", function (LengthAwarePaginator $movements) {
                if (!$movements->first()->relationLoaded("movementable")) {
                    return false;
                } elseif (!$movements->first()->movementable->relationLoaded("identifier")) {
                    return false;
                }
                return $movements->total() === 2;
            });
    }
}
