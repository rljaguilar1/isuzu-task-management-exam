<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'System Administrator',
            'password' => 'password',
            'role' => UserRole::Admin,
        ]);

        $user = User::query()->updateOrCreate([
            'email' => 'user@example.com',
        ], [
            'name' => 'Regular User',
            'password' => 'password',
            'role' => UserRole::User,
        ]);

        Task::query()->delete();

        $user->tasks()->createMany([
            [
                'title' => 'Review project requirements',
                'description' => 'Confirm that every required feature is covered before submission.',
                'is_completed' => true,
            ],
            [
                'title' => 'Test task permissions',
                'description' => 'Check that users can only view and update their own tasks.',
                'is_completed' => false,
            ],
            [
                'title' => 'Prepare the project README',
                'description' => 'Document the setup steps, test accounts, and local development commands.',
                'is_completed' => false,
            ],
            [
                'title' => 'Run the final test suite',
                'description' => 'Run all automated tests and resolve any failures before submission.',
                'is_completed' => false,
            ],
        ]);

        $admin->tasks()->create([
            'title' => 'Verify user task records',
            'description' => 'Review task ownership and completion status from the administration panel.',
            'is_completed' => false,
        ]);
    }
}
