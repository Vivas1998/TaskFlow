<x-layouts.app title="Nueva tarea" :projects="$projects" :active-project="$project">
    <div class="page-narrow">
        <a class="back-link" href="{{ route('projects.show', $project) }}">← Volver al proyecto</a>
        <div class="dashboard__heading"><div><p class="dashboard__eyebrow">{{ $project->name }}</p><h1 class="dashboard__title">Nueva tarea</h1><p class="dashboard__subtitle">Asígnala a una o varias personas del proyecto.</p></div></div>
        <section class="panel form-panel"><x-task-form :action="route('tasks.store', $project)" :project="$project" :statuses="$statuses" :priorities="$priorities" :recurrences="$recurrences" /></section>
    </div>
</x-layouts.app>
