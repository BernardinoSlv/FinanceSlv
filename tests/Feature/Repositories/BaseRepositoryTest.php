<?php

namespace Tests\Feature\Repositories;

use App\Models\Entry;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseRepositoryTest extends TestCase
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
     * deve retornar false
     */
    public function test_update_nonexistent(): void
    {
        $data = Entry::factory()->make()->toArray();

        $this->assertFalse($this->_repository()->update(0, $data));
    }

    public function test_update_method(): void
    {
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->assertTrue($this->_repository()->update($entry->id, $data));
        $this->assertDatabaseHas("entries", [
            ...$data,
            "id" => $entry->id,
            "user_id" => $entry->user_id
        ]);
    }

    /**
     * deve retornar false
     */
    public function test_delete_nonexistent(): void
    {
        $this->assertFalse($this->_repository()->delete(0));
    }

    /**
     * deve remover a entrada
     */
    public function test_delete(): void
    {
        $entry = Entry::factory()->create();

        $this->assertTrue($this->_repository()->delete($entry->id));
        $this->assertDatabaseMissing("entries", [
            "id" => $entry->id
        ]);
    }

    protected function _repository(): EntryRepositoryContract
    {
        return app()->make(EntryRepositoryContract::class);
    }
}
