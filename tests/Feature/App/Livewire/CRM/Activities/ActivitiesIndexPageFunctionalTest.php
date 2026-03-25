<?php

namespace Tests\Feature\App\Livewire\CRM\Activities;

use Tests\TestCase;

class ActivitiesIndexPageFunctionalTest extends TestCase
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
