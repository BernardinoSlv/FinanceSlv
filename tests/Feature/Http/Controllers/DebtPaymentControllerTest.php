<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class DebtPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->get(route('debts.payments.index', $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.index", $debt))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view debts.payments.index
     */
    public function test_index_action(): void
    {
        Debt::factory(2)->has(Movement::factory(), "movements")->create();

        $user = User::factory()->has(Debt::factory())->create();
        /** @var Debt */
        $debt = $user->debts->first();
        Movement::factory(2)->for($debt, "movementable")->create([
            "type" => MovementTypeEnum::OUT->value
        ]);

        $this->actingAs($user)->get(route("debts.payments.index", $debt))
            ->assertOk()
            ->assertViewIs("debts.payments.index")
            ->assertViewHas(
                "movements",
                function (LengthAwarePaginator $movements): bool {
                    return $movements->total() === 2;
                }
            );
    }
}
