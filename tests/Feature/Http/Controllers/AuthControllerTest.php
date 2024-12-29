<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** deve ter status 200 */
    public function test_index_action(): void
    {
        $this->get(route("auth.index"))
            ->assertOk()
            ->assertViewIs("auth.index");
    }

    /** deve redirecionar para o dashboard */
    public function test_index_action_authenticated(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->get(route("auth.index"))
            ->assertRedirect(route("dashboard.index"));
    }

    /** deve redirecionar com mensagem de erro */
    public function test_attempt_action_incorrect_creedentials(): void
    {
        $user = User::factory()->create();

        // email incorreto 
        $this->post(route("auth.attempt"), ["email" => "incorrect-email@gmail.com", "password" => "password"])
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas("message_type", "danger");

        // senha incorreta
        $this->post(route("auth.attempt"), ["email" => $user->email, "password" => "invalid-password"])
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas("message_type", "danger");
    }

    /** deve ter status 403 */
    public function test_attempt_action_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->post(route("auth.attempt"), ["email" => $user->email, "password" => "password"])
            ->assertForbidden();
    }

    /** deve redirecionar para dashboard */
    public function test_attempt_action(): void
    {
        $user = User::factory()->create();

        $this->post(route("auth.attempt"), ["email" => $user->email, "password" => "password"])
            ->assertRedirect(route("dashboard.index"));
        $this->assertAuthenticated();
    }
}
