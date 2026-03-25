<?php

namespace Tests\Feature\App\Modules\Activities\Actions;

use Tests\TestCase;

class SendActivityReminderActionFunctionalTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
