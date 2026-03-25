<?php

namespace Tests\Feature\tests\App\Modules\CRM\Models;

use Tests\TestCase;

class DealFunctionalTest extends TestCase
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
