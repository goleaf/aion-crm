<?php

namespace Tests\Integration\Http\Web\Auth;

use App\Livewire\Auth\LoginPage;
use App\Modules\Shared\Models\User;
use Database\Seeders\DemoUsersSeeder;
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

    public function test_it_displays_seeded_users_credentials_for_guests(): void
    {
        // Arrange

        config()->set('demo-users.users', [
            [
                'name' => 'Alpha User',
                'email' => 'alpha@example.com',
                'password' => 'password-alpha',
            ],
            [
                'name' => 'Beta User',
                'email' => 'beta@example.com',
                'password' => 'password-beta',
            ],
        ]);

        $this->seed(DemoUsersSeeder::class);

        // Act

        $response = $this->get(route('login'));

        // Assert

        $response
            ->assertOk()
            ->assertSee('Demo credentials')
            ->assertSee('Alpha User')
            ->assertSee('Beta User')
            ->assertSee('password-alpha')
            ->assertSee('password-beta');
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
