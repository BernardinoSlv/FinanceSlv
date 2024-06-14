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
        $user  = User::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.create", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_create_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route("debts.payments.create", $debt))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view debts.payments.create
     */
    public function test_create_action(): void
    {
        $user =  User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();

        $this->actingAs($user)->get(route("debts.payments.create", $debt))
            ->assertOk()
            ->assertViewIs("debts.payments.create")
            ->assertViewHas(["debt"]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->post(route("debts.payments.store", $debt))
            ->assertRedirect(route("auth.index"));
    }

    /** deve ter status 404 */
    public function test_store_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("debts.payments.store", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->post(route("debts.payments.store", $debt))
            ->assertFound()
            ->assertSessionHasErrors("amount");
    }

    /**
     * deve ter status 403
     */
    public function test_store_acton_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();
        $data = Movement::factory()->make([
            "amount" => "39,90"
        ])->toArray();

        $this->actingAs($user)->post(route("debts.payments.store", $debt), $data)
            ->assertForbidden();
    }

    /**
     * deve redireiconar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->for($user)->create();
        $data = Movement::factory()->make([
            "amount" => "39,90"
        ])->toArray();

        $this->actingAs($user)->post(route("debts.payments.store", $debt), $data)
            ->assertRedirect(route("debts.payments.create", $debt))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("movements", [
            "movementable_type" => Debt::class,
            "movementable_id" => $debt->id,
            'amount' => 39.90,
            "type" => MovementTypeEnum::OUT->value,
            "user_id" => $user->id
        ]);
    }
}
