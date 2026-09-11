<x-layouts.app :title="$project->name" :projects="$projects" :active-project="$project">
    <div class="dashboard__heading">
        <div><p class="dashboard__eyebrow">{{ $project->isArchived() ? 'Proyecto archivado' : 'Proyecto activo' }}</p><h1 class="dashboard__title">{{ $project->name }}</h1><p class="dashboard__subtitle">{{ $project->description ?: 'Sin descripción.' }}</p></div>
        @if (! $project->isArchived())<div class="dashboard__actions">@can('update', $project)<a class="button button--secondary" href="{{ route('projects.edit', $project) }}">Editar proyecto</a>@endcan<a class="button button--secondary" href="{{ route('events.create', $project) }}">Nuevo evento</a><a class="button button--primary" href="{{ route('tasks.create', $project) }}">Nueva tarea</a></div>@endif
    </div>

    @if ($project->isArchived())
        <div class="archive-banner"><div><strong>Proyecto en modo de solo lectura</strong><p>Sus tareas y eventos se conservarán sin cambios.</p></div>@can('restore', $project)<form action="{{ route('projects.restore', $project) }}" method="post">@csrf<button class="button button--secondary" type="submit">Restaurar proyecto</button></form>@endcan</div>
    @endif

    <section class="summary-grid" aria-label="Resumen del proyecto">
        <article class="summary-card"><p class="summary-card__label">Sin empezar</p><div class="summary-card__value-row"><p class="summary-card__value">{{ $statusCounts['not_started'] ?? 0 }}</p><span class="summary-card__signal">○</span></div></article>
        <article class="summary-card"><p class="summary-card__label">En progreso</p><div class="summary-card__value-row"><p class="summary-card__value">{{ $statusCounts['in_progress'] ?? 0 }}</p><span class="summary-card__signal">◔</span></div></article>
        <article class="summary-card"><p class="summary-card__label">Completadas</p><div class="summary-card__value-row"><p class="summary-card__value">{{ $statusCounts['completed'] ?? 0 }}</p><span class="summary-card__signal summary-card__signal--success">✓</span></div></article>
        <article class="summary-card"><p class="summary-card__label">Miembros</p><div class="summary-card__value-row"><p class="summary-card__value">{{ $project->members_count }}</p><span class="summary-card__signal">◎</span></div></article>
    </section>

    <div class="dashboard__grid">
        <section class="panel">
            <div class="panel__header"><div><h2 class="panel__title">Tareas</h2><p class="panel__meta">{{ $tasks->count() }} resultados con los filtros actuales</p></div><a class="panel__link" href="{{ route('tasks.trash', $project) }}">Papelera ({{ $trashedTaskCount }})</a></div>
            <form class="task-filters" action="{{ route('projects.show', $project) }}" method="get">
                <input class="form-field__input" name="q" type="search" value="{{ request('q') }}" placeholder="Buscar por nombre" aria-label="Buscar tarea por nombre">
                <select class="form-field__input" name="status" aria-label="Filtrar por estado"><option value="">Todos los estados</option>@foreach($statuses as $status)<option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>@endforeach</select>
                <select class="form-field__input" name="assignee" aria-label="Filtrar por responsable"><option value="">Todos los responsables</option>@foreach($project->members as $member)<option value="{{ $member->id }}" @selected((string) request('assignee') === (string) $member->id)>{{ $member->full_name }}</option>@endforeach</select>
                <select class="form-field__input" name="priority" aria-label="Filtrar por prioridad"><option value="">Todas las prioridades</option>@foreach($priorities as $priority)<option value="{{ $priority->value }}" @selected(request('priority') === $priority->value)>{{ $priority->label() }}</option>@endforeach</select>
                <input class="form-field__input" name="due_from" type="date" value="{{ request('due_from') }}" aria-label="Fecha límite desde">
                <input class="form-field__input" name="due_to" type="date" value="{{ request('due_to') }}" aria-label="Fecha límite hasta">
                <button class="button button--secondary" type="submit">Filtrar</button>
            </form>
            <div class="task-table">
                @if ($tasks->isEmpty())
                    <div class="panel-empty"><p>{{ request()->hasAny(['q', 'status', 'assignee', 'priority', 'due_from', 'due_to']) ? 'No hay tareas que coincidan con los filtros.' : 'Todavía no hay tareas.' }}</p></div>
                @else
                    @php
                        $taskGroups = [
                            ['status' => \App\Enums\TaskStatus::InProgress, 'label' => 'En progreso', 'open' => true],
                            ['status' => \App\Enums\TaskStatus::NotStarted, 'label' => 'Sin empezar', 'open' => true],
                            ['status' => \App\Enums\TaskStatus::Completed, 'label' => 'Completadas', 'open' => false],
                            ['status' => \App\Enums\TaskStatus::Cancelled, 'label' => 'Canceladas', 'open' => false],
                        ];
                        $hasActiveTaskFilters = request()->hasAny(['q', 'status', 'assignee', 'priority', 'due_from', 'due_to']);
                    @endphp
                    <div class="task-groups">
                    @foreach ($taskGroups as $group)
                        @php $groupTasks = $tasks->filter(fn ($task) => $task->status === $group['status']); @endphp
                        @continue($groupTasks->isEmpty())
                        <details class="task-group task-group--{{ $group['status']->value }}" @if($group['open'] || $hasActiveTaskFilters) open @endif>
                            <summary class="task-group__summary">
                                <span class="task-group__dot" aria-hidden="true"></span>
                                <span class="task-group__title">{{ $group['label'] }}</span>
                                <span class="task-group__count">{{ $groupTasks->count() }}</span>
                                <svg class="task-group__chevron" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 8 4 4 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </summary>
                            <div class="task-group__content">
                            @foreach ($groupTasks as $task)
                    <article class="task-row task-row--{{ $task->status->value }}" style="--assignee-color: {{ $task->assignmentColor() }}">
                        @if (! $project->isArchived())
                            <form class="task-completion" action="{{ route('tasks.completion', [$project, $task]) }}" method="post">
                                @csrf
                                @method('PATCH')
                                <input name="completed" type="hidden" value="0">
                                <label class="task-completion__label" title="{{ $task->status === \App\Enums\TaskStatus::Completed ? 'Reabrir tarea' : 'Completar tarea' }}">
                                    <input class="task-completion__input" name="completed" type="checkbox" value="1" @checked($task->status === \App\Enums\TaskStatus::Completed) onchange="this.form.submit()">
                                    <span class="task-completion__box" aria-hidden="true">✓</span>
                                    <span class="sr-only">{{ $task->status === \App\Enums\TaskStatus::Completed ? 'Reabrir' : 'Completar' }} {{ $task->name }}</span>
                                </label>
                            </form>
                        @else
                            <span class="task-completion__box task-completion__box--static {{ $task->status === \App\Enums\TaskStatus::Completed ? 'task-completion__box--checked' : '' }}" aria-hidden="true">✓</span>
                        @endif
                        <div class="task-row__main">
                            @if ($project->isArchived())<span class="task-row__title">{{ $task->name }}</span>@else<a class="task-row__title" href="{{ route('tasks.edit', [$project, $task]) }}">{{ $task->name }}</a>@endif
                            <p>{{ $task->due_at ? $task->due_at->translatedFormat('j M Y, H:i') : 'Sin fecha límite' }}@if($task->recurrence) · {{ $task->recurrence->label() }}@endif</p>
                            @if ($task->subtasks->isNotEmpty())
                                <ul class="task-row__subtasks" aria-label="Subtareas de {{ $task->name }}">
                                    @foreach ($task->subtasks as $subtask)
                                        <li class="subtask-check {{ $subtask->is_completed ? 'subtask-check--completed' : '' }}">
                                            @if (! $project->isArchived())
                                                <form action="{{ route('subtasks.update', [$project, $task, $subtask]) }}" method="post">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input name="completed" type="hidden" value="0">
                                                    <label>
                                                        <input name="completed" type="checkbox" value="1" @checked($subtask->is_completed) onchange="this.form.submit()">
                                                        <span class="subtask-check__box" aria-hidden="true">✓</span>
                                                        <span>{{ $subtask->title }}</span>
                                                    </label>
                                                </form>
                                            @else
                                                <span class="subtask-check__box {{ $subtask->is_completed ? 'subtask-check__box--checked' : '' }}" aria-hidden="true">✓</span><span>{{ $subtask->title }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <span class="task-row__priority task-row__priority--{{ $task->priority?->value ?? 'none' }}">{{ $task->priority?->label() ?? 'Sin prioridad' }}</span>
                        <div class="task-row__people" aria-label="{{ $task->assignmentLabel() }}">@foreach($task->assignees->take(3) as $assignee)<span class="avatar" style="--avatar-color: {{ $assignee->displayColor() }}" title="{{ $assignee->full_name }}">{{ $assignee->initials }}</span>@endforeach</div>
                        @if (! $project->isArchived())<form action="{{ route('tasks.status', [$project, $task]) }}" method="post">@csrf @method('PATCH')<select class="form-field__input task-row__status" name="status" onchange="this.form.submit()" aria-label="Estado de {{ $task->name }}">@foreach($statuses as $status)<option value="{{ $status->value }}" @selected($task->status === $status)>{{ $status->label() }}</option>@endforeach</select></form>@else<span class="status-badge">{{ $task->status->label() }}</span>@endif
                    </article>
                            @endforeach
                            </div>
                        </details>
                    @endforeach
                    </div>
                @endif
            </div>
        </section>
        <section class="panel">
            <div class="panel__header"><div><h2 class="panel__title">Miembros</h2><p class="panel__meta">{{ $project->members_count }} en el proyecto</p></div></div>
            <ul class="member-list">
                @foreach($project->members as $member)
                    <li class="member-item"><span class="avatar" style="--avatar-color: {{ $member->displayColor() }}">{{ $member->initials }}</span><span><strong>{{ $member->full_name }}</strong><small>{{ $member->email }}</small></span>
                        @can('manageMembers', $project)
                            <div class="member-item__actions"><form action="{{ route('project-members.update', [$project, $member]) }}" method="post">@csrf @method('PUT')<select class="form-field__input member-item__role" name="role" onchange="this.form.submit()" aria-label="Rol de {{ $member->full_name }}"><option value="member" @selected($member->pivot->role === 'member')>Miembro</option><option value="owner" @selected($member->pivot->role === 'owner')>Propietario</option></select></form><form action="{{ route('project-members.destroy', [$project, $member]) }}" method="post">@csrf @method('DELETE')<button class="icon-button" type="submit" aria-label="Quitar a {{ $member->full_name }}" title="Quitar del proyecto">×</button></form></div>
                        @else<span class="status-badge">{{ $member->pivot->role === 'owner' ? 'Propietario' : 'Miembro' }}</span>@endcan
                    </li>
                @endforeach
            </ul>
            @error('role')<p class="panel-error">{{ $message }}</p>@enderror
            @error('member')<p class="panel-error">{{ $message }}</p>@enderror
        </section>
    </div>

    @can('manageMembers', $project)
        <form class="archive-action" action="{{ route('projects.archive', $project) }}" method="post">@csrf<button class="button button--danger" type="submit">Archivar proyecto</button><p>No se borrará ningún dato y podrás restaurarlo después.</p></form>
    @endcan
</x-layouts.app>
