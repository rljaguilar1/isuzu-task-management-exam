<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
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
        $tasks = [
            [
                'title' => 'Review project requirements',
                'description' => 'Confirm that every required feature is covered before submission.',
            ],
            [
                'title' => 'Test task permissions',
                'description' => 'Check that users can only view and update their own tasks.',
            ],
            [
                'title' => 'Prepare the project README',
                'description' => 'Document the setup steps, test accounts, and local development commands.',
            ],
            [
                'title' => 'Run the final test suite',
                'description' => 'Run all automated tests and resolve any failures before submission.',
            ],
        ];
        $task = fake()->randomElement($tasks);

        return [
            'user_id' => User::factory(),
            'title' => $task['title'],
            'description' => $task['description'],
            'is_completed' => fake()->boolean(30),
        ];
    }
}
