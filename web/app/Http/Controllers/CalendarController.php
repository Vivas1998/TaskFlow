<?php

namespace App\Http\Controllers;

use App\Enums\EventColor;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Support\ColorPalette;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        return view('calendar.index', [
            'project' => $project->load('members'),
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'trashedEventCount' => $project->events()->onlyTrashed()->count(),
        ]);
    }

    public function feed(Request $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $start = $request->date('start');
        $end = $request->date('end');

        $events = $project->events()
            ->when($start, fn ($query) => $query->where(fn ($dates) => $dates->whereNull('ends_at')->where('starts_at', '>=', $start)->orWhere('ends_at', '>=', $start)))
            ->when($end, fn ($query) => $query->where('starts_at', '<', $end))
            ->get()
            ->map(fn ($event) => [
                'id' => 'event-'.$event->id,
                'title' => $event->title,
                'start' => $event->all_day ? $event->starts_at->toDateString() : $event->starts_at->toIso8601String(),
                'end' => $event->all_day && $event->ends_at ? $event->ends_at->copy()->addDay()->toDateString() : $event->ends_at?->toIso8601String(),
                'allDay' => $event->all_day,
                'backgroundColor' => ($event->color ?? EventColor::Slate)->hex(),
                'borderColor' => ($event->color ?? EventColor::Slate)->hex(),
                'textColor' => '#ffffff',
                'classNames' => ['calendar-entry', 'calendar-entry--event'],
                'extendedProps' => [
                    'kind' => 'event',
                    'assignmentLabel' => 'Todo el proyecto',
                    'editUrl' => route('events.edit', [$project, $event]),
                ],
            ]);

        $tasks = $project->tasks()
            ->where('show_in_calendar', true)
            ->whereNotNull('due_at')
            ->with('assignees')
            ->when($start, fn ($query) => $query->where('due_at', '>=', $start))
            ->when($end, fn ($query) => $query->where('due_at', '<', $end))
            ->get()
            ->map(function ($task) use ($project) {
                $assignmentColor = $task->assignmentColor();

                return [
                    'id' => 'task-'.$task->id,
                    'title' => $task->name,
                    'start' => $task->due_at->toIso8601String(),
                    'allDay' => false,
                    'backgroundColor' => $this->taskBackgroundColor($task->status, $assignmentColor),
                    'borderColor' => $assignmentColor,
                    'textColor' => $this->taskTextColor($task->status),
                    'classNames' => ['calendar-entry', 'calendar-entry--task', 'calendar-entry--'.$task->status->value],
                    'extendedProps' => [
                        'kind' => 'task',
                        'assignmentLabel' => $task->assignmentLabel(),
                        'editUrl' => route('tasks.edit', [$project, $task]),
                    ],
                ];
            });

        return response()->json($events->concat($tasks)->values());
    }

    private function taskBackgroundColor(TaskStatus $status, string $assignmentColor): string
    {
        return match ($status) {
            TaskStatus::Completed => ColorPalette::COMPLETED_BACKGROUND,
            TaskStatus::Cancelled => ColorPalette::CANCELLED_BACKGROUND,
            default => $assignmentColor,
        };
    }

    private function taskTextColor(TaskStatus $status): string
    {
        return match ($status) {
            TaskStatus::Completed => ColorPalette::COMPLETED_TEXT,
            TaskStatus::Cancelled => ColorPalette::CANCELLED_TEXT,
            default => '#ffffff',
        };
    }
}
