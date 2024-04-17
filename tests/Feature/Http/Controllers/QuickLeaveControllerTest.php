<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Identifier;
use App\Models\Leave;
use App\Models\QuickLeave;
use App\Models\User;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\Contracts\QuickLeaveRepositoryContract;
use App\Repositories\LeaveRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Mockery;
use Tests\TestCase;

class QuickLeaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para página de login
     */
    public function test_index_unauthenticated(): void
    {
        $this->get(route("quick-leaves.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view leave.index
     */
    public function test_index(): void
    {
        $user = User::factory()->create();
        QuickLeave::factory(10)->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("quick-leaves.index"))
            ->assertOk()
            ->assertViewIs("quick-leaves.index")
            ->assertViewHas("quickLeaves");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_create_unauthenticated(): void
    {
        $this->get(route("quick-leaves.create"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view leave.create
     */
    public function test_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("quick-leaves.create"))
            ->assertOk()
            ->assertViewIs("quick-leaves.create")
            ->assertViewHas("identifiers");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_store_unauthenticated(): void
    {
        $this->post(route("quick-leaves.store"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("quick-leaves.store"))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "identifier_id",
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com erro no campo identifier_id
     */
    public function test_store_action_using_identifier_is_not_owner(): void
    {
        $user = User::factory()->create();
        $data = QuickLeave::factory()->make([
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->post(route("quick-leaves.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",
            ])
            ->assertSessionDoesntHaveErrors(["amount", "title", "description"]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $data = QuickLeave::factory()->make([
            "identifier_id" => Identifier::factory()->create([
                "user_id" => $user
            ]),
            "amount" => "15,99"
        ])->toArray();

        $this->instance(
            QuickLeaveRepositoryContract::class,
            Mockery::mock(app(QuickLeaveRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, [
                    ...Arr::only($data, ["title", "description", "identifier_id"]),
                    "amount" => 15.99
                ])
                ->passthru()
                ->once()
                ->getMock()
        );

        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(app(LeaveRepositoryContract::class))
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["leaveable_type"] !== QuickLeave::class) {
                        return false;
                    } else if (!is_int($attributes["leaveable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->passthru()
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("create")
                ->with($user->id, Mockery::on(function (array $attributes): bool {
                    if ($attributes["movementable_type"] !== Leave::class) {
                        return false;
                    } else if (!is_int($attributes["movementable_id"])) {
                        return false;
                    }
                    return true;
                }))
                ->once()
                ->getMock()

        );

        $this->actingAs($user)->post(route("quick-leaves.store"), $data)
            ->assertRedirect(route("quick-leaves.index"))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_edit_unauthenticated(): void
    {
        $quickLeave = QuickLeave::factory()->create();

        $this->get(route("quick-leaves.edit", $quickLeave))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_edit_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("quick-leaves.edit", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_edit_is_not_owner(): void
    {
        $leave = Leave::factory()->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);

        $this->actingAs($this->_user())->get(route("quick-leaves.edit", $leave->id))
            ->assertStatus(404);
    }

    /**
     * deve ter status 200 e view leave.edit
     */
    public function test_edit(): void
    {
        $user = $this->_user();
        $quickLeave = QuickLeave::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($user)->get(route("quick-leaves.edit", $quickLeave))
            ->assertOk()
            ->assertViewIs("quick-leaves.edit")
            ->assertViewHas(["quickLeave", "identifiers"]);
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_update_unauthenticated(): void
    {
        $quickLeave = QuickLeave::factory()->create();

        $this->put(route("quick-leaves.update", $quickLeave))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_update_nonexistent(): void
    {
        $this->actingAs($this->_user())->put(route("quick-leaves.update", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_update_is_not_owner(): void
    {
        $user = $this->_user();
        $leave = Leave::factory()->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);
        $data = Leave::factory()->make([
            "identifier_id" => Identifier::factory()->create([
                "user_id" => $user
            ]),
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($user)->put(route("quick-leaves.update", $leave->id), $data)
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_without_data(): void
    {
        $user = $this->_user();
        $quickLeave = QuickLeave::factory()->create([
            "user_id" => $user->id
        ]);

        $this->actingAs($this->_user())->put(route("quick-leaves.update", $quickLeave->id))
            ->assertStatus(302)
            ->assertSessionHasErrors([
                "title",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors("description");
    }

    /**
     * deve redirecionar com erro de validação no campo identifier_id
     */
    public function test_update_action_using_identifier_is_not_owner(): void
    {
        $user = $this->_user();
        $quickLeave = QuickLeave::factory()->create([
            "user_id" => $user->id
        ]);
        $data = QuickLeave::factory()->make([
            "amount" => "100,00"
        ])->toArray();

        $this->actingAs($this->_user())->put(route("quick-leaves.update", $quickLeave), $data)
            ->assertFound(302)
            ->assertSessionHasErrors([
                "identifier_id",
            ])
            ->assertSessionDoesntHaveErrors([
                "title",
                "description",
                "amount"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update(): void
    {
        $user = $this->_user();
        $quickLeave = QuickLeave::factory()->create([
            "user_id" => $user->id
        ]);
        $data = QuickLeave::factory()->make([
            "identifier_id" => Identifier::factory()->create([
                "user_id" => $user
            ]),
            "amount" => "100,00"
        ])->toArray();

        $this->instance(
            QuickLeaveRepositoryContract::class,
            Mockery::mock(QuickLeaveRepositoryContract::class)
                ->shouldReceive("update")
                ->with($quickLeave->id, [
                    ...Arr::only($data, [
                        "title", "description", "amount", "identifier_id"
                    ]),
                    "amount" => 100
                ])
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->put(route("quick-leaves.update", $quickLeave), $data)
            ->assertRedirect(route("quick-leaves.index"))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_destroy_unauthenticated(): void
    {
        $quickLeave = QuickLeave::factory()->create();

        $this->delete(route("quick-leaves.destroy", $quickLeave))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_nonexistent(): void
    {
        $this->actingAs($this->_user())->delete(route("quick-leaves.destroy", 0))
            ->assertStatus(404);
    }

    /**
     * deve ter status 404
     */
    public function test_destroy_is_not_owner(): void
    {
        $leave = Leave::factory()->create([
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => QuickLeave::factory()->create()
        ]);

        $this->actingAs($this->_user())->delete(route("quick-leaves.destroy", $leave->id))
            ->assertStatus(404);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy(): void
    {
        $user = $this->_user();
        $quickLeave = QuickLeave::factory()->hasLeave()->create(["user_id" => $user]);

        $this->instance(
            QuickLeaveRepositoryContract::class,
            Mockery::mock(QuickLeaveRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($quickLeave->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            LeaveRepositoryContract::class,
            Mockery::mock(LeaveRepositoryContract::class)
                ->shouldReceive("delete")
                ->with($quickLeave->leave->id)
                ->once()
                ->getMock()
        );
        $this->instance(
            MovementRepositoryContract::class,
            Mockery::mock(MovementRepositoryContract::class)
                ->shouldReceive("deletePolymorph")
                ->with(Leave::class, $quickLeave->leave->id)
                ->once()
                ->getMock()
        );

        $this->actingAs($user)->delete(route("quick-leaves.destroy", $quickLeave))
            ->assertRedirect(route("quick-leaves.index"))
            ->assertSessionHas("alert_type", "success");
        Mockery::close();
    }
}
