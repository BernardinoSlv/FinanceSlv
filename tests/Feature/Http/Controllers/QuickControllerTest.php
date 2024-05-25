<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Models\Identifier;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Tests\TestCase;

class QuickControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("quicks.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view quicks.index
     */
    public function test_index_action(): void
    {
        Quick::factory(2)->create();

        $user = User::factory()->create();
        Quick::factory(2)->create(["user_id" => $user]);

        $this->actingAs($user)->get(route("quicks.index"))
            ->assertOk()
            ->assertViewIs("quicks.index")
            ->assertViewHas("paginator", function (LengthAwarePaginator $paginator): bool {
                return $paginator->total() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("quicks.create"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view quicks.index
     */
    public function test_create_action(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->has(Identifier::factory(2))->create();

        $this->actingAs($user)->get(route("quicks.create"))
            ->assertOk()
            ->assertViewIs("quicks.create")
            ->assertViewHas("identifiers", function (Collection $identifiers): bool {
                return $identifiers->count() === 2;
            });
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unathenticated(): void
    {
        $this->post(route("quicks.store"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route("quicks.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "title",
                "type",
                "amount"
            ])
            ->assertSessionDoesntHaveErrors(["description", "identifier_id"]);
    }

    /**
     * deve redirecionar com erro de validação no campo amount
     */
    public function test_store_action_invalid_amount_format(): void
    {
        $user = User::factory()->create();
        $data = Quick::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "type" => MovementTypeEnum::IN->value,
            "amount" => 500.00
        ])->toArray();

        $this->actingAs($user)->post(route("quicks.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "amount",
            ])
            ->assertSessionDoesntHaveErrors([
                "description",
                "title",
                "type",
                "identifier_id"
            ]);
    }

    /**
     * deve redirecionar com erro de validação no campo identifier_id
     */
    public function test_store_action_using_identifier_from_other_user(): void
    {
        $user = User::factory()->create();
        $data = Quick::factory()->make([
            "identifier_id" => Identifier::factory()->create(),
            "type" => MovementTypeEnum::IN->value,
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->post(route("quicks.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                "identifier_id",
            ])
            ->assertSessionDoesntHaveErrors([
                "description",
                "title",
                "type",
                "amount"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $data = Quick::factory()->make([
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "type" => MovementTypeEnum::IN->value,
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->post(route("quicks.store"), $data)
            ->assertRedirect(route("quicks.index"))
            ->assertSessionHas("alert_type", "success");

        $this->assertDatabaseHas("quicks", [
            ...Arr::except($data, ["type", "amount"]),
            "user_id" => $user->id,
        ]);
        $quick = Quick::query()->where([
            ...Arr::except($data, ["type", "amount"]),
            "user_id" => $user->id,
        ])->first();
        $this->assertDatabaseHas("movements", [
            ...Arr::except($data, ["identifier_id", "description", "title"]),
            "movementable_type" => Quick::class,
            "movementable_id" => $quick->id,
            "user_id" => $user->id,
        ]);
    }
}
