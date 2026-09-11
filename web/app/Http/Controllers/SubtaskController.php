<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubtaskController extends Controller
{
    public function update(Request $request, Project $project, Task $task, Subtask $subtask): RedirectResponse
    {
        abort_unless($task->project_id === $project->id && $subtask->task_id === $task->id, 404);
        $this->authorize('update', $task);

        $subtask->update([
            'is_completed' => $request->boolean('completed'),
        ]);

        return back()->with('status', 'Subtarea actualizada.');
    }
}
