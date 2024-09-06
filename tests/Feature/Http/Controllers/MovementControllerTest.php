<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Debt;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
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
