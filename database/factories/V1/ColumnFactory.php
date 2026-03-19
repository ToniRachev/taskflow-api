<?php

namespace Database\Factories\V1;

use App\Models\V1\Board;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Column>
 */
class ColumnFactory extends Factory
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
            'board_id' => Board::factory()->create(),
            'name' => fake()->sentence(),
            'color' => null,
            'order' => 0,
            'wip_limit' => 0
        ];
    }
}
