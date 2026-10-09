<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\App\Resources\Tasks\Pages\CreateTask as CreateUserTask;
use App\Filament\App\Resources\Tasks\Pages\EditTask as EditUserTask;
use App\Filament\App\Resources\Tasks\TaskResource as UserTaskResource;
use App\Filament\Resources\Tasks\Pages\CreateTask as CreateAdminTask;
use App\Models\Task;
use App\Models\User;
use Filament\Enums\UserMenuPosition;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class TaskAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_shared_login_page(): void
    {
        $this->get(route('filament.app.resources.tasks.index'))
            ->assertRedirect(route('login'));

        $this->get(route('filament.admin.resources.tasks.index'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_users_can_access_only_the_user_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('filament.app.resources.tasks.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('filament.admin.resources.tasks.index'))
            ->assertForbidden();
    }

    public function test_administrators_can_access_only_the_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.tasks.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('filament.app.resources.tasks.index'))
            ->assertForbidden();
    }

    public function test_regular_users_only_query_their_own_tasks(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownTask = Task::factory()->for($user)->create();
        Task::factory()->for($otherUser)->create();

        $this->actingAs($user);

        $this->assertSame([$ownTask->id], UserTaskResource::getEloquentQuery()->pluck('id')->all());
    }

    public function test_regular_users_cannot_open_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherTask = Task::factory()->create();

        $this->actingAs($user)
            ->get(route('filament.app.resources.tasks.edit', $otherTask))
            ->assertNotFound();
    }

    public function test_regular_users_can_open_their_own_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Review the task details',
        ]);

        $this->actingAs($user)
            ->get(route('filament.app.resources.tasks.view', $task))
            ->assertOk()
            ->assertSeeText('Review the task details');
    }

    public function test_task_policy_enforces_ownership(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($owner)->create();

        $this->assertTrue($owner->can('update', $task));
        $this->assertTrue($owner->can('delete', $task));
        $this->assertFalse($otherUser->can('update', $task));
        $this->assertFalse($otherUser->can('delete', $task));
    }

    public function test_administrators_can_manage_any_task(): void
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create();

        $this->assertTrue($admin->can('view', $task));
        $this->assertTrue($admin->can('update', $task));
        $this->assertTrue($admin->can('delete', $task));

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.tasks.edit', $task))
            ->assertOk();
    }

    public function test_new_users_receive_the_regular_user_role_by_default(): void
    {
        $user = User::factory()->create();

        $this->assertSame(UserRole::User, $user->role);
        $this->assertFalse($user->isAdmin());
    }

    public function test_only_the_user_panel_offers_public_registration(): void
    {
        $this->get(route('filament.app.auth.register'))->assertOk();

        $this->assertTrue(Route::has('login'));
        $this->assertFalse(Route::has('filament.app.auth.login'));
        $this->assertFalse(Route::has('filament.admin.auth.register'));
        $this->assertFalse(Route::has('filament.admin.auth.login'));
    }

    public function test_both_panels_place_the_user_menu_in_the_sidebar(): void
    {
        $this->assertSame(UserMenuPosition::Sidebar, Filament::getPanel('app')->getUserMenuPosition());
        $this->assertSame(UserMenuPosition::Sidebar, Filament::getPanel('admin')->getUserMenuPosition());
    }

    public function test_shared_login_redirects_regular_users_to_the_user_panel(): void
    {
        $user = User::factory()->create([
            'email' => 'member@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.authenticate'), [
            'email' => 'member@example.com',
            'password' => 'password',
        ])->assertRedirect('/app');

        $this->assertAuthenticatedAs($user);
    }

    public function test_shared_login_redirects_administrators_to_the_admin_panel(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'administrator@example.com',
            'password' => 'password',
        ]);

        $this->post(route('login.authenticate'), [
            'email' => 'administrator@example.com',
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
    }

    public function test_shared_login_rejects_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'member@example.com']);

        $this->post(route('login.authenticate'), [
            'email' => 'member@example.com',
            'password' => 'incorrect-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_user_created_task_is_assigned_to_that_user(): void
    {
        $user = User::factory()->create();
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $this->actingAs($user);

        Livewire::test(CreateUserTask::class)
            ->fillForm([
                'title' => 'Prepare submission',
                'description' => 'Review the project before submitting.',
                'is_completed' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Prepare submission',
        ]);
    }

    public function test_users_are_redirected_to_the_task_view_after_saving_an_edit(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $this->actingAs($user);

        Livewire::test(EditUserTask::class, ['record' => $task->getRouteKey()])
            ->fillForm([
                'title' => 'Updated task title',
                'description' => 'Updated task description.',
                'is_completed' => true,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.app.resources.tasks.view', $task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated task title',
        ]);
    }

    public function test_an_administrator_can_assign_a_task_to_any_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->actingAs($admin);

        Livewire::test(CreateAdminTask::class)
            ->fillForm([
                'user_id' => $user->id,
                'title' => 'Assigned by administrator',
                'description' => null,
                'is_completed' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Assigned by administrator',
        ]);
    }
}
