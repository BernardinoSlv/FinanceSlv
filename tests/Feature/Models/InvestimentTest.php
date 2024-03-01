<?php

namespace Tests\Feature\Models;

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
}
