<?php

namespace App\Http\Controllers;

use App\Actions\CreateNextTaskOccurrence;
use App\Enums\RecurrenceType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function create(Request $request, Project $project): View
    {
        $this->authorize('createTask', $project);

        return view('tasks.create', $this->formData($request, $project));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('createTask', $project);
        $validated = $this->validatedTask($request, $project);

        DB::transaction(function () use ($request, $project, $validated): void {
            $task = $project->tasks()->create([
                ...Arr::except($validated, ['assignee_ids', 'subtasks']),
                'created_by' => $request->user()->id,
            ]);

            $task->assignees()->attach($validated['assignee_ids']);
            $this->syncSubtasks($task, $validated['subtasks']);
        });

        return redirect()->route('projects.show', $project)->with('status', 'Tarea creada.');
    }

    public function edit(Request $request, Project $project, Task $task): View
    {
        $this->ensureTaskBelongsToProject($task, $project);
        $this->authorize('update', $task);
        $task->load(['assignees', 'subtasks']);

        return view('tasks.edit', [
            ...$this->formData($request, $project),
            'task' => $task,
        ]);
    }

    public function update(Request $request, Project $project, Task $task): RedirectResponse
    {
        $this->ensureTaskBelongsToProject($task, $project);
        $this->authorize('update', $task);
        $validated = $this->validatedTask($request, $project, $task);

        DB::transaction(function () use ($task, $validated): void {
            $task->update(Arr::except($validated, ['assignee_ids', 'subtasks']));
            $task->assignees()->sync($validated['assignee_ids']);
            $this->syncSubtasks($task, $validated['subtasks']);
        });

        return redirect()->route('projects.show', $project)->with('status', 'Tarea actualizada.');
    }

    public function updateStatus(
        Request $request,
        Project $project,
        Task $task,
        CreateNextTaskOccurrence $createNextOccurrence
    ): RedirectResponse {
        $this->ensureTaskBelongsToProject($task, $project);
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TaskStatus::class)],
        ]);

        $task->update(['status' => $validated['status']]);
        $createNextOccurrence->handle($task->fresh());

        return back()->with('status', 'Estado actualizado.');
    }

    public function updateCompletion(
        Request $request,
        Project $project,
        Task $task,
        CreateNextTaskOccurrence $createNextOccurrence
    ): RedirectResponse {
        $this->ensureTaskBelongsToProject($task, $project);
        $this->authorize('update', $task);
        $request->validate(['completed' => ['required', 'boolean']]);

        $status = $request->boolean('completed')
            ? TaskStatus::Completed
            : TaskStatus::NotStarted;

        $task->update(['status' => $status]);

        if ($status === TaskStatus::Completed) {
            $createNextOccurrence->handle($task->fresh());
        }

        return back()->with('status', $status === TaskStatus::Completed ? 'Tarea completada.' : 'Tarea reabierta.');
    }

    public function destroy(Request $request, Project $project, Task $task): RedirectResponse
    {
        $this->ensureTaskBelongsToProject($task, $project);
        $this->authorize('delete', $task);
        $task->update(['deleted_by' => $request->user()->id]);
        $task->delete();

        return back()->with('status', 'Tarea enviada a la papelera.');
    }

    public function trash(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        return view('tasks.trash', [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'tasks' => $project->tasks()->onlyTrashed()->with('assignees')->latest('deleted_at')->get(),
        ]);
    }

    public function restore(Project $project, int $task): RedirectResponse
    {
        $taskModel = Task::onlyTrashed()->findOrFail($task);
        $this->ensureTaskBelongsToProject($taskModel, $project);
        $this->authorize('restore', $taskModel);
        $taskModel->restore();
        $taskModel->update(['deleted_by' => null]);

        return back()->with('status', 'Tarea restaurada.');
    }

    /** @return array<string, mixed> */
    private function formData(Request $request, Project $project): array
    {
        return [
            'project' => $project->load('members'),
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
            'recurrences' => RecurrenceType::cases(),
        ];
    }

    /** @return array<string, mixed> */
    private function validatedTask(Request $request, Project $project, ?Task $task = null): array
    {
        $subtasks = collect($request->input('subtasks', []))
            ->map(fn ($subtask) => [
                'id' => isset($subtask['id']) && $subtask['id'] !== '' ? (int) $subtask['id'] : null,
                'title' => trim((string) ($subtask['title'] ?? '')),
                'completed' => filter_var($subtask['completed'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ])
            ->filter(fn ($subtask) => $subtask['title'] !== '')
            ->values()
            ->all();

        $request->merge(['subtasks' => $subtasks]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'due_at' => ['nullable', 'date'],
            'show_in_calendar' => ['nullable', 'boolean'],
            'priority' => ['nullable', Rule::enum(TaskPriority::class)],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'recurrence' => ['nullable', Rule::enum(RecurrenceType::class)],
            'recurrence_ends_at' => ['nullable', 'date'],
            'assignee_ids' => ['required', 'array', 'min:1'],
            'assignee_ids.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'subtasks' => ['array', 'max:50'],
            'subtasks.*.id' => [
                'nullable',
                'integer',
                Rule::exists('subtasks', 'id')->where(fn ($query) => $query->where('task_id', $task?->id ?? 0)),
            ],
            'subtasks.*.title' => ['required', 'string', 'max:255'],
            'subtasks.*.completed' => ['boolean'],
        ]);

        if (($request->boolean('show_in_calendar') || ! empty($validated['recurrence'])) && empty($validated['due_at'])) {
            throw ValidationException::withMessages([
                'due_at' => 'La fecha límite es obligatoria para mostrar o repetir la tarea.',
            ]);
        }

        if (! empty($validated['recurrence_ends_at']) && empty($validated['recurrence'])) {
            throw ValidationException::withMessages([
                'recurrence_ends_at' => 'Selecciona una frecuencia antes de indicar su final.',
            ]);
        }

        if (! empty($validated['due_at']) && ! empty($validated['recurrence_ends_at'])
            && $validated['recurrence_ends_at'] < substr($validated['due_at'], 0, 10)) {
            throw ValidationException::withMessages([
                'recurrence_ends_at' => 'El final de la recurrencia no puede ser anterior a la fecha límite.',
            ]);
        }

        $assigneeIds = array_unique(array_map('intval', $validated['assignee_ids']));
        $memberCount = $project->members()->whereKey($assigneeIds)->count();

        if ($memberCount !== count($assigneeIds)) {
            throw ValidationException::withMessages([
                'assignee_ids' => 'Todas las personas asignadas deben pertenecer al proyecto.',
            ]);
        }

        $validated['assignee_ids'] = $assigneeIds;
        $validated['show_in_calendar'] = $request->boolean('show_in_calendar');
        $validated['priority'] = $validated['priority'] ?: null;
        $validated['recurrence'] = $validated['recurrence'] ?: null;
        $validated['recurrence_ends_at'] = $validated['recurrence_ends_at'] ?: null;

        return $validated;
    }

    /** @param array<int, array{id: ?int, title: string, completed: bool}> $subtasks */
    private function syncSubtasks(Task $task, array $subtasks): void
    {
        $retainedIds = [];

        foreach ($subtasks as $position => $subtask) {
            $attributes = [
                'title' => $subtask['title'],
                'is_completed' => $subtask['completed'],
                'sort_order' => $position,
            ];

            if ($subtask['id']) {
                $task->subtasks()->whereKey($subtask['id'])->update($attributes);
                $retainedIds[] = $subtask['id'];
            } else {
                $retainedIds[] = $task->subtasks()->create($attributes)->id;
            }
        }

        $task->subtasks()->whereNotIn('id', $retainedIds)->delete();
    }

    private function ensureTaskBelongsToProject(Task $task, Project $project): void
    {
        abort_unless($task->project_id === $project->id, 404);
    }
}
