<?php

namespace App\Actions;

use App\Enums\RecurrenceType;
use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class CreateNextTaskOccurrence
{
    public function handle(Task $task): ?Task
    {
        return DB::transaction(function () use ($task): ?Task {
            $current = Task::query()->lockForUpdate()->findOrFail($task->id);

            if (! $current->recurrence || ! $current->due_at || ! $current->status->isTerminal()) {
                return null;
            }

            $existing = Task::withTrashed()->where('previous_occurrence_id', $current->id)->first();

            if ($existing) {
                return $existing;
            }

            $nextDueAt = match ($current->recurrence) {
                RecurrenceType::Daily => $current->due_at->copy()->addDay(),
                RecurrenceType::Weekly => $current->due_at->copy()->addWeek(),
                RecurrenceType::Monthly => $current->due_at->copy()->addMonthNoOverflow(),
                RecurrenceType::Yearly => $current->due_at->copy()->addYearNoOverflow(),
            };

            if ($current->recurrence_ends_at && $nextDueAt->copy()->startOfDay()->gt($current->recurrence_ends_at)) {
                return null;
            }

            $next = Task::create([
                'project_id' => $current->project_id,
                'created_by' => $current->created_by,
                'name' => $current->name,
                'description' => $current->description,
                'due_at' => $nextDueAt,
                'show_in_calendar' => $current->show_in_calendar,
                'priority' => $current->priority,
                'status' => TaskStatus::NotStarted,
                'recurrence' => $current->recurrence,
                'recurrence_ends_at' => $current->recurrence_ends_at,
                'previous_occurrence_id' => $current->id,
            ]);

            $next->assignees()->attach($current->assignees()->pluck('users.id'));
            $next->subtasks()->createMany(
                $current->subtasks()->get()->map(fn ($subtask) => [
                    'title' => $subtask->title,
                    'is_completed' => false,
                    'sort_order' => $subtask->sort_order,
                ])->all()
            );

            return $next;
        });
    }
}
