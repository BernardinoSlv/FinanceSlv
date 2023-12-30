<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve ter status 200 e view auth.create
     *
     * @return void
     */
    public function test_create_action(): void
    {
        $this->get(route("auth.create"))
            ->assertOk()
            ->assertViewIs("auth.create");
    }

    /**
     * deve redirecionar com erros de validação
     *
     * @return void
     */
    public function test_store_action_without_data(): void
    {
        $this->post(route("auth.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "name",
                "email",
                "password",
                "confirm_password",
                "terms"
            ]);
    }

    /**
     * deve redirecionar para página de login com mensagem de sucesso
     *
     * @return void
     */
    public function test_store_action(): void
    {
        $data = [
            ...User::factory()->make()->toArray(),
            "password" => "password",
            "confirm_password" => 'password',
            "terms" => "on",
        ];

        $this->post(route("auth.store"), $data)
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas([
                "alert_type" => "success",
                "alert_text" => "Cadastro realizado com sucesso."
            ]);

        $this->assertDatabaseHas("users", Arr::except(
            $data,
            ["password", "confirm_password", "email_verified_at", "terms"]
        ));
    }

    /**
     * deve ter status 200 e view auth.index
     *
     * @return void
     */
    public function test_index_action(): void
    {
        $this->get(route("auth.index"))
            ->assertOk()
            ->assertViewIs("auth.index");
    }

    /**
     * deve redirecionar com erros de validação
     *
     * @return void
     */
    public function test_index_action_without_data(): void
    {
        $this->post(route("auth.index_store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "email",
                "password"
            ]);
    }

    /**
     * deve redirecionar com erro de credencials
     *
     * @return void
     */
    public function test_index_action_incorrect_credentials(): void
    {
        $user = User::factory()->create();

        // password incorreto
        $this->post(route("auth.index_store"), [
            "email" => $user->email,
            "password" => "incorrect"
        ])
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas([
                "alert_type" => "danger",
                "alert_text" => "E-mail e/ou senha incorretos."
            ]);
        // email incorreto
        $this->post(route("auth.index_store"), [
            "email" => "incorrect@email.com",
            "password" => "password"
        ])
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas([
                "alert_type" => "danger",
                "alert_text" => "E-mail e/ou senha incorretos."
            ]);
    }

    /**
     * deve autenticar e redirecionar para dashboard.index
     *
     * @return void
     */
    public function test_index_store_action(): void
    {
        $user = User::factory()->create();

        $this->post(route("auth.index_store"), [
            "email" => $user->email,
            "password" => "password",
            "remember" => ""
        ])
            ->assertRedirect(route("dashboard.index"));

        $this->assertAuthenticated();
    }

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_logout_action_unauthenticated(): void
    {
        $this->get(route("auth.logout"))
            ->assertSessionMissing([
                "alert_type" => "alert_success",
                "alert_text" => "Volte sempre"
            ]);
    }

    /**
     * deve redirecionar para página de login com mensagem de sucesso
     *
     * @return void
     */
    public function test_logout_action(): void
    {
        $user = User::factory()->create();

        $this->be($user);
        $this->get(route("auth.logout"))
            ->assertSessionMissing([
                "alert_type" => "alert_success",
                "alert_text" => "Volte sempre"
            ]);
        $this->assertGuest();
    }
}
