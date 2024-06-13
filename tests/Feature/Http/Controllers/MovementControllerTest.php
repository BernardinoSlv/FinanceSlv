<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Tests\TestCase;

class MovementControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route('movements.index'))->assertRedirectToRoute("auth.index");
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
            ->assertViewIs("movements.index")
            ->assertViewHas("movements", function (LengthAwarePaginator $movements) {
                if (!$movements->first()->relationLoaded("movementable")) {
                    return false;
                } elseif (!$movements->first()->movementable->relationLoaded("identifier")) {
                    return false;
                }
                return $movements->total() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $movement = Movement::factory()->for(Quick::factory(), "movementable")->create();

        $this->get(route("movements.edit", $movement))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $user =  User::factory()->create();

        $this->actingAs($user)->get(route("movements.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()->for(Quick::factory(), "movementable")->create();

        $this->actingAs($user)->get(route('movements.edit', $movement))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view movements.index
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()->for($user)
            ->for(Quick::factory(), "movementable")->create();

        $this->actingAs($user)->get(route("movements.edit", $movement))
            ->assertOk()
            ->assertViewIs("movements.edit")
            ->assertViewHas("movement");
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $movement = Movement::factory()->for(Quick::factory(), "movementable")->create();

        $this->put(route("movements.update", $movement))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route("movements.update", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com errors de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()
            ->for($user)
            ->for(Quick::factory(), "movementable")
            ->create();

        $this->actingAs($user)->put(route("movements.update", $movement))
            ->assertFound()
            ->assertSessionHasErrors([
                "type",
                "amount"
            ]);
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()
            ->for(Quick::factory(), "movementable")
            ->create();
        $data = Movement::factory()->make(["amount" => "100,00"])->toArray();

        $this->actingAs($user)->put(route("movements.update", $movement), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()
            ->for($user)
            ->for(Quick::factory(), "movementable")
            ->state(["type" => MovementTypeEnum::IN->value])
            ->create();
        $data = Movement::factory()
            ->make([
                "amount" => "100,00",
                "type" => MovementTypeEnum::OUT->value
            ])->toArray();

        $this->actingAs($user)->put(route("movements.update", $movement), $data)
            ->assertRedirect(route("movements.edit", $movement))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("movements", [
            "type" => MovementTypeEnum::OUT->value,
            "id" => $movement->id,
            "amount" => 100.00
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $movement = Movement::factory()->for(Quick::factory(), "movementable")->create();

        $this->delete(route("movements.destroy", $movement))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route("movements.destroy", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $movement = Movement::factory()->for(Quick::factory(), "movementable")->create();

        $this->actingAs($user)->delete(route("movements.destroy", $movement))
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
            ->for(Quick::factory(), "movementable")
            ->create();

        $this->actingAs($user)->delete(route('movements.destroy', $movement))
            ->assertRedirect(route("movements.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertSoftDeleted($movement);
    }
}
