<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Entry;
use App\Models\Investiment;
use App\Models\Movement;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class InvestimentEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.entries.index", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("investiments.entries.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();

        $this->actingAs($user)->get(route("investiments.entries.index", $investiment))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.entries.index", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.entries.index")
            ->assertViewHas("investiment", function (Investiment $investiment): bool {
                return $investiment->relationLoaded("entries");
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.entries.create", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("investiments.entries.create", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_create_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();

        $this->actingAs($user)->get(route("investiments.entries.create", $investiment))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.entries.create", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.entries.create")
            ->assertViewHas("investiment", $investiment);
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->post(route("investiments.entries.store", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /** deve ter status 404 */
    public function test_store_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("investiments.entries.store", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->post(route("investiments.entries.store", $investiment))
            ->assertFound()
            ->assertSessionHasErrors(["amount"]);
    }

    /**
     * deve ter status 403
     */
    public function test_store_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();
        $data = [
            "amount" => "1.800,00"
        ];

        $this->actingAs($user)->post(route("investiments.entries.store", $investiment), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);
        $data = [
            "amount" => "1.800,00"
        ];

        $this->instance(
            EntryRepositoryContract::class,
            Mockery::mock(app(EntryRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    "entryable_type" => Investiment::class,
                    "entryable_id" => $investiment->id,
                    "amount" => 1800.00
                ])
                ->passthru()
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["movementable_type"] !== Entry::class) {
                        return false;
                    } else if (!is_int($attributes["movementable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("investiments.entries.store", $investiment), $data)
            ->assertFound()
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();
        $entry = Entry::factory()->create([
            "entryable_type" => Investiment::class,
            "entryable_id" => $investiment
        ]);

        $this->get(route("investiments.entries.edit", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent_investiment(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.entries.edit", [
            "investiment" => 0,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.entries.edit", [
            "investiment" => $investiment,
            "entry" => 0
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_of_the_investiment(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);
        $entry = Entry::factory()->create([
            "user_id" => $user,
        ]);

        $this->actingAs($user)->get(route("investiments.entries.edit", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);
        $entry = Entry::factory()->create([
            "entryable_type" => Investiment::class,
            "entryable_id" => $investiment
        ]);

        $this->actingAs($user)->get(route("investiments.entries.edit", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);
        $entry = Entry::factory()->create([
            "user_id" => $user,
            "entryable_type" => Investiment::class,
            "entryable_id" => $investiment
        ]);

        $this->actingAs($user)->get(route("investiments.entries.edit", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertOk()
            ->assertViewIs("investiments.entries.edit")
            ->assertViewHas(["investiment", "entry"]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->hasEntries(1)->create();

        $this->put(route("investiments.entries.update", [
            "investiment" => $investiment,
            "entry" => $investiment->entries->first()
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent_investiment(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->put(route("investiments.entries.update", [
            "investiment" => 0,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->put(route("investiments.entries.update", [
            "investiment" => $investiment,
            "entry" => 0
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_of_the_investiment(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);
        $entry = Entry::factory()->create([
            "user_id" => $user,
            "entryable_type" => Investiment::class,
            "entryable_id" => Investiment::factory()->create()
        ]);

        $this->actingAs($user)->put(route("investiments.entries.update", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasEntries(1, ["user_id" => $user])
            ->create(["user_id" => $user]);
        $entry = $investiment->entries->first();


        $this->actingAs($user)->put(route("investiments.entries.update", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertFound()
            ->assertSessionHasErrors("amount");
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()
            ->hasEntries(1, ["user_id" => User::factory()->create()])
            ->create(["user_id" => $user]);
        $entry = $investiment->entries->first();
        $data = [
            "amount" => "8.000,00"
        ];


        $this->actingAs($user)->put(route("investiments.entries.update", [
            "investiment" => $investiment,
            "entry" => $entry
        ]), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasEntries(1, ["user_id" => $user])
            ->create(["user_id" => $user]);
        $entry = $investiment->entries->first();
        $data = [
            "amount" => "8.000,00"
        ];

        $this->instance(
            EntryRepositoryContract::class,
            Mockery::mock(EntryRepositoryContract::class)
                ->shouldReceive("update")
                ->with($entry->id, [
                    "amount" => 8000.00
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("investiments.entries.update", [
            "investiment" => $investiment,
            "entry" => $entry
        ]), $data)
            ->assertRedirect(route("investiments.entries.edit", [
                "investiment" => $investiment,
                "entry" => $entry
            ]))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->hasEntries(1)->create();
        $entry = $investiment->entries->first();

        $this->delete(route("investiments.entries.destroy", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent_investiment(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->delete(route("investiments.entries.destroy", [
            "investiment" => 0,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->delete(route("investiments.entries.destroy", [
            "investiment" => $investiment,
            "entry" => 0
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_is_not_of_the_investiment(): void
    {
        $user = User::factory()->create();
        $investiment =  Investiment::factory()->create(["user_id" => $user]);
        $entry = Entry::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->delete(route("investiments.entries.destroy", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasEntries(1)->create();
        $entry = $investiment->entries->first();

        $this->actingAs($user)->delete(route("investiments.entries.destroy", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasEntries(1, ["user_id" => $user])->create([
            "user_id" => $user
        ]);
        $entry = $investiment->entries->first();
        $movement = Movement::factory()->create([
            "movementable_type" => Entry::class,
            "movementable_id" => $entry
        ]);

        $this->instance(
            EntryRepositoryContract::class,
            Mockery::mock(EntryRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($entry->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($movement->id)
                ->once()
                ->getMock()
        );


        $this->actingAs($user)->delete(route("investiments.entries.destroy", [
            "investiment" => $investiment,
            "entry" => $entry
        ]))
            ->assertRedirect(route("investiments.entries.index", $investiment))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }
}
