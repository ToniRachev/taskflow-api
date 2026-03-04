<?php

namespace Database\Factories\V1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bio' => fake()->sentence(),
            'phone' => fake()->phoneNumber(),
            'github_url' => fake()->url(),
            'preferences' => [
                'theme' => fake()->randomElement(['light', 'dark']),
                'language' => fake()->randomElement(['en', 'es', 'fr']),
                'notifications' => true,
            ]
        ];
    }
}
