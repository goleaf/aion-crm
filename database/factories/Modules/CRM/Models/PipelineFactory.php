<?php

namespace Database\Factories\Modules\CRM\Models;

use App\Modules\CRM\Models\Pipeline;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pipeline>
 */
class PipelineFactory extends Factory
{
    /** @var class-string<Pipeline> */
    protected $model = Pipeline::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'position' => fake()->numberBetween(1, 10),
            'is_default' => false,
        ];
    }

    public function default(): self
    {
        return $this->state(fn (): array => [
            'name' => 'Default Sales Pipeline',
            'position' => 1,
            'is_default' => true,
        ]);
    }
}
