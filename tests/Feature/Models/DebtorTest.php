<?php

namespace Tests\Feature\Models;

use App\Models\Debtor;
use App\Models\Entry;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar uma coleção vazia
     */
    public function test_entries_method_without_entries(): void
    {
        Entry::factory(10)->create();
        $debtor = Debtor::factory()->create();

        $this->assertInstanceOf(Collection::class, $debtor->entries);
        $this->assertCount(0, $debtor->entries);
    }

    /**
     * deve retornar as entradas correspondentes
     */
    public function test_entries_method(): void
    {
        Entry::factory(10)->create();
        $debtor = Debtor::factory()->create();
        Entry::factory(5)->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor
        ]);

        $this->assertInstanceOf(Collection::class, $debtor->entries);
        $this->assertCount(5, $debtor->entries);
    }
}
