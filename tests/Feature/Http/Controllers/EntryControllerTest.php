<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class EntryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("entries.index"))
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

        $this->actingAs($user)->get(route("entries.index"))
            ->assertOk()
            ->assertViewIs("entries.index")
            ->assertViewHas("entries");
    }

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("entries.create"))
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

        $this->actingAs($user)->get(route("entries.create"))
            ->assertOk()
            ->assertViewIs("entries.create")
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

        $this->post(route("entries.store"), $data)
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

        $this->actingAs($user)->post(route("entries.store"))
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
        $data = Entry::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("entries.store"), $data)
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
        $data = Entry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => '125,55',
            "description" => "Apenas um teste"
        ])->toArray();

        $this->actingAs($user)->post(route("entries.store"), $data)
            ->assertRedirect(route("entries.index"))
            ->assertSessionHas([
                "alert_type" => "success",
                "alert_text" => "Entrada criada com sucesso."
            ]);
        $this->assertDatabaseHas("entries", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 125.55
        ]);
    }

    /**
     * deve criar e redirecionar com mensagem de sucesso
     *
     * @return void
     */
    public function test_store_action_with_amount_real_formatting(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $data = Entry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "192.125,25"
        ])->toArray();

        $this->actingAs($user)->post(route("entries.store"), $data)
            ->assertRedirect(route("entries.index"))
            ->assertSessionHas([
                "alert_type" => "success",
                "alert_text" => "Entrada criada com sucesso."
            ]);
        $this->assertDatabaseHas("entries", [
            ...$data,
            "user_id" => $user->id,
            "amount" => 192125.25
        ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $entry = Entry::factory()->create();

        $this->get(route("entries.edit", [
            "entry" => $entry->id
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->get(route("entries.edit", [
            "entry" => 0
        ]))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create();

        $this->actingAs($user)->get(route("entries.edit", [
            "entry" => $entry->id
        ]))
            ->assertStatus(404);
    }

    /**
     * deve ter status 200 e view entries.edit
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("entries.edit", [
            "entry" => $entry->id
        ]))
            ->assertOk()
            ->assertViewIs("entries.edit")
            ->assertViewHas([
                "entry",
                "identifiers"
            ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_update_action_unauthenticated(): void
    {
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->put(route("entries.update", [
            "entry" => $entry->id
        ]), $data)
            ->assertRedirect(route("auth.index"));
    }

    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->actingAs($user)->put(route("entries.update", [
            "entry" => 0
        ]), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com erros de validação -
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->put(route("entries.update", [
            "entry" => $entry->id
        ]))
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
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("entries.update", [
            "entry" => $entry->id
        ]), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo identifier_id
     */
    public function test_update_action_with_identifier_of_other_user(): void
    {
        $user = User::factory()->hasIdentifiers(1)->create();
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("entries.update", [
            "entry" => $entry->id
        ]), $data)
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
        $entry = Entry::factory()->create([
            "identifier_id" => $user->identifiers->first(),
            "user_id" => $user->id
        ]);
        $data = Entry::factory()->make([
            "identifier_id" => $user->identifiers->first(),
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("entries.update", [
            "entry" => $entry->id
        ]), $data)
            ->assertRedirect(route("entries.edit", [
                "entry" => $entry->id
            ]))
            ->assertSessionHas([
                "alert_type" => "success"
            ]);
        $this->assertDatabaseHas("entries", [
            ...$data,
            "amount" => 5000.00,
            "id" => $entry->id,
            "user_id" => $entry->user_id,
        ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_destroy_unauthenticated(): void
    {
        $entry = Entry::factory()->create();

        $this->delete(route("entries.destroy", [
            "entry" => $entry->id
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route("entries.destroy", [
            "entry" => 0
        ]))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_is_not_owner(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create();

        $this->actingAs($user)->delete(route("entries.destroy", [
            "entry" => $entry->id
        ]))
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->delete(route("entries.destroy", [
            "entry" => $entry->id
        ]))
            ->assertRedirect(route("entries.index"))
            ->assertSessionHas("alert_type", "success");

        $this->assertDatabaseMissing("entries", [
            "id" => $entry->id
        ]);
    }
}
