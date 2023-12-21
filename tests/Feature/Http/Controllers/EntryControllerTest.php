<?php

namespace Tests\Feature\Http\Controllers;

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
        $this->get(route("entry.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view entry.index
     *
     * @return void
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("entry.index"))
            ->assertOk()
            ->assertViewIs("entry.index")
            ->assertViewHas("entries");
    }

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("entry.create"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view entry.create
     *
     * @return void
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("entry.create"))
            ->assertOk()
            ->assertViewIs("entry.create");
    }

    /**
     * deve redirecionar para o página de login
     *
     * @return void
     */
    public function test_store_action_unauthenticated(): void
    {
        $data = Entry::factory()->make()->toArray();

        $this->post(route("entry.store"), $data)
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

        $this->actingAs($user)->post(route("entry.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
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
        $user = User::factory()->create();
        $data = Entry::factory()->make([
            "amount" => '125,55',
            "description" => "Apenas um teste"
        ])->toArray();

        $this->actingAs($user)->post(route("entry.store"), $data)
            ->assertRedirect(route("entry.index"))
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
        $user = User::factory()->create();
        $data = Entry::factory()->make([
            "amount" => "192.125,25"
        ])->toArray();

        $this->actingAs($user)->post(route("entry.store"), $data)
            ->assertRedirect(route("entry.index"))
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

        $this->get(route("entry.edit", [
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

        $this->get(route("entry.edit", [
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

        $this->actingAs($user)->get(route("entry.edit", [
            "entry" => $entry->id
        ]))
            ->assertStatus(404);
    }

    /**
     * deve ter status 200 e view entry.edit
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("entry.edit", [
            "entry" => $entry->id
        ]))
            ->assertOk()
            ->assertViewIs("entry.edit")
            ->assertViewHas("entry");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_update_action_unauthenticated(): void
    {
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->put(route("entry.update", [
            "entry" => $entry->id
        ]), $data)
            ->assertRedirect(route("auth.index"));
    }

    public function test_update_action_nonexistent(): void
    {
        $user = User::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->actingAs($user)->put(route("entry.update", [
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

        $this->actingAs($user)->put(route("entry.update", [
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
        $user = User::factory()->create();
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make([
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("entry.update", [
            "entry" => $entry->id
        ]), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()->create();
        $entry = Entry::factory()->create([
            "user_id" => $user->id
        ]);
        $data = Entry::factory()->make([
            "amount" => "5.000,00"
        ])->toArray();

        $this->actingAs($user)->put(route("entry.update", [
            "entry" => $entry->id
        ]), $data)
            ->assertRedirect(route("entry.edit", [
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
}
