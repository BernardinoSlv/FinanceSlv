<?php

namespace Tests\Feature\Repositories;

use App\Models\Entry;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class EntryRepositoryTest extends TestCase
{
    use RefreshDatabase;


    /**
     * deve retornar uma coleção vazia
     *
     * @return void
     */
    public function test_all_by_user_method_nonexistent_user(): void
    {
        Entry::factory(20)->create();

        $this->assertCount(0, $this->_repository()->allByUser(0));
    }

    /**
     * deve retornar uma coleção vazia
     *
     * @return void
     */
    public function test_all_by_user_method_without_entries(): void
    {
        // entradas de outros usuários
        Entry::factory(20)->create();
        $user = User::factory()->create();

        $this->assertCount(0, $this->_repository()->allByUser($user->id));
    }

    /**
     * deve retornar uma coleção com 2 entradas
     *
     * @return void
     */
    public function test_all_by_user_method(): void
    {
        // entradas de outros usuários
        Entry::factory(20)->create();
        $user = User::factory()->create();
        Entry::factory(2)->create([
            "user_id" => $user->id
        ]);

        $this->assertCount(2, $this->_repository()->allByUser($user->id));
    }

    /**
     * deve criar a entrada corretamente
     *
     * @return void
     */
    public function test_create_method(): void
    {
        $user = User::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->assertNotNull($this->_repository()->create($user->id, $data));
        $this->assertDatabaseHas("entries", [
            ...$data,
            "user_id" => $user->id,
        ]);
    }

    protected function _repository(): EntryRepositoryContract
    {
        return App::make(EntryRepositoryContract::class);
    }
}
