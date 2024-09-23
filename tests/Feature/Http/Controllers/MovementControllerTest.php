<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\Expense;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MovementControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route('movements.index'))->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 200 e view movements.index
     */
    public function test_index_action(): void
    {
        Quick::factory(2)->has(Movement::factory())->create();

        $user = User::factory()
            ->create();
        $user->factory()->has(
            Quick::factory(2)
                ->has(Movement::factory()->for($user))
        )->create();

        $this->actingAs($user)->get(route('movements.index'))
            ->assertOk()
            ->assertViewIs('movements.index')
            ->assertViewHas('movements', function (LengthAwarePaginator $movements) {
                if (! $movements->first()->relationLoaded('movementable')) {
                    return false;
                } elseif (! $movements->first()->relationLoaded('identifier')) {
                    return false;
                }

                return $movements->total() === 2;
            });
    }

    /** deve ter status 422 */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()
            ->create();
        Expense::factory()
            ->has(Movement::factory(null, [
                "closed_date" => null,
                "user_id" => $user
            ]))
            ->create(["user_id" => $user]);
        $movement = $user->expenses->first()->movements->first();

        $this->actingAs($user)->putJson(route("movements.update", $movement))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(["fees_amount", "status"]);
    }

    /** deve ter status 400 */
    public function test_update_action_already_closed(): void
    {
        $user = User::factory()
            ->create();
        Expense::factory()
            ->has(Movement::factory(null, [
                "user_id" => $user
            ]))
            ->create(["user_id" => $user]);
        $movement = $user->expenses->first()->movements->first();
        $data = [
            "fees_amount" => "20,00"
        ];

        $this->actingAs($user)->putJson(route("movements.update", $movement), $data)
            ->assertBadRequest()
            ->assertJson([
                "message" => "Não é permitido atualizar movimentações já fechadas."
            ]);
    }

    /** deve ter status 403 */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()
            ->create();
        Expense::factory()
            ->has(Movement::factory(null))
            ->create(["user_id" => $user]);
        $movement = $user->expenses->first()->movements->first();
        $data = [
            "fees_amount" => "20,00"
        ];

        $this->actingAs($user)->putJson(route("movements.update", $movement), $data)
            ->assertForbidden();
    }

    /** deve ter status 200 */
    public function test_update_action(): void
    {
        $this->travelBack();

        $this->freezeTime(function (Carbon $time): void {
            $user = User::factory()
                ->create();
            Expense::factory()
                ->has(Movement::factory(null, [
                    "closed_date" => null,
                    "user_id" => $user
                ]))
                ->create(["user_id" => $user]);
            $movement = $user->expenses->first()->movements->first();
            $data = [
                "fees_amount" => "20,00",
                "status" => 1
            ];

            $this->actingAs($user)->putJson(route("movements.update", $movement), $data)
                ->assertOk()
                ->assertJson(
                    fn(AssertableJson $json) => $json->has("message")
                );
            $this->assertDatabaseHas("movements", [
                "id" => $movement->id,
                "fees_amount" => 20.00,
                "closed_date" => now()->format("Y-m-d")
            ]);
        });
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $movement = Movement::factory()->for(Quick::factory(), 'movementable')->create();

        $this->delete(route('movements.destroy', $movement))
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route('movements.destroy', 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()->for(Quick::factory(), 'movementable')->create();

        $this->actingAs($user)->delete(route('movements.destroy', $movement))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()
            ->for($user)
            ->for(Debt::factory(), 'movementable')
            ->create();

        $this->actingAs($user)->delete(route('movements.destroy', $movement))
            ->assertRedirect(route('movements.index'))
            ->assertSessionHas('alert_type', 'success');
        $this->assertSoftDeleted($movement);
    }

    /**
     * deve redirecionar e remover o quick
     */
    public function test_destroy_action_a_quick(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()
            ->for($user)
            ->for(Quick::factory(), 'movementable')
            ->create();

        $this->actingAs($user)->delete(route('movements.destroy', $movement))
            ->assertRedirect(route('movements.index'))
            ->assertSessionHas('alert_type', 'success');
        $this->assertSoftDeleted($movement);
        $this->assertSoftDeleted($movement->movementable);
    }
}
