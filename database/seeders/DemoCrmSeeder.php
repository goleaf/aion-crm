<?php

namespace Database\Seeders;

use App\Modules\CRM\Enums\CrmRoleEnum;
use App\Modules\CRM\Models\Account;
use App\Modules\CRM\Models\Contact;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\CRM\Models\Pipeline;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Seeder;

class DemoCrmSeeder extends Seeder
{
    public function run(): void
    {
        $team = CrmTeam::query()->firstOrCreate([
            'name' => 'Revenue Team',
        ]);

        Pipeline::query()->firstOrCreate(
            ['name' => 'Default Sales Pipeline'],
            [
                'position' => 1,
                'is_default' => true,
            ],
        );

        User::query()
            ->orderBy('id')
            ->get()
            ->each(function (User $user, int $index) use ($team): void {
                $role = $index === 0 ? CrmRoleEnum::Admin : CrmRoleEnum::Rep;

                CrmUserProfile::query()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'primary_team_id' => $team->id,
                        'role' => $role,
                        'record_visibility' => $role->defaultRecordVisibility(),
                        'is_active' => true,
                        'deactivated_at' => null,
                    ],
                );

                $account = Account::query()->firstOrCreate(
                    [
                        'owner_id' => $user->id,
                        'name' => "{$user->name} Account",
                    ],
                    [
                        'team_id' => $team->id,
                        'type' => 'customer',
                        'industry' => 'technology',
                        'website' => 'https://example.test',
                        'email' => $user->email,
                        'billing_address' => [
                            'line_1' => 'Demo Street 1',
                            'line_2' => null,
                            'city' => 'Vilnius',
                            'state_or_province' => null,
                            'postal_code' => '01100',
                            'country_code' => 'LT',
                        ],
                        'tags' => ['demo'],
                    ],
                );

                Contact::query()->firstOrCreate(
                    [
                        'account_id' => $account->getKey(),
                        'email' => $user->email,
                    ],
                    [
                        'owner_id' => $user->id,
                        'team_id' => $team->id,
                        'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                        'last_name' => explode(' ', $user->name)[1] ?? 'Contact',
                        'preferred_channel' => 'email',
                    ],
                );
            });
    }
}
