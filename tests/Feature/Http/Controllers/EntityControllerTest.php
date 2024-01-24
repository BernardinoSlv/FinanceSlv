<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EntityControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("entities.index"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $this->actingAs($this->_user())->get(route("entities.index"))
            ->assertOk()
            ->assertViewIs("entities.index")
            ->assertViewHas("entities");
    }

    /**
     * deve redirecionar para login
     */
    public function test_create_action_unauthenticated(): void
    {
        $this->get(route("entities.create"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_create_action(): void
    {
        $this->actingAs($this->_user())->get(route("entities.create"))
            ->assertOk()
            ->assertViewIs("entities.create");
    }

    /**
     * deve redirecionar para login
     */
    public function test_store_action_unauthenticated(): void
    {
        $this->post(route("entities.store"))->assertRedirectToRoute("auth.index");
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $this->actingAs($this->_user())->post(route("entities.store"))
            ->assertFound()
            ->assertSessionHasErrors([
                "name",
            ])
            ->assertSessionDoesntHaveErrors([
                "phone",
                "avatar",
                "description"
            ]);
    }

    /**
     * deve retornar erro de validação apenas no campo phone
     *
     * @dataProvider invalidPhonesProvider
     */
    public function test_store_action_invalid_phone_format(string $phone): void
    {
        $data = Entity::factory()->make([
            "phone" => $phone
        ])->toArray();

        $this->actingAs($this->_user())->post(route("entities.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors("phone")
            ->assertSessionDoesntHaveErrors([
                "name",
                "avatar",
                "description"
            ]);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo name
     */
    public function test_store_action_duplicated_name(): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make([
            "name" => $entity->name
        ])->toArray();

        $this->actingAs($user)->post(route("entities.store"), $data)
            ->assertFound()
            ->assertSessionHasErrors("name")
            ->assertSessionDoesntHaveErrors([
                "phone",
                "avatar",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        $user = $this->_user();
        $data = Entity::factory()->make()->toArray();

        $this->actingAs($user)->post(route("entities.store"), $data)
            ->assertRedirectToRoute("entities.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("entities", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve salvar o avatar
     */
    public function test_store_action_send_avatar(): void
    {
        Storage::fake();

        $user = $this->_user();
        $data = Entity::factory()->make([
            "avatar" => UploadedFile::fake()->image("test.jpg")
        ])->toArray();

        $this->actingAs($user)->post(route("entities.store"), $data)
            ->assertRedirectToRoute("entities.index")
            ->assertSessionHas("alert_type", "success");

        $entity = Entity::query()->where([
            ...Arr::except($data, "avatar"),
            "user_id" => $user->id
        ])->first();
        $this->assertNotNull($entity->avatar);
        $this->assertStringEndsWith(".jpg", $entity->avatar);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action_same_name_other_user(): void
    {
        $user = $this->_user();
        $data = Entity::factory()->make([
            "name" => Entity::factory()->create()->name
        ])->toArray();

        $this->actingAs($user)->post(route("entities.store"), $data)
            ->assertRedirectToRoute("entities.index")
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("entities", [
            ...$data,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar para login
     */
    public function test_edit_action_unauthenticated(): void
    {
        $entity = Entity::factory()->create();

        $this->get(route("entities.edit", $entity))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->get(route("entities.edit", 0))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action_is_not_owner(): void
    {
        $entity = Entity::factory()->create();

        $this->actingAs($this->_user())->get(route("entities.edit", $entity))
            ->assertNotFound();
    }

    /**
     * deve ter status 404
     */
    public function test_edit_action(): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->get(route("entities.edit", $entity))
            ->assertOk()
            ->assertViewIs("entities.edit")
            ->assertViewHas("entity");
    }

    /**
     * deve redirecionar para login
     */
    public function test_update_action_unauthenticated(): void
    {
        $entity = Entity::factory()->create();

        $this->put(route("entities.update", $entity))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_nonexistent(): void
    {
        $this->actingAs($this->_user())->put(route("entities.update", 0))
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erros de validação
     */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()->create();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);

        $this->actingAs($user)->put(route("entities.update", $entity))
            ->assertFound()
            ->assertSessionHasErrors("name")
            ->assertSessionDoesntHaveErrors([
                "avatar",
                "phone",
                "description"
            ]);
    }

    /**
     * deve ter status 404
     */
    public function test_update_action_is_not_owner(): void
    {
        $entity = Entity::factory()->create();
        $data = Entity::factory()->make()->toArray();

        $this->actingAs($this->_user())->put(route("entities.update", $entity), $data)
            ->assertNotFound();
    }

    /**
     * deve redirecionar com erro de validação apenas no campo name
     */
    public function test_update_action_duplicated_name(): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make([
            "name" => Entity::factory()->create(["user_id" => $user])->name
        ])->toArray();

        $this->actingAs($user)->put(route("entities.update", $entity), $data)
            ->assertFound()
            ->assertSessionHasErrors("name")
            ->assertSessionDoesntHaveErrors([
                "avatar",
                "phone",
                "description"
            ]);
    }

    /**
     * deve redirecionar com erro de validação apenas no campo phone
     *
     * @dataProvider invalidPhonesProvider
     */
    public function test_update_action_invalid_phone(string $phone): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make([
            "phone" => $phone
        ])->toArray();

        $this->actingAs($user)->put(route("entities.update", $entity), $data)
            ->assertFound()
            ->assertSessionHasErrors("phone")
            ->assertSessionDoesntHaveErrors([
                "avatar",
                "name",
                "description"
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make()->toArray();

        $this->actingAs($user)->put(route("entities.update", $entity), $data)
            ->assertRedirectToRoute("entities.edit", $entity)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("entities", [
            ...$data,
            "id" => $entity->id,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_name_other_user(): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make([
            "name" => Entity::factory()->create()->name
        ])->toArray();

        $this->actingAs($user)->put(route("entities.update", $entity), $data)
            ->assertRedirectToRoute("entities.edit", $entity)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("entities", [
            ...$data,
            "id" => $entity->id,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action_same_name(): void
    {
        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make([
            "name" => $entity->name
        ])->toArray();

        $this->actingAs($user)->put(route("entities.update", $entity), $data)
            ->assertRedirectToRoute("entities.edit", $entity)
            ->assertSessionHas("alert_type", "success");
        $this->assertDatabaseHas("entities", [
            ...$data,
            "id" => $entity->id,
            "user_id" => $user->id
        ]);
    }

    /**
     * deve salvar o avatar
     */
    public function test_update_action_send_avatar(): void
    {
        Storage::fake();

        $user = $this->_user();
        $entity = Entity::factory()->create([
            "user_id" => $user
        ]);
        $data = Entity::factory()->make([
            "name" => $entity->name,
            "avatar" => UploadedFile::fake()->image("avatar.png")
        ])->toArray();

        $this->actingAs($user)->put(route("entities.update", $entity), $data)
            ->assertRedirectToRoute("entities.edit", $entity)
            ->assertSessionHas("alert_type", "success");
        $actualEntity = Entity::query()->find($entity->id);

        $this->assertNotNull($actualEntity->avatar);
        $this->assertStringEndsWith(".png", $actualEntity->avatar);
    }

    public static function invalidPhonesProvider(): array
    {
        return [
            ["(99)987654321"],
            ["(99)9876-54321"],
            ["99987654321"],
            ["9998765-4321"],
            ["99 987654321"],
            ["99 9876-54321"],
            ["99 98765-4321"]
        ];
    }
}
