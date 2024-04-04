<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use App\Models\Leave;
use App\Models\User;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class InvestimentLeaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.leaves.index", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_index_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.index", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_index_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.index", $investiment))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.leaves.index", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.leaves.index")
            ->assertViewHas("investiment", function (Investiment $investiment): bool {
                return $investiment->relationLoaded("leaves");
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->get(route("investiments.leaves.create", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_create_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.create", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_create_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();

        $this->actingAs($user)->get(route("investiments.leaves.create", $investiment))
            ->assertForbidden();
    }


    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.leaves.create", $investiment))
            ->assertOk()
            ->assertViewIs("investiments.leaves.create")
            ->assertViewHas("investiment");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->create();

        $this->post(route("investiments.leaves.store", $investiment))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_store_action_nonexistent(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("investiments.leaves.store", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user->id]);

        $this->actingAs($user)->post(route("investiments.leaves.store", $investiment))
            ->assertFound()
            ->assertSessionHasErrors("amount");
    }

    /**
     * deve ter status 403
     */
    public function test_store_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create();
        $data = [
            "amount" => "1.000,00"
        ];

        $this->actingAs($user)->post(route("investiments.leaves.store", $investiment), $data)
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
            "amount" => "1.000,00"
        ];

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    "leaveable_type" => Investiment::class,
                    "leaveable_id" => $investiment->id,
                    "amount" => 1000.00
                ])
                ->once()
                ->passthru()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["movementable_type"] !== Leave::class) {
                        return false;
                    }
                    return is_int($attributes["movementable_id"]);
                }))
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->post(route("investiments.leaves.store", $investiment), $data)
            ->assertRedirect(route("investiments.leaves.index", $investiment))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }
}
