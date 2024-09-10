<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Identifier;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Tests\TestCase;

class QuickControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route('quicks.index'))
            ->assertRedirect(route('auth.index'));
    }

    /**
     * deve ter status 200 e view quicks.index
     */
    public function test_index_action(): void
    {
        Quick::factory(2)->create();

        $user = User::factory()->create();
        Quick::factory(2)->create(['user_id' => $user]);

        $this->actingAs($user)->get(route('quicks.index'))
            ->assertOk()
            ->assertViewIs('quicks.index')
            ->assertViewHas('quicks', function (LengthAwarePaginator $quicks): bool {
                return $quicks->total() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route('quicks.create'))
            ->assertRedirect(route('auth.index'));
    }

    /**
     * deve ter status 200 e view quicks.index
     */
    public function test_create_action(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->has(Identifier::factory(2))->create();

        $this->actingAs($user)->get(route('quicks.create'))
            ->assertOk()
            ->assertViewIs('quicks.create')
            ->assertViewHas('identifiers', function (Collection $identifiers): bool {
                return $identifiers->count() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unathenticated(): void
    {
        $this->post(route('quicks.store'))
            ->assertRedirect(route('auth.index'));
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('quicks.store'))
            ->assertFound()
            ->assertSessionHasErrors([
                'title',
                'type',
                'amount',
            ])
            ->assertSessionDoesntHaveErrors(['description', 'identifier_id']);
    }

    /**
     * deve redirecionar com erro de validação no campo amount
     */
    public function test_store_action_invalid_amount_format(): void
    {
        $user = User::factory()->create();
        $data = Quick::factory()->make([
            'identifier_id' => Identifier::factory()->create(['user_id' => $user]),
            'type' => MovementTypeEnum::IN->value,
            'amount' => 500.00,
        ])->toArray();

        $this->actingAs($user)->post(route('quicks.store'), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                'amount',
            ])
            ->assertSessionDoesntHaveErrors([
                'description',
                'title',
                'type',
                'identifier_id',
            ]);
    }

    /**
     * deve redirecionar com erro de validação no campo identifier_id
     */
    public function test_store_action_using_identifier_from_other_user(): void
    {
        $user = User::factory()->create();
        $data = Quick::factory()->make([
            'identifier_id' => Identifier::factory()->create(),
            'type' => MovementTypeEnum::IN->value,
            'amount' => '500,00',
        ])->toArray();

        $this->actingAs($user)->post(route('quicks.store'), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                'identifier_id',
            ])
            ->assertSessionDoesntHaveErrors([
                'description',
                'title',
                'type',
                'amount',
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $data = Quick::factory()->make([
            'identifier_id' => Identifier::factory()->create(['user_id' => $user]),
            'type' => MovementTypeEnum::IN->value,
            'amount' => '500,00',
        ])->toArray();

        $this->actingAs($user)->post(route('quicks.store'), $data)
            ->assertRedirect(route('quicks.index'))
            ->assertSessionHas('alert_type', 'success');

        $this->assertDatabaseHas('quicks', [
            ...Arr::except($data, ['type', 'amount']),
            'user_id' => $user->id,
        ]);
        $quick = Quick::query()->where([
            ...Arr::except($data, ['type', 'amount']),
            'user_id' => $user->id,
        ])->first();
        $this->assertDatabaseHas('movements', [
            ...Arr::except($data, ['description', 'title']),
            'movementable_type' => Quick::class,
            'movementable_id' => $quick->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $quick = Quick::factory()->create([]);

        $this->get(route('quicks.edit', $quick))
            ->assertRedirect(route('auth.index'));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('quicks.edit', 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_trashed(): void
    {
        $user = User::factory()->create();
        $quick = Quick::factory()->trashed()->create(['user_id' => $user]);

        $this->actingAs($user)->get(route('quicks.edit', $quick))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $quick = Quick::factory()->create();

        $this->actingAs($user)->get(route('quicks.edit', $quick))
            ->assertForbidden();
    }

    /**
     * deve ter status 200 e view quicks.edit
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $quick = Quick::factory()->create(['user_id' => $user]);

        $this->actingAs($user)->get(route('quicks.edit', $quick))
            ->assertOk()
            ->assertViewIs('quicks.edit')
            ->assertViewHas('identifiers')
            ->assertViewHas('quick', fn (Quick $actualQuick): bool => $quick->id === $actualQuick->id);
    }

    /**deve redirecionar para login */
    public function test_update_action_unauthenticated(): void
    {
        $quick = Quick::factory()->create();

        $this->put(route('quicks.update', $quick))
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistente(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('quicks.update', 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com errors de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->has(Quick::factory())->create();
        $quick = $user->quicks->first();

        $this->actingAs($user)->put(route('quicks.update', $quick))
            ->assertFound()
            ->assertSessionHasErrors([
                'title',
                'type',
                'amount',
            ]);
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->has(Identifier::factory())->create();
        $quick = Quick::factory()->create();
        $data = Quick::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'type' => MovementTypeEnum::IN->value,
        ])->toArray();

        $this->actingAs($user)->put(route('quicks.update', $quick), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()
            ->has(Quick::factory()
                ->has(Movement::factory()))
            ->has(Identifier::factory())
            ->create();
        $quick = $user->quicks->first();
        $data = Quick::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'type' => MovementTypeEnum::IN->value,
        ])->toArray();

        $this->actingAs($user)->put(route('quicks.update', $quick), $data)
            ->assertRedirectToRoute('quicks.edit', $quick)
            ->assertSessionHas('alert_type', 'success');
        $this->assertDatabaseHas('quicks', [
            'id' => $quick->id,
            ...Arr::only($data, ['identifier_id', 'title', 'description']),
        ]);
        $this->assertDatabaseHas('movements', [
            'id' => $quick->movement->id,
            ...Arr::only($data, [
                'type',
                'identifier_id',
            ]),
            'amount' => 500.00,
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $quick = Quick::factory()->create();

        $this->delete(route('quicks.destroy', $quick))
            ->assertRedirectToRoute('auth.index');
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route('quicks.destroy', 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $quick = Quick::factory()->create();

        $this->actingAs($user)->delete(route('quicks.destroy', $quick))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()
            ->has(
                Quick::factory()
                    ->has(Movement::factory())
            )
            ->create();
        $quick = $user->quicks->first();
        $movement = $quick->movement;

        $this->actingAs($user)->delete(route('quicks.destroy', $quick))
            ->assertRedirect(route('quicks.index'))
            ->assertSessionHas('alert_type', 'success');
        $this->assertSoftDeleted($quick);
        $this->assertSoftDeleted($movement);
    }
}
