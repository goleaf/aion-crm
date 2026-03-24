<?php

namespace Tests\App\Livewire\Auth;

use App\Livewire\Auth\LoginPage;
use App\Modules\Shared\Models\User;
use Database\Seeders\DemoUsersSeeder;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(LoginPage::class)]
#[Group('authentication')]
class LoginPageFunctionalTest extends FunctionalTestCase
{
    public function test_it_logs_in_with_valid_seeded_credentials(): void
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

        Livewire::test(LoginPage::class)
            ->set('email', 'alpha@example.com')
            ->set('password', 'password-alpha')
            ->call('login')
            ->assertRedirect(route('users.index'));

        // Assert

        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_it_rejects_invalid_credentials(): void
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

        // Act

        $component = Livewire::test(LoginPage::class)
            ->set('email', 'alpha@example.com')
            ->set('password', 'wrong-password')
            ->call('login');

        // Assert

        $component->assertHasErrors(['email']);

        $this->assertGuest('web');
    }
}
