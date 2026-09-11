<?php

namespace Tests\Feature;

use App\Enums\EventColor;
use App\Enums\ProjectRole;
use App\Enums\TaskStatus;
use App\Models\Event;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Support\ColorPalette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarAndEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_page_contains_its_visible_mount_point_and_feed(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->get(route('calendar.index', $project))
            ->assertOk()
            ->assertSee('data-calendar', false)
            ->assertSee(route('calendar.feed', $project), false)
            ->assertSee('Cargando calendario');
    }

    public function test_a_member_can_create_a_timed_or_all_day_event(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->post(route('events.store', $project), [
            'title' => 'Revisión semanal',
            'start_date' => '2026-09-10',
            'start_time' => '10:30',
            'color' => EventColor::Violet->value,
        ])->assertRedirect(route('calendar.index', $project));

        $this->actingAs($owner)->post(route('events.store', $project), [
            'title' => 'Día de planificación',
            'start_date' => '2026-09-14',
            'all_day' => '1',
        ])->assertRedirect(route('calendar.index', $project));

        $this->assertDatabaseCount('events', 2);
        $this->assertTrue(Event::where('title', 'Día de planificación')->firstOrFail()->all_day);
        $this->assertSame(EventColor::Violet, Event::where('title', 'Revisión semanal')->firstOrFail()->color);
        $this->assertSame(EventColor::Blue, Event::where('title', 'Día de planificación')->firstOrFail()->color);
    }

    public function test_a_timed_event_requires_a_start_time(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->post(route('events.store', $project), [
            'title' => 'Revisión semanal',
            'start_date' => '2026-09-10',
        ])->assertSessionHasErrors('start_time');
    }

    public function test_an_event_rejects_a_color_outside_the_available_palette(): void
    {
        [$owner, $project] = $this->createProject();

        $this->actingAs($owner)->post(route('events.store', $project), [
            'title' => 'Evento inválido',
            'start_date' => '2026-09-10',
            'all_day' => '1',
            'color' => 'transparent',
        ])->assertSessionHasErrors('color');

        $this->assertDatabaseCount('events', 0);
    }

    public function test_calendar_contains_all_events_and_only_visible_dated_tasks(): void
    {
        [$owner, $project] = $this->createProject();
        $event = Event::create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'title' => 'Demo de producto',
            'starts_at' => '2026-09-10 16:30:00',
        ]);
        $visibleTask = $this->createTask($owner, $project, 'Tarea visible', true);
        $this->createTask($owner, $project, 'Tarea oculta', false);

        $response = $this->actingAs($owner)->getJson(route('calendar.feed', [
            'project' => $project,
            'start' => '2026-09-01',
            'end' => '2026-10-01',
        ]));

        $response->assertOk()
            ->assertJsonFragment(['id' => 'event-'.$event->id, 'title' => 'Demo de producto'])
            ->assertJsonFragment(['id' => 'task-'.$visibleTask->id, 'title' => 'Tarea visible'])
            ->assertJsonMissing(['title' => 'Tarea oculta']);

        $entries = collect($response->json());
        $this->assertSame(ColorPalette::GENERIC, $entries->firstWhere('id', 'event-'.$event->id)['backgroundColor']);
        $this->assertSame($owner->displayColor(), $entries->firstWhere('id', 'task-'.$visibleTask->id)['backgroundColor']);
    }

    public function test_the_selected_event_color_is_used_by_the_calendar(): void
    {
        [$owner, $project] = $this->createProject();
        $event = Event::create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'title' => 'Evento violeta',
            'starts_at' => '2026-09-10 16:30:00',
            'color' => EventColor::Violet,
        ]);

        $entries = collect($this->actingAs($owner)->getJson(route('calendar.feed', [
            'project' => $project,
            'start' => '2026-09-01',
            'end' => '2026-10-01',
        ]))->assertOk()->json());

        $calendarEvent = $entries->firstWhere('id', 'event-'.$event->id);
        $this->assertSame(EventColor::Violet->hex(), $calendarEvent['backgroundColor']);
        $this->assertSame(EventColor::Violet->hex(), $calendarEvent['borderColor']);
    }

    public function test_shared_and_terminal_tasks_have_distinct_calendar_presentations(): void
    {
        [$owner, $project] = $this->createProject();
        $member = User::factory()->create();
        $project->members()->attach($member->id, ['role' => ProjectRole::Member->value]);

        $shared = $this->createTask($owner, $project, 'Tarea compartida', true);
        $shared->assignees()->sync([$owner->id, $member->id]);

        $completed = $this->createTask($owner, $project, 'Tarea completada', true);
        $completed->update(['status' => TaskStatus::Completed]);

        $cancelled = $this->createTask($owner, $project, 'Tarea cancelada', true);
        $cancelled->update(['status' => TaskStatus::Cancelled]);

        $entries = collect($this->actingAs($owner)->getJson(route('calendar.feed', [
            'project' => $project,
            'start' => '2026-09-01',
            'end' => '2026-10-01',
        ]))->assertOk()->json());

        $this->assertSame(ColorPalette::GENERIC, $entries->firstWhere('id', 'task-'.$shared->id)['backgroundColor']);
        $this->assertSame(ColorPalette::COMPLETED_BACKGROUND, $entries->firstWhere('id', 'task-'.$completed->id)['backgroundColor']);
        $this->assertSame($owner->displayColor(), $entries->firstWhere('id', 'task-'.$completed->id)['borderColor']);
        $this->assertContains('calendar-entry--completed', $entries->firstWhere('id', 'task-'.$completed->id)['classNames']);
        $this->assertSame(ColorPalette::CANCELLED_BACKGROUND, $entries->firstWhere('id', 'task-'.$cancelled->id)['backgroundColor']);
        $this->assertContains('calendar-entry--cancelled', $entries->firstWhere('id', 'task-'.$cancelled->id)['classNames']);
    }

    public function test_an_event_can_be_moved_to_trash_and_restored(): void
    {
        [$owner, $project] = $this->createProject();
        $event = Event::create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'title' => 'Demo',
            'starts_at' => '2026-09-10 16:30:00',
        ]);

        $this->actingAs($owner)->delete(route('events.destroy', [$project, $event]));
        $this->assertSoftDeleted($event);

        $this->actingAs($owner)->post(route('events.restore', [$project, $event->id]));
        $this->assertNotSoftDeleted($event);
    }

    private function createProject(): array
    {
        $owner = User::factory()->create();
        $project = Project::create(['name' => 'TaskFlow', 'created_by' => $owner->id]);
        $project->members()->attach($owner->id, ['role' => ProjectRole::Owner->value]);

        return [$owner, $project];
    }

    private function createTask(User $owner, Project $project, string $name, bool $visible): Task
    {
        $task = Task::create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'name' => $name,
            'description' => 'Descripción de prueba.',
            'due_at' => '2026-09-12 12:00:00',
            'show_in_calendar' => $visible,
            'status' => TaskStatus::NotStarted,
        ]);
        $task->assignees()->attach($owner->id);

        return $task;
    }
}
