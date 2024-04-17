<?php

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\QuickEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class QuickEntryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar null
     */
    public function test_entry_method_without_entry(): void
    {
        Entry::factory(10)->create([
            "entryable_type" => QuickEntry::class,
            "entryable_id" => QuickEntry::factory()->create()
        ]);
        $quickEntry = QuickEntry::factory()->create();

        $this->assertNull($quickEntry->entry);
    }

    /**
     * deve retonar a entrada correspondente
     */
    public function test_entry_method(): void
    {
        Entry::factory(10)->create([
            "entryable_type" => QuickEntry::class,
            "entryable_id" => QuickEntry::factory()->create()
        ]);
        $quickEntry = QuickEntry::factory()->create();
        $entry = Entry::factory()->create([
            "entryable_type" => QuickEntry::class,
            "entryable_id" => $quickEntry
        ]);

        $this->assertEquals($entry->id, $quickEntry->entry->id);
    }
}
