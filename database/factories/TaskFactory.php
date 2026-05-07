<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
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
        return [
            'board_id' => Board::factory(),
            'workspace_id' => function (array $attributes) {
                return Board::find($attributes['board_id'])->project->workspace_id;
            },
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->optional()->paragraph(),
            'position' => $this->faker->numberBetween(1, 20),
            'assigned_to' => User::factory(),
            'due_date' => $this->faker->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
