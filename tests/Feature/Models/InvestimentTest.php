<?php

namespace Tests\Feature\Models;

use App\Models\Entry;
use App\Models\Investiment;
use App\Models\Leave;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestimentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar uma coleção vazia
     */
    public function test_leaves_method_without_leaves(): void
    {
        Leave::factory(10)->create();
        $investiment = Investiment::factory()->create();

        $this->assertCount(0, $investiment->leaves);
    }

    /**
     * deve retornar uma coleção com 1 item
     */
    public function test_leaves_method_one_leave(): void
    {
        Leave::factory(10)->create();
        $investiment = Investiment::factory()->create();
        Leave::factory()->create([
            "leaveable_type" => Investiment::class,
            "leaveable_id" => $investiment->id
        ]);

        $this->assertCount(1, $investiment->leaves);
    }

    /**
     * deve retornar uma coleção com 10 item
     */
    public function test_leaves_method(): void
    {
        Leave::factory(10)->create();
        $investiment = Investiment::factory()->create();
        Leave::factory(10)->create([
            "leaveable_type" => Investiment::class,
            "leaveable_id" => $investiment->id
        ]);

        $this->assertCount(10, $investiment->leaves);
    }

    /**
     * deve retornar uma coleção vazia
     */
    public function test_entries_method_without_entries(): void
    {
        Entry::factory(10)->create();
        $investiment = Investiment::factory()->create();

        $this->assertCount(0, $investiment->entries);
    }

    /**
     * deve retornar 5 entradas
     */
    public function test_entries_method(): void
    {
        Entry::factory(10)->create();
        $investiment = Investiment::factory()->create();
        Entry::factory(5)->create([
            "entryable_type" => Investiment::class,
            "entryable_id" => $investiment
        ]);

        $this->assertCount(5, $investiment->entries);
    }
}
