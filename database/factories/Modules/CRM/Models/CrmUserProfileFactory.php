<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Enums\CrmRoleEnum;
use App\Modules\CRM\Foundation\Enums\RecordVisibilityEnum;
use App\Modules\CRM\Models\CrmTeam;
use App\Modules\CRM\Models\CrmUserProfile;
use App\Modules\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmUserProfile>
 */
class CrmUserProfileFactory extends Factory
{
    protected $model = CrmUserProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'primary_team_id' => CrmTeam::factory(),
            'role' => CrmRoleEnum::Rep,
            'record_visibility' => RecordVisibilityEnum::Own,
            'is_active' => true,
            'deactivated_at' => null,
        ];
    }

    public function admin(): self
    {
        return $this->state(fn (array $attributes): array => [
            'role' => CrmRoleEnum::Admin,
            'record_visibility' => RecordVisibilityEnum::All,
        ]);
    }

    public function manager(): self
    {
        return $this->state(fn (array $attributes): array => [
            'role' => CrmRoleEnum::Manager,
            'record_visibility' => RecordVisibilityEnum::Team,
        ]);
    }

    public function rep(): self
    {
        return $this->state(fn (array $attributes): array => [
            'role' => CrmRoleEnum::Rep,
            'record_visibility' => RecordVisibilityEnum::Own,
        ]);
    }

    public function viewer(): self
    {
        return $this->state(fn (array $attributes): array => [
            'role' => CrmRoleEnum::Viewer,
            'record_visibility' => RecordVisibilityEnum::Own,
        ]);
    }

    public function inactive(): self
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
            'deactivated_at' => now(),
        ]);
    }
}
