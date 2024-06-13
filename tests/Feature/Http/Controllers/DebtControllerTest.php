<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class DebtControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("debts.index"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve te status 200 e view debts.index
     */
    public function test_index_action(): void
    {
        Debt::factory(2)->create();

        $user = User::factory()->has(Debt::factory(2))->create();

        $this->actingAs($user)->get(route('debts.index'))
            ->assertOk()
            ->assertViewIs("debts.index")
            ->assertViewHas("debts", function (LengthAwarePaginator $debts): bool {
                if (!$debts->first()->loadCount('movements')) return false;
                return $debts->total() === 2;
            });
    }
}
