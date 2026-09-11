<?php

namespace Tests\Feature;

use App\Enums\ProjectRole;
use App\Enums\RecurrenceType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Support\ColorPalette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_member_can_create_a_task_for_one_or_more_project_members(): void
    {
        [$owner, $project] = $this->createProject();
        $member = User::factory()->create();
        $project->members()->attach($member->id, ['role' => ProjectRole::Member->value]);

        $this->actingAs($member)->post(route('tasks.store', $project), [
            ...$this->validTaskData([$owner->id, $member->id]),
            'show_in_calendar' => '1',
        ])->assertRedirect(route('projects.show', $project));

        $task = Task::firstOrFail();
        $this->assertSame(2, $task->assignees()->count());
        $this->assertTrue($task->show_in_calendar);
    }

    public function test_a_task_can_be_created_with_subtasks_and_each_subtask_can_be_completed(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->post(route('tasks.store', $project), [
            ...$this->validTaskData([$owner->id]),
            'subtasks' => [
                ['title' => 'Añadir CSS', 'completed' => '0'],
                ['title' => 'Crear contenedor Docker', 'completed' => '0'],
            ],
        ])->assertRedirect(route('projects.show', $project));

        $task = Task::firstOrFail();
        $this->assertSame(['Añadir CSS', 'Crear contenedor Docker'], $task->subtasks()->pluck('title')->all());

        $subtask = $task->subtasks()->firstOrFail();
        $this->actingAs($owner)->patch(route('subtasks.update', [$project, $task, $subtask]), [
            'completed' => '1',
        ])->assertSessionHasNoErrors();

        $this->assertTrue($subtask->fresh()->is_completed);
    }

    public function test_task_checkbox_completes_and_reopens_a_task(): void
    {
        [$owner, $project] = $this->createProject();
        $task = $this->createTask($owner, $project);

        $this->actingAs($owner)->patch(route('tasks.completion', [$project, $task]), [
            'completed' => '1',
        ])->assertSessionHas('status');

        $this->assertSame(TaskStatus::Completed, $task->fresh()->status);

        $this->actingAs($owner)->patch(route('tasks.completion', [$project, $task]), [
            'completed' => '0',
        ])->assertSessionHas('status');

        $this->assertSame(TaskStatus::NotStarted, $task->fresh()->status);
    }

    public function test_tasks_use_the_default_status_and_priority_order(): void
    {
        [$owner, $project] = $this->createProject();
        $this->createTask($owner, $project, ['name' => 'Cancelada urgente', 'status' => TaskStatus::Cancelled, 'priority' => TaskPriority::Urgent]);
        $this->createTask($owner, $project, ['name' => 'Pendiente baja', 'status' => TaskStatus::NotStarted, 'priority' => TaskPriority::Low]);
        $this->createTask($owner, $project, ['name' => 'Completada urgente', 'status' => TaskStatus::Completed, 'priority' => TaskPriority::Urgent]);
        $this->createTask($owner, $project, ['name' => 'En progreso media', 'status' => TaskStatus::InProgress, 'priority' => TaskPriority::Medium]);
        $this->createTask($owner, $project, ['name' => 'En progreso urgente', 'status' => TaskStatus::InProgress, 'priority' => TaskPriority::Urgent]);

        $this->actingAs($owner)->get(route('projects.show', $project))
            ->assertOk()
            ->assertSeeInOrder([
                'task-group task-group--in_progress',
                'task-group task-group--not_started',
                'task-group task-group--completed',
                'task-group task-group--cancelled',
            ], false)
            ->assertSeeInOrder([
                'En progreso urgente',
                'En progreso media',
                'Pendiente baja',
                'Completada urgente',
                'Cancelada urgente',
            ]);
    }

    public function test_changing_status_moves_a_task_to_its_collapsible_group(): void
    {
        [$owner, $project] = $this->createProject();
        $task = $this->createTask($owner, $project, ['status' => TaskStatus::InProgress]);

        $this->actingAs($owner)->get(route('projects.show', $project))
            ->assertSee('task-group task-group--in_progress', false)
            ->assertDontSee('task-group task-group--completed', false);

        $this->actingAs($owner)->patch(route('tasks.status', [$project, $task]), [
            'status' => TaskStatus::Completed->value,
        ])->assertSessionHasNoErrors();

        $this->actingAs($owner)->get(route('projects.show', $project))
            ->assertDontSee('task-group task-group--in_progress', false)
            ->assertSee('task-group task-group--completed', false)
            ->assertSee('task-row task-row--completed', false);
    }

    public function test_task_rows_identify_single_and_shared_assignments_by_color(): void
    {
        [$owner, $project] = $this->createProject();
        $member = User::factory()->create();
        $project->members()->attach($member->id, ['role' => ProjectRole::Member->value]);

        $single = $this->createTask($owner, $project, ['name' => 'Tarea individual']);
        $shared = $this->createTask($owner, $project, ['name' => 'Tarea compartida']);
        $shared->assignees()->sync([$owner->id, $member->id]);

        $this->actingAs($owner)->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('style="--assignee-color: '.$single->fresh('assignees')->assignmentColor().'"', false)
            ->assertSee('style="--assignee-color: '.ColorPalette::GENERIC.'"', false)
            ->assertSee('style="--avatar-color: '.$owner->displayColor().'"', false)
            ->assertSee('style="--avatar-color: '.$member->displayColor().'"', false);
    }

    public function test_a_task_cannot_be_assigned_outside_its_project(): void
    {
        [$owner, $project] = $this->createProject();
        $outsider = User::factory()->create();

        $this->actingAs($owner)->post(route('tasks.store', $project), [
            ...$this->validTaskData([$outsider->id]),
        ])->assertSessionHasErrors('assignee_ids');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_calendar_visibility_and_recurrence_require_a_due_date(): void
    {
        [$owner, $project] = $this->createProject();
        $data = $this->validTaskData([$owner->id]);
        $data['due_at'] = null;
        $data['show_in_calendar'] = '1';
        $data['recurrence'] = RecurrenceType::Weekly->value;

        $this->actingAs($owner)->post(route('tasks.store', $project), $data)
            ->assertSessionHasErrors('due_at');
    }

    public function test_completing_a_recurring_task_creates_one_successor_without_duplicates(): void
    {
        [$owner, $project] = $this->createProject();
        $task = $this->createTask($owner, $project, [
            'due_at' => '2026-09-10 09:30:00',
            'recurrence' => RecurrenceType::Weekly,
            'recurrence_ends_at' => '2026-10-01',
        ]);
        $task->subtasks()->create(['title' => 'Paso recurrente', 'is_completed' => true]);

        $this->actingAs($owner)->patch(route('tasks.status', [$project, $task]), [
            'status' => TaskStatus::Completed->value,
        ])->assertSessionHasNoErrors();

        $next = $task->fresh()->nextOccurrence;
        $this->assertNotNull($next);
        $this->assertSame('2026-09-17 09:30:00', $next->due_at->format('Y-m-d H:i:s'));
        $this->assertSame(TaskStatus::NotStarted, $next->status);
        $this->assertSame([$owner->id], $next->assignees()->pluck('users.id')->all());
        $this->assertSame('Paso recurrente', $next->subtasks()->firstOrFail()->title);
        $this->assertFalse($next->subtasks()->firstOrFail()->is_completed);

        $this->actingAs($owner)->patch(route('tasks.status', [$project, $task]), [
            'status' => TaskStatus::Cancelled->value,
        ]);

        $this->assertDatabaseCount('tasks', 2);
    }

    public function test_a_recurring_task_stops_after_its_optional_end_date(): void
    {
        [$owner, $project] = $this->createProject();
        $task = $this->createTask($owner, $project, [
            'due_at' => '2026-09-10 09:30:00',
            'recurrence' => RecurrenceType::Weekly,
            'recurrence_ends_at' => '2026-09-12',
        ]);

        $this->actingAs($owner)->patch(route('tasks.status', [$project, $task]), [
            'status' => TaskStatus::Completed->value,
        ]);

        $this->assertDatabaseCount('tasks', 1);
    }

    public function test_a_task_can_be_moved_to_trash_and_restored(): void
    {
        [$owner, $project] = $this->createProject();
        $task = $this->createTask($owner, $project);

        $this->actingAs($owner)->delete(route('tasks.destroy', [$project, $task]))
            ->assertSessionHas('status');

        $this->assertSoftDeleted($task);

        $this->actingAs($owner)->post(route('tasks.restore', [$project, $task->id]))
            ->assertSessionHas('status');

        $this->assertNotSoftDeleted($task);
    }

    /** @return array{User, Project} */
    private function createProject(): array
    {
        $owner = User::factory()->create();
        $project = Project::create(['name' => 'TaskFlow', 'created_by' => $owner->id]);
        $project->members()->attach($owner->id, ['role' => ProjectRole::Owner->value]);

        return [$owner, $project];
    }

    /** @param array<int> $assignees */
    private function validTaskData(array $assignees): array
    {
        return [
            'name' => 'Preparar calendario',
            'description' => 'Integrar las tareas visibles con el calendario.',
            'due_at' => '2026-09-10T09:30',
            'priority' => TaskPriority::High->value,
            'status' => TaskStatus::NotStarted->value,
            'recurrence' => '',
            'recurrence_ends_at' => '',
            'assignee_ids' => $assignees,
        ];
    }

    private function createTask(User $owner, Project $project, array $overrides = []): Task
    {
        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'name' => 'Preparar calendario',
            'description' => 'Descripción de prueba.',
            'status' => TaskStatus::NotStarted,
            'priority' => TaskPriority::High,
            ...$overrides,
        ]);
        $task->assignees()->attach($owner->id);

        return $task;
    }
}
