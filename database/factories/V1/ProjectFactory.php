<?php

namespace Database\Factories\V1;

use App\Enums\V1\ProjectStatusEnum;
use App\Enums\V1\ProjectVisibilityEnum;
use App\Models\V1\Organization;
use App\Models\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Project>
 */
class ProjectFactory extends Factory
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
            'name' => fake()->sentence(3),
            'key' => fake()->word(),
            'description' => fake()->sentence(20),
            'status' => fake()->randomElement(ProjectStatusEnum::cases())->value,
            'visibility' => fake()->randomElement(ProjectVisibilityEnum::cases())->value,
            'start_date' => $startDate = fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween($startDate, '+1 year')->format('Y-m-d'),
            'owner_id' => User::factory(),
            'organization_id' => Organization::factory(),
        ];
    }
}
