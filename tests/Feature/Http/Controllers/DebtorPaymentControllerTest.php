<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\Debtor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class DebtorPaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->get(route("debtors.payments.index", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent_debtor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.index", $debtor))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.index", $debtor))
            ->assertOk()
            ->assertViewIs("debtors.payments.index")
            ->assertViewHas("debtor", function (Debtor $debtor): bool {
                return $debtor->isRelation("entries");
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $debtor = Debtor::factory()->create();

        $this->get(route("debtors.payments.create", $debtor))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent_debtor(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.create", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_create_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create();

        $this->actingAs($user)->get(route("debtors.payments.create", $debtor))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $debtor = Debtor::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("debtors.payments.create", $debtor))
            ->assertOk()
            ->assertViewIs("debtors.payments.create")
            ->assertViewHas("debtor", function (Debtor $actualDebtor) use ($debtor): bool {
                return $debtor->id === $actualDebtor->id;
            });
    }
}
