<?php

namespace Tests\App\Livewire\Users;

use App\Livewire\Users\UsersTablePage;
use App\Modules\Shared\Models\User;
use Database\Seeders\DemoUsersSeeder;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Tests\Support\TestCases\FunctionalTestCase;

#[CoversClass(UsersTablePage::class)]
#[Group('authentication')]
class UsersTablePageFunctionalTest extends FunctionalTestCase
{
    public function test_it_renders_seeded_user_credentials(): void
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

        $this->actingAs($user, 'web');

        // Act

        $component = Livewire::test(UsersTablePage::class);

        // Assert

        $component
            ->assertSee('Alpha User')
            ->assertSee('Beta User')
            ->assertSee('password-alpha')
            ->assertSee('password-beta');
    }
}
