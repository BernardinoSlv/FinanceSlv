<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Investiment;
use App\Models\Leave;
use App\Models\Movement;
use App\Models\User;
use App\Repositories\Contracts\InvestimentRepositoryContract;
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

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->hasLeaves(1)->create();
        $leave = $investiment->leaves->first();

        $this->get(route("investiments.leaves.edit", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent_investiment(): void
    {
        $user = User::factory()->create();
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.leaves.edit", [
            "investiment" => 0,
            "leave" => $leave
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

        $this->actingAs($user)->get(route("investiments.leaves.edit", [
            "investiment" => $investiment,
            "leave" => 0
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
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("investiments.leaves.edit", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasLeaves(1)->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();

        $this->actingAs($user)->get(route("investiments.leaves.edit", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasLeaves(1, ["user_id" => $user])
            ->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();

        $this->actingAs($user)->get(route("investiments.leaves.edit", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertOk()
            ->assertViewIs("investiments.leaves.edit")
            ->assertViewHas(["investiment", "leave"]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->hasLeaves(1)->create();
        $leave = $investiment->leaves->first();

        $this->put(route("investiments.leaves.update", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent_investiment(): void
    {
        $user = User::factory()->create();
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->put(route("investiments.leaves.update", [
            "investiment" => 0,
            "leave" => $leave
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

        $this->actingAs($user)->put(route("investiments.leaves.update", [
            "investiment" => $investiment,
            "leave" => 0
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
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->put(route("investiments.leaves.update", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erro de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasLeaves(1)->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();

        $this->actingAs($user)->put(route("investiments.leaves.update", [
            "investiment" => $investiment,
            "leave" => $leave
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
        $investiment = Investiment::factory()->hasLeaves(1)->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();
        $data = [
            "amount" => "2.500,00"
        ];

        $this->actingAs($user)->put(route("investiments.leaves.update", [
            "investiment" => $investiment,
            "leave" => $leave
        ]), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasLeaves(1, ["user_id" => $user])
            ->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();
        $data = [
            "amount" => "2.500,00"
        ];

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(LeaveRepositoryContract::class)
                ->shouldReceive("update")
                ->with($leave->id, [
                    "amount" => 2500.00
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("investiments.leaves.update", [
            "investiment" => $investiment,
            "leave" => $leave
        ]), $data)
            ->assertRedirect(route("investiments.leaves.edit", [
                "investiment" => $investiment,
                "leave" => $leave
            ]))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para login
     */
    public function test_destroy_action_unauthenticated(): void
    {
        $investiment = Investiment::factory()->hasLeaves(1)->create();
        $leave = $investiment->leaves->first();

        $this->delete(route("investiments.leaves.destroy", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_action_nonexistent_investiment(): void
    {
        $user = User::factory()->create();
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->delete(route("investiments.leaves.destroy", [
            "investiment" => 0,
            "leave" => $leave
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

        $this->actingAs($user)->delete(route("investiments.leaves.destroy", [
            "investiment" => $investiment,
            "leave" => 0
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_delete_action_is_not_of_the_investiment(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->create(["user_id" => $user]);
        $leave = Leave::factory()->create(["user_id" => $user]);

        $this->actingAs($user)->delete(route("investiments.leaves.destroy", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertNotFound();
    }

    /**
     * deve ter status 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasLeaves(1)->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();

        $this->actingAs($user)->delete(route("investiments.leaves.destroy", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()->create();
        $investiment = Investiment::factory()->hasLeaves(1, ["user_id" => $user])
            ->create(["user_id" => $user]);
        $leave = $investiment->leaves->first();
        $movement = Movement::factory()->create([
            "movementable_type" => Leave::class,
            "movementable_id" => $leave
        ]);

        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($movement->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(LeaveRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($leave->id)
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->delete(route("investiments.leaves.destroy", [
            "investiment" => $investiment,
            "leave" => $leave
        ]))
            ->assertRedirect(route("investiments.leaves.index", $investiment))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }
}
