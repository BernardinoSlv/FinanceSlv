<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class QuickControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("quicks.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view quicks.index
     */
    public function test_index_action(): void
    {
        Quick::factory(2)->create();

        $user = User::factory()->create();
        Quick::factory(2)->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("quicks.index"))
            ->assertOk()
            ->assertViewIs("quicks.index")
            ->assertViewHas("paginator", function (LengthAwarePaginator $paginator): bool {
                return $paginator->total() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("quicks.create"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view quicks.index
     */
    public function test_create_action(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->has(Identifier::factory(2))->create();

        $this->actingAs($user)->get(route("quicks.create"))
            ->assertOk()
            ->assertViewIs("quicks.create")
            ->assertViewHas("identifiers", function (Collection $identifiers): bool {
                return $identifiers->count() === 2;
            });
    }
}
