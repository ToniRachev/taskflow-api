<?php

namespace Database\Factories\V1;

use App\Enums\V1\TaskPriorityEnum;
use App\Enums\V1\TaskStatusEnum;
use App\Enums\V1\TaskTypeEnum;
use App\Models\V1\Project;
use App\Models\V1\Task;
use App\Models\V1\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use function fake;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\V1\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $project = Project::factory()->create();
        $referenceNumber = fake()->numberBetween(1, 1000);

        return [
            'uuid' => fake()->uuid(),
            'reference_number' => $referenceNumber,
            'reference' => $project->key . '-' . $referenceNumber,
            'project_id' => $project->id,
            'assignee_id' => User::factory(),
            'reporter_id' => User::factory(),
            'parent_id' => null,
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(TaskTypeEnum::cases()),
            'status' => fake()->randomElement(TaskStatusEnum::cases()),
            'priority' => fake()->randomElement(TaskPriorityEnum::cases()),
            'story_points' => fake()->numberBetween(1, 10),
            'order' => fake()->numberBetween(1, 100),
            'due_date' => null,
            'completed_at' => null,
        ];
    }
}
