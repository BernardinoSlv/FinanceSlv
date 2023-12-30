<?php

namespace Tests\Feature\Repositories;

use App\Models\Entry;
use App\Repositories\Contracts\EntryRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BaseRepositoryTest extends TestCase
{
    use RefreshDatabase;

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
