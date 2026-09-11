<?php

namespace App\Http\Controllers;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProjectMemberController extends Controller
{
    public function create(Request $request, Project $project): View
    {
        $this->authorize('manageMembers', $project);

        return view('members.create', [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::enum(ProjectRole::class)],
        ], [
            'email.exists' => 'No existe ninguna cuenta con ese correo electrónico.',
        ]);

        $user = User::where('email', Str::lower($validated['email']))->firstOrFail();

        if ($project->members()->whereKey($user->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Esta persona ya pertenece al proyecto.',
            ]);
        }

        $project->members()->attach($user->id, ['role' => $validated['role']]);

        return redirect()->route('dashboard')->with('status', 'Miembro añadido a '.$project->name.'.');
    }

    public function update(Request $request, Project $project, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validate([
            'role' => ['required', Rule::enum(ProjectRole::class)],
        ]);

        $currentRole = $project->members()->whereKey($user->id)->firstOrFail()->pivot->role;

        if ($currentRole === ProjectRole::Owner->value
            && $validated['role'] !== ProjectRole::Owner->value
            && $this->ownerCount($project) === 1) {
            throw ValidationException::withMessages([
                'role' => 'El proyecto debe conservar al menos un propietario.',
            ]);
        }

        $project->members()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        return back()->with('status', 'Rol actualizado.');
    }

    public function destroy(Project $project, User $user): RedirectResponse
    {
        $this->authorize('manageMembers', $project);

        $membership = $project->members()->whereKey($user->id)->firstOrFail();

        if ($membership->pivot->role === ProjectRole::Owner->value && $this->ownerCount($project) === 1) {
            throw ValidationException::withMessages([
                'member' => 'No se puede quitar al único propietario del proyecto.',
            ]);
        }

        $project->members()->detach($user->id);

        if ($user->is(request()->user())) {
            return redirect()->route('dashboard')->with('status', 'Has salido del proyecto.');
        }

        return back()->with('status', 'Miembro retirado del proyecto.');
    }

    private function ownerCount(Project $project): int
    {
        return $project->members()->wherePivot('role', ProjectRole::Owner->value)->count();
    }
}
