<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\Entry;
use App\Models\QuickEntry;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\Contracts\QuickEntryRepositoryContract;
use App\Repositories\QuickEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Mockery;
use Tests\TestCase;

class QuickEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("quick-entries.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view entries.index
     *
     * @return void
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        QuickEntry::factory(10)->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("quick-entries.index"))
            ->assertOk()
            ->assertViewIs("quick-entries.index")
            ->assertViewHas("entries");
    }

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("quick-entries.create"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view entries.create
     *
     * @return void
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("quick-entries.create"))
            ->assertOk()
            ->assertViewIs("quick-entries.create")
            ->assertViewHas("identifiers");
    }

    /**
     * deve redirecionar para o página de login
     *
     * @return void
     */
    public function test_store_action_unauthenticated(): void
    {
        $data = Entry::factory()->make()->toArray();

        $this->post(route("quick-entries.store"), $data)
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve redirecionar com erros de validação
     *
     * @return void
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("quick-entries.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "identifier_id",
                "title",
                "amount"
            ]);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo identifier
     */
    public function test_store_action_identifier_of_other_user(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $data = QuickEntry::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("quick-entries.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",
            ])
            ->assertSessionDoesntHaveErrors([
                "title",
                "amount"
            ]);
    }

    /**
     * deve criar e redirecionar com mensagem de sucesso
     *
     * @return void
     */
    public function test_store_action(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $data = QuickEntry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => '125,55',
            "description" => "Apenas um teste"
        ])->toArray();

        $this->instance(
            QuickEntryRepositoryContract::class,
            Mockery::mock(QuickEntryRepository::class)
                ->shouldReceive("create")
                ->with($user->id, [
                    ...Arr::only($data, ["identifier_id", "title", "description"]),
                    "amount" => 125.55
                ])
                ->passthru()
                ->once()
                ->getMock()
        );

        $this->instance(
            EntryRepositoryContract::class,
            Mockery::mock(app(EntryRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    "entryable_type" => QuickEntry::class,
                    "entryable_id" => 1
                ])
                ->passthru()
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, [
                    "movementable_type" => Entry::class,
                    "movementable_id" => 1
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("quick-entries.store"), $data)
            ->assertRedirect(route("quick-entries.index"))
            ->assertSessionHas([
                "alert_type" => "success",
                "alert_text" => "Entrada criada com sucesso."
            ]);

        Mockery::close();
    }

    /**
     * deve criar e redirecionar com mensagem de sucesso
     *
     * @return void
     */
    public function test_store_action_with_amount_real_formatting(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $data = QuickEntry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "192.125,25"
        ])->toArray();

        $this->actingAs($user)->post(route("quick-entries.store"), $data)
            ->assertRedirect(route("quick-entries.index"))
            ->assertSessionHas([
                "alert_type" => "success",
                "alert_text" => "Entrada criada com sucesso."
            ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $quickEntry = QuickEntry::factory()->create();

        $this->get(route("quick-entries.edit", $quickEntry))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->get(route("quick-entries.edit", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $quickEntry = QuickEntry::factory()->create();

        $this->actingAs($user)->get(route("quick-entries.edit", $quickEntry))
            ->assertStatus(404);
    }

    /**
     * deve ter status 200 e view entries.edit
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $quickEntry = QuickEntry::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("quick-entries.edit", $quickEntry))
            ->assertOk()
            ->assertViewIs("quick-entries.edit")
            ->assertViewHas([
                "quickEntry",
                "identifiers"
            ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_update_action_unauthenticated(): void
    {
        $quickEntry = QuickEntry::factory()->create();
        $data = QuickEntry::factory()->make()->toArray();

        $this->put(route("quick-entries.update", $quickEntry), $data)
            ->assertRedirect(route("auth.index"));
    }

    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->create();
        $data = QuickEntry::factory()->make()->toArray();

        $this->actingAs($user)->put(route("quick-entries.update", 0), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com erros de validação -
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $quickEntry = QuickEntry::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->put(route("quick-entries.update", $quickEntry))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "title",
                "amount"
            ]);
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $quickEntry = QuickEntry::factory()->create();
        $data = QuickEntry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("quick-entries.update", $quickEntry), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo identifier_id
     */
    public function test_update_action_with_identifier_of_other_user(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $quickEntry = QuickEntry::factory()->create();
        $data = QuickEntry::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("quick-entries.update", $quickEntry), $data)
            ->assertFound()
            ->assertSessionHasErrors("identifier_id")
            ->assertSessionDoesntHaveErrors([
                "title",
                "amount",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $quickEntry = QuickEntry::factory()->create([
            "user_id" => $user
        ]);
        $quickEntry = QuickEntry::factory()->create([
            "user_id" => $user->id
        ]);
        $data = QuickEntry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "5.000,00"
        ])->toArray();

        $this->instance(
            QuickEntryRepositoryContract::class,
            Mockery::mock(QuickEntryRepositoryContract::class)
                ->shouldReceive("update")
                ->with($quickEntry->id, [
                    ...Arr::only($data, ["identifier_id", "title", "description"]),
                    "amount" => 5000.00
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("quick-entries.update", $quickEntry), $data)
            ->assertRedirect(route("quick-entries.edit", $quickEntry))
            ->assertSessionHas([
                "alert_type" => "success"
            ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_destroy_unauthenticated(): void
    {
        $quickEntry = QuickEntry::factory()->create();

        $this->delete(route("quick-entries.destroy", $quickEntry))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route("quick-entries.destroy", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_is_not_owner(): void
    {
        $user = User::factory()->create();
        $quickEntry = QuickEntry::factory()->create();

        $this->actingAs($user)->delete(route("quick-entries.destroy", $quickEntry))
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy(): void
    {
        $user = User::factory()->create();
        $quickEntry = QuickEntry::factory()->hasEntry()->create([
            "user_id" => $user->id
        ]);

        $this->instance(
            QuickEntryRepositoryContract::class,
            Mockery::mock(QuickEntryRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($quickEntry->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            EntryRepositoryContract::class,
            Mockery::mock(app(EntryRepositoryContract::class))
                ->shouldReceive("delete")
                ->with($quickEntry->entry->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(app(MovementRepositoryContract::class))
                ->shouldReceive("deletePolymorph")
                ->with(Entry::class, $quickEntry->entry->id)
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->delete(route("quick-entries.destroy", $quickEntry))
            ->assertRedirect(route("quick-entries.index"))
            ->assertSessionHas("alert_type", "success");

        Mockery::close();
    }
}
