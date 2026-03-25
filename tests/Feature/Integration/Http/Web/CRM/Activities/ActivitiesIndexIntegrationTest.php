<?php

namespace Tests\Feature\Integration\Http\Web\CRM\Activities;

use Tests\TestCase;

class ActivitiesIndexIntegrationTest extends TestCase
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
