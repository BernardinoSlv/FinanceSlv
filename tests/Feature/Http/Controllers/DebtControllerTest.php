<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtControllerTest extends TestCase
{
    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("debts.index"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200 e view debt.index
     */
    public function test_index_action(): void
    {
        $user = $this->_user();
        Debt::factory(10)->create();
        Debt::factory(5)->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debts.index"))
            ->assertOk()
            ->assertViewIs("debt.index")
            ->assertViewHas("debts", function (Collection $debts): bool {
                return $debts->count() === 5;
            });
    }
}
