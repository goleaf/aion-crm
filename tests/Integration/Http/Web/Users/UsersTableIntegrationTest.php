<?php

namespace Tests\Integration\Http\Web\Users;

use App\Livewire\Users\UsersTablePage;
use App\Modules\Shared\Models\User;
use Database\Seeders\DemoUsersSeeder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(UsersTablePage::class)]
#[Group('authentication')]
class UsersTableIntegrationTest extends FunctionalTestCase
{
    public function test_it_requires_authentication(): void
    {
        // Arrange

        // Act

        $response = $this->get(route('users.index'));

        // Assert

        $response->assertRedirect(route('login'));
    }

    public function test_it_displays_logout_action_for_authenticated_users(): void
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

        $user = User::query()->where('email', 'alpha@example.com')->firstOrFail();

        // Act

        $response = $this->actingAs($user, 'web')->get(route('users.index'));

        // Assert

        $response
            ->assertOk()
            ->assertSee('You are signed in.')
            ->assertSee('Logout')
            ->assertDontSee('password-alpha')
            ->assertDontSee('password-beta');
    }

    public function test_it_integrates(): void
    {
        // Arrange

        config()->set('demo-users.users', [
            [
                'name' => 'Alpha User',
                'email' => 'alpha@example.com',
                'password' => 'password-alpha',
            ],
        ]);

        $this->seed(DemoUsersSeeder::class);

        $user = User::query()->where('email', 'alpha@example.com')->firstOrFail();

        // Act

        $response = $this->actingAs($user, 'web')->get(route('users.index'));

        // Assert

        $response
            ->assertOk()
            ->assertSee('Logout');
    }
}
