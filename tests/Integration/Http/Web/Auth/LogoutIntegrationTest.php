<?php

namespace Tests\Integration\Http\Web\Auth;

use App\Http\Web\Auth\Controllers\LogoutController;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(LogoutController::class)]
#[Group('authentication')]
class LogoutIntegrationTest extends FunctionalTestCase
{
    public function test_it_logs_out_authenticated_users(): void
    {
        // Arrange

        $user = User::factory()->create();

        // Act

        $response = $this->actingAs($user, 'web')->post(route('logout'));

        // Assert

        $response->assertRedirect(route('login'));

        $this->assertGuest('web');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        $user = User::factory()->create();

        // Act

        $response = $this->actingAs($user, 'web')->post(route('logout'));

        // Assert

        $response->assertRedirect(route('login'));
    }
}
