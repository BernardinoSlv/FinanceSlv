<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestimentLeaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.leaves.index", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.index", $investiment))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.leaves.index", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.leaves.index")
            ->assertViewHas("investiment", function (Investiment $investiment): bool {
                return $investiment->relationLoaded("leaves");
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.leaves.create", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.create", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_create_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.create", $investiment))
            ->assertForbidden();
    }


    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.leaves.create", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.leaves.create")
            ->assertViewHas("investiment");
    }
}
