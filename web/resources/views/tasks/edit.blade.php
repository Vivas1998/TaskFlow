<x-layouts.app title="Editar tarea" :projects="$projects" :active-project="$project">
    <div class="page-narrow">
        <a class="back-link" href="{{ route('projects.show', $project) }}">← Volver al proyecto</a>
        <div class="dashboard__heading"><div><p class="dashboard__eyebrow">{{ $project->name }}</p><h1 class="dashboard__title">Editar tarea</h1></div></div>
        <section class="panel form-panel"><x-task-form :action="route('tasks.update', [$project, $task])" method="PUT" :project="$project" :task="$task" :statuses="$statuses" :priorities="$priorities" :recurrences="$recurrences" /></section>
        <form class="archive-action" action="{{ route('tasks.destroy', [$project, $task]) }}" method="post">
            @csrf
            @method('DELETE')
            <button class="button button--danger" type="submit">Mover tarea a la papelera</button>
            <p>Podrás recuperarla desde la papelera del proyecto.</p>
        </form>
    </div>
</x-layouts.app>
