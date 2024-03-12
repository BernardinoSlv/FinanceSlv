<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $this->get(route("debts.payments.index", $debt))
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
     * deve ter status 404
     */
    public function test_index_action_with_debt_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.index", $debt))
            ->assertNotFound();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("debts.payments.index", $debt))
            ->assertOk()
            ->assertViewIs("debts.payments.index")
            ->assertViewHas("debt", function (Debt $actualDebt) use ($debt): bool {
                if ($actualDebt->id !== $debt->id) {
                    return false;
                } else if (!$actualDebt->relationLoaded("leaves")) {
                    return false;
                }
                return true;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->get(route("debts.payments.create", $debt))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.create", $user))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_with_debt_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.create", $debt))
            ->assertNotFound();
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("debts.payments.create", $debt))
            ->assertOk()
            ->assertViewIs("debts.payments.create")
            ->assertViewHas("debt");
    }
}
