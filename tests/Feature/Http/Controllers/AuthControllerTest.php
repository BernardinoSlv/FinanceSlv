<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\LogTypeEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** deve redirecionar para o dashboard */
    public function test_index_action_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->get(route("auth.index"))
            ->assertRedirect(route("dashboard.index"));
    }

    /** deve ter status 200 */
    public function test_index_action(): void
    {
        $this->get(route("auth.index"))
            ->assertOk()
            ->assertViewIs("auth.index");
    }

    /** deve redirecionar com erros de validação */
    public function test_attempt_action_without_data(): void
    {
        $this->post(route("auth.attempt"), [])
            ->assertFound()
            ->assertSessionHasErrors([
                "email",
                "password"
            ]);
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
        $this->assertCount(1, $user->logs()->where("type", LogTypeEnum::INFO->value)->get());
    }

    /** deve redirecionar para dashboard */
    public function test_create_action_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->get(route("auth.create"))
            ->assertRedirect(route("dashboard.index"));
    }

    /** deve ter status 200 */
    public function test_create_action(): void
    {
        $this->get(route("auth.create"))
            ->assertOk()
            ->assertViewIs("auth.create");
    }

    /** deve redirecionar com erros de validação */
    public function test_store_action_without_data(): void
    {
        $this->post(route("auth.store"), [])
            ->assertFound()
            ->assertSessionHasErrors([
                "name",
                "email",
                "password",
                "terms"
            ]);
    }

    /** deve ter status 403 */
    public function test_store_action_when_autenticated(): void
    {
        $user = User::factory()->create();
        $this->be($user);
        $data = User::factory()->make()->toArray();
        $data["password"] = "password";
        $data["password_confirmation"] = "password";
        $data["terms"] = "on";

        $this->post(route("auth.store"), $data)
            ->assertForbidden();
    }

    /** deve redirecionar para tela de login  */
    public function test_store_action(): void
    {
        $data = User::factory()->make()->toArray();
        $data["password"] = "password";
        $data["password_confirmation"] = "password";
        $data["terms"] = "on";

        $this->post(route("auth.store"), $data)
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas("message_type", "success");
        $this->assertNotNull($user = User::query()->where(Arr::only($data, ["name", "email"]))->first());
        $this->assertCount(1, $user->logs()->where("type", LogTypeEnum::INFO->value)->get());
    }

    /** deve redirecionar para página de login  */
    public function test_logout_action(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $this->get(route("auth.logout"))
            ->assertRedirect(route("auth.index"))
            ->assertSessionHas("message_type", "primary");
        $this->assertGuest();
    }

    public function test_logout_action_when_unauthenticated(): void
    {
        $this->get(route("auth.logout"))
            ->assertRedirect(route("auth.index"))
            ->assertSessionMissing("message_type");
    }
}
