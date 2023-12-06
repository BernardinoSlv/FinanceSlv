<?php

namespace Tests\Feature\Helpers;

use App\Helpers\Alert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlertTest extends TestCase
{
    /**
     * deve retornar o array com alert_type successo
     *
     * @return void
     */
    public function test_success_method(): void
    {
        $expect = [
            "alert_type" => "success",
            "alert_text" => "apenas teste."
        ];

        $this->assertEquals($expect, Alert::success("apenas teste."));
    }

    /**
     * deve retornar o array com alert_type danger
     *
     * @return void
     */
    public function test_danger_method(): void
    {
        $expect = [
            "alert_type" => "danger",
            "alert_text" => "apenas teste."
        ];

        $this->assertEquals($expect, Alert::danger("apenas teste."));
    }
}
