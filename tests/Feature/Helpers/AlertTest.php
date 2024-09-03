<?php

namespace Tests\Feature\Helpers;

use App\Helpers\Alert;
use Tests\TestCase;

class AlertTest extends TestCase
{
    /**
     * deve retornar o array com alert_type successo
     */
    public function test_success_method(): void
    {
        $expect = [
            'alert_type' => 'success',
            'alert_text' => 'apenas teste.',
        ];

        $this->assertEquals($expect, Alert::success('apenas teste.'));
    }

    /**
     * deve retornar o array com alert_type danger
     */
    public function test_danger_method(): void
    {
        $expect = [
            'alert_type' => 'danger',
            'alert_text' => 'apenas teste.',
        ];

        $this->assertEquals($expect, Alert::danger('apenas teste.'));
    }
}
