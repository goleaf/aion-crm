<?php

namespace Tests\Feature\tests\App\Modules\CRM\Actions;

use Tests\TestCase;

class CloseDealActionFunctionalTest extends TestCase
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
