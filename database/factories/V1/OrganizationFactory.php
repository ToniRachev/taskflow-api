<?php

namespace Database\Factories\V1;

use App\Enums\V1\PlanEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Organization>
 */
class OrganizationFactory extends Factory
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
            'name' => fake()->company(),
            'slug' => fake()->slug(),
            'logo_url' => fake()->imageUrl(200, 200, 'business', true),
            'plan' => fake()->randomElement(PlanEnum::cases())->value,
            'is_active' => true,
            'settings' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
