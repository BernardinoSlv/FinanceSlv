<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Debt;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Testing\Fluent\AssertableJson;
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
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('debts.payments.index', 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->get(route('debts.payments.index', $debt))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view debts.payments.index
     */
    public function test_index_action(): void
    {
        Debt::factory(2)->has(Movement::factory(), 'movements')->create();

        $user = User::factory()->has(Debt::factory())->create();
        /** @var Debt */
        $debt = $user->debts->first();
        Movement::factory(2)->for($debt, 'movementable')->create([
            'type' => MovementTypeEnum::OUT->value,
            'amount' => 50,
        ]);

        $this->actingAs($user)->get(route('debts.payments.index', $debt))
            ->assertOk()
            ->assertViewIs('debts.payments.index')
            ->assertViewHas(
                'movements',
                function (LengthAwarePaginator $movements): bool {
                    return $movements->total() === 2;
                }
            )
            ->assertViewHas('debt', function (Debt $debt): bool {
                return floatval($debt->movements_sum_amount) === 100.00;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $debt = Debt::factory()->create();

        $this->post(route('debts.payments.store', $debt))
            ->assertRedirect(route('auth.index'));
    }

    /** deve ter status 404 */
    public function test_store_action_nonexistent_debt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('debts.payments.store', 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create();

        $this->actingAs($user)->post(route('debts.payments.store', $debt))
            ->assertFound()
            ->assertSessionHasErrors('amount');
    }

    /**
     * deve ter status 403
     */
    public function test_store_acton_is_not_owner(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create(["amount" => 1000]);
        $data = Movement::factory()->make([
            'amount' => '39,90',
        ])->toArray();

        $this->actingAs($user)->post(route('debts.payments.store', $debt), $data)
            ->assertForbidden();
    }

    /**
     * deve retornar erro de validação no campo amount
     */
    public function test_store_action_exceded_debt_amount(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->for($user)->create(['amount' => 10]);
        $data = Movement::factory()->make([
            'amount' => '39,90',
        ])->toArray();

        $this->actingAs($user)->postJson(route('debts.payments.store', $debt), $data)
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('message')
                    ->where('errors.amount', ['O valor do pagamento excedeu o total da dívida.'])
            );
    }

    /**
     * deve redireiconar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->for($user)->create();
        $data = Movement::factory()->make([
            'amount' => '39,90',
        ])->toArray();

        $this->actingAs($user)->post(route('debts.payments.store', $debt), $data)
            ->assertCreated()
            ->assertJson([
                'error' => false,
            ]);
        $this->assertDatabaseHas('movements', [
            'movementable_type' => Debt::class,
            'movementable_id' => $debt->id,
            'amount' => 39.90,
            'type' => MovementTypeEnum::OUT->value,
            'user_id' => $user->id,
            'identifier_id' => $debt->identifier_id,
        ]);
    }

    /**
     * deve redirecionar para auth.index
     */
    public function test_edit_action_unauthenticated(): void
    {
        $debt = Debt::factory()->has(
            Movement::factory()->state(['type' => MovementTypeEnum::OUT->value])
        )->create();
        $movement = $debt->movements->first();

        $this->get(route('debts.payments.index', $debt))
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve redirecionar para auth.index
     */
    public function test_update_action_unauthenticated(): void
    {
        $debt = Debt::factory()->has(
            Movement::factory()->state(['type' => MovementTypeEnum::OUT->value])
        )->create();
        $movement = $debt->movements->first();

        $this->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => 0,
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent_debt(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $movement = $user->debts->first()
            ->factory()->has(Movement::factory()->for($user)->state([
                'type' => MovementTypeEnum::OUT->value,
            ]))
            ->create();

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => 0,
            'movement' => $movement,
        ]))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erro de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = Debt::factory()->create();
        $movement = Movement::factory()->for($debt, 'movementable')->for($user)->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertFound()
            ->assertSessionHasErrors([
                'amount',
            ]);
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_movement_is_not_from_debt(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();
        $movement = Movement::factory()->for(Debt::factory()->create(), 'movementable')->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->has(Debt::factory()->state(['amount' => 1000]))->create();
        $debt = $user->debts->first();
        $movement = Movement::factory()->for($debt, 'movementable')->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);
        $data = [
            'amount' => '800,00',
        ];

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]), $data)
            ->assertForbidden();
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner_from_debt(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = Debt::factory()->create(['amount' => 1000]);
        $movement = Movement::factory()->for($debt, 'movementable')->for($user)->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);
        $data = [
            'amount' => '800,00',
        ];

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]), $data)
            ->assertForbidden();
    }

    /**
     * deve retornar erro de validação no campo amount
     */
    public function test_update_action_exceded_debt_amount(): void
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->for($user)
            ->has(Movement::factory()->state(['amount' => 9.50]), 'movements')
            ->create(['amount' => 10]);
        $movement = $debt->movements->first();
        $data = Movement::factory()->make([
            'amount' => '39,90',
        ])->toArray();

        $this->actingAs($user)->putJson(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]), $data)
            ->assertUnprocessable()
            ->assertJson(
                fn (AssertableJson $json) => $json->has('message')
                    ->where('errors.amount', ['O valor do pagamento excedeu o total da dívida.'])
            );
    }

    /**
     * deve ter status 200 e view debts.payments.update
     */
    public function test_update_action(): void
    {
        $user = User::factory()->has(Debt::factory()->state(['amount' => 10000]))->create();
        $debt = $user->debts->first();
        $movement = Movement::factory()->for($debt, 'movementable')->for($user)->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);
        $data = [
            'amount' => '800,00',
        ];

        $this->actingAs($user)->put(route('debts.payments.update', [
            'debt' => $debt,
            'movement' => $movement,
        ]), $data)
            ->assertOk()
            ->assertJson([
                'error' => false,
            ]);
        $this->assertDatabaseHas('movements', [
            'id' => $movement->id,
            'movementable_type' => Debt::class,
            'movementable_id' => $debt->id,
            'amount' => 800.00,
            'identifier_id' => $debt->identifier_id,
        ]);
    }

    /**
     * deve redirecionar para auth.index
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $debt = Debt::factory()->has(
            Movement::factory()->state(['type' => MovementTypeEnum::OUT->value])
        )->create();
        $movement = $debt->movements->first();

        $this->delete(route('debts.payments.destroy', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();

        $this->actingAs($user)->delete(route('debts.payments.destroy', [
            'debt' => $debt,
            'movement' => 0,
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent_debt(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $movement = $user->debts->first()
            ->factory()->has(Movement::factory()->for($user)->state([
                'type' => MovementTypeEnum::OUT->value,
            ]))
            ->create();

        $this->actingAs($user)->delete(route('debts.payments.destroy', [
            'debt' => 0,
            'movement' => $movement,
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_movement_is_not_from_debt(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();
        $movement = Movement::factory()->for(Debt::factory()->create(), 'movementable')->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);

        $this->actingAs($user)->delete(route('debts.payments.destroy', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();
        $movement = Movement::factory()->for($debt, 'movementable')->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);

        $this->actingAs($user)->delete(route('debts.payments.destroy', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertForbidden();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner_from_debt(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = Debt::factory()->create();
        $movement = Movement::factory()->for($debt, 'movementable')->for($user)->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);

        $this->actingAs($user)->delete(route('debts.payments.destroy', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view debts.payments.destroy
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->has(Debt::factory())->create();
        $debt = $user->debts->first();
        $movement = Movement::factory()->for($debt, 'movementable')->for($user)->create([
            'type' => MovementTypeEnum::OUT->value,
        ]);

        $this->actingAs($user)->delete(route('debts.payments.destroy', [
            'debt' => $debt,
            'movement' => $movement,
        ]))
            ->assertRedirect(route('debts.payments.index', $debt))
            ->assertSessionHas('alert_type', 'success');
        $this->assertSoftDeleted($movement);
    }
}
