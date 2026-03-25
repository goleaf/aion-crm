<?php

namespace Tests\Feature\App\Modules\Activities\Models;

use Tests\TestCase;

class ActivityFunctionalTest extends TestCase
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
