<?php

namespace Tests\Integration\Http\Web\Auth;

use App\Livewire\Auth\LoginPage;
use App\Modules\Shared\Models\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(LoginPage::class)]
#[Group('authentication')]
class LoginPageIntegrationTest extends FunctionalTestCase
{
    public function test_it_renders_login_page_for_guests(): void
    {
        // Arrange

        // Act

        $response = $this->get(route('login'));

        // Assert

        $response
            ->assertOk()
            ->assertSeeText('Sign in')
            ->assertSeeText('Remember me');
    }

    public function test_it_redirects_authenticated_users_away_from_login_page(): void
    {
        // Arrange

        $user = User::factory()->create();

        // Act

        $response = $this->actingAs($user, 'web')->get(route('login'));

        // Assert

        $response->assertRedirect(route('users.index'));
    }

    public function test_it_renders_login_page_when_database_sessions_are_enabled(): void
    {
        // Arrange

        config()->set('session.driver', 'database');

        // Act

        $response = $this->get(route('login'));

        // Assert

        $response
            ->assertOk()
            ->assertSeeText('Sign in')
            ->assertSeeText('Remember me');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        // Act

        $response = $this->get(route('login'));

        // Assert

        $response->assertOk();
    }
}
