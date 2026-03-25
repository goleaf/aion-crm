<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Models\CrmTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

class CrmTeamFactory extends Factory
{
    protected $model = CrmTeam::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
        ];
    }
}
