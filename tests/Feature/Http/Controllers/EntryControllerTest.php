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
}
