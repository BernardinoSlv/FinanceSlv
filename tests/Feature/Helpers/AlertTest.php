<?php

namespace Tests\Feature\Helpers;

use App\Support\Message;
use Tests\TestCase;

class AlertTest extends TestCase
{
    /**
     * deve retornar o array com message_type successo
     */
    public function test_success_method(): void
    {
        $expect = [
            'message_type' => 'success',
            'message_text' => 'apenas teste.',
        ];

        $this->assertEquals($expect, Message::success('apenas teste.'));
    }

    /**
     * deve retornar o array com message_type danger
     */
    public function test_danger_method(): void
    {
        $expect = [
            'message_type' => 'danger',
            'message_text' => 'apenas teste.',
        ];

        $this->assertEquals($expect, Message::danger('apenas teste.'));
    }
}
