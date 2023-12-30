<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveControllerTest extends TestCase
{
    /**
     * deve redirecionar para página de login
     */
    public function test_index_unauthenticated(): void
    {
        $this->get(route("leaves.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view leave.index
     */
    public function test_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("leaves.index"))
            ->assertOk()
            ->assertViewIs("leave.index")
            ->assertViewHas("leaves");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_create_unauthenticated(): void
    {
        $this->get(route("leaves.create"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view leave.create
     */
    public function test_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("leaves.create"))
            ->assertOk()
            ->assertViewIs("leave.create");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_store_unauthenticated(): void
    {
        $this->post(route("leaves.store"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("leaves.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store(): void
    {
        $user = User::factory()->create();
        $data = Leave::factory()->make([
            "amount" => "15,99"
        ])->toArray();

        $this->actingAs($user)->post(route("leaves.store"), $data)
            ->assertRedirect(route("leaves.index"))
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("leaves", [
            ...$data,
            "amount" => 15.99,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_edit_unauthenticated(): void
    {
        $leave = Leave::factory()->create();

        $this->get(route("leaves.edit", $leave->id))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("leaves.edit", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_edit_is_not_owner(): void
    {
        $leave = Leave::factory()->create();

        $this->actingAs($this->_user())->get(route("leaves.edit", $leave->id))
            ->assertStatus(404);
    }

    /**
     * deve ter status 200 e view leave.edit
     */
    public function test_edit(): void
    {
        $user = $this->_user();
        $leave = Leave::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("leaves.edit", $leave->id))
            ->assertOk()
            ->assertViewIs("leave.edit")
            ->assertViewHas("leave");
    }
}
