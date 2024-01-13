<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestimentControllerTest extends TestCase
{
    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("investiments.index"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view investiment.index
     */
    public function test_index_action(): void
    {
        Investiment::factory(20)->create();
        $user = $this->_user();
        Investiment::factory(10)->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("investiments.index"))
            ->assertOk()
            ->assertViewIs("investiment.index")
            ->assertViewHas("investiments", function (Collection $investiments): bool {
                return $investiments->count() === 10;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("investiments.create"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view investiment.create
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("investiments.create"))
            ->assertOk()
            ->assertViewIs("investiment.create");
    }
}
