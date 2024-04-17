<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\Debtor;
use App\Models\Entry;
use App\Models\Movement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar null
     */
    public function test_movements_method_without_movements(): void
    {
        $entry = Entry::factory()->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => Debtor::factory()->create()
        ]);

        $this->assertNull($entry->movement);
    }

    /**
     * deve retornar a movimentação correspondente
     */
    public function test_movements(): void
    {
        $entry = Entry::factory()->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => Debtor::factory()->create()
        ]);
        $movement = Movement::factory()->create([
            "movementable_type" => Entry::class,
            "movementable_id" => $entry
        ]);

        $this->assertEquals($movement->id, $entry->movement->id);
    }
}
