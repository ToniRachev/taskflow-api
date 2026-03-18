<?php

namespace Database\Factories\V1;

use App\Models\V1\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BoardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'project_id' => Project::factory()->create(),
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'is_default' => false,
        ];
    }
}
