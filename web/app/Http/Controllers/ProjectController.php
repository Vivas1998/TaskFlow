<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRole;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = $request->user()->projects()
            ->withCount('members')
            ->orderByRaw('archived_at is not null')
            ->orderBy('name')
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function create(Request $request): View
    {
        return view('projects.create', [
            'projects' => $request->user()->projects()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $project = DB::transaction(function () use ($request, $validated): Project {
            $project = Project::create([
                ...$validated,
                'created_by' => $request->user()->id,
            ]);

            $project->members()->attach($request->user()->id, [
                'role' => ProjectRole::Owner->value,
            ]);

            return $project;
        });

        return redirect()->route('projects.show', $project)
            ->with('status', 'Proyecto creado correctamente.');
    }

    public function show(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        $project->load('members')->loadCount('members');

        $tasks = $project->tasks()
            ->with(['assignees', 'subtasks'])
            ->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where('name', 'like', '%'.$request->string('q')->toString().'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')))
            ->when($request->integer('assignee'), fn ($query, $assignee) => $query->whereHas('assignees', fn ($assignees) => $assignees->whereKey($assignee)))
            ->when($request->filled('due_from'), fn ($query) => $query->whereDate('due_at', '>=', $request->string('due_from')))
            ->when($request->filled('due_to'), fn ($query) => $query->whereDate('due_at', '<=', $request->string('due_to')))
            ->orderByRaw("case status when 'in_progress' then 0 when 'not_started' then 1 when 'completed' then 2 when 'cancelled' then 3 else 4 end")
            ->orderByRaw("case priority when 'urgent' then 0 when 'high' then 1 when 'medium' then 2 when 'low' then 3 else 4 end")
            ->orderByRaw('due_at is null')
            ->orderBy('due_at')
            ->latest('created_at')
            ->get();

        $statusCounts = $project->tasks()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('projects.show', [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'tasks' => $tasks,
            'statusCounts' => $statusCounts,
            'statuses' => TaskStatus::cases(),
            'priorities' => TaskPriority::cases(),
            'trashedTaskCount' => $project->tasks()->onlyTrashed()->count(),
        ]);
    }

    public function edit(Request $request, Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]));

        return redirect()->route('projects.show', $project)
            ->with('status', 'Proyecto actualizado.');
    }

    public function archive(Project $project): RedirectResponse
    {
        $this->authorize('archive', $project);
        $project->update(['archived_at' => now()]);

        return redirect()->route('dashboard')->with('status', 'Proyecto archivado.');
    }

    public function restore(Project $project): RedirectResponse
    {
        $this->authorize('restore', $project);
        $project->update(['archived_at' => null]);

        return redirect()->route('projects.show', $project)->with('status', 'Proyecto restaurado.');
    }
}
