<x-layouts.app title="Papelera de tareas" :projects="$projects" :active-project="$project">
    <a class="back-link" href="{{ route('projects.show', $project) }}">← Volver al proyecto</a>
    <div class="dashboard__heading"><div><p class="dashboard__eyebrow">Papelera</p><h1 class="dashboard__title">Tareas eliminadas</h1><p class="dashboard__subtitle">Puedes restaurarlas; en la versión 1.0 no se borran definitivamente.</p></div></div>
    <section class="panel">
        @forelse ($tasks as $task)
            <div class="trash-item"><div><strong>{{ $task->name }}</strong><p>Eliminada {{ $task->deleted_at->diffForHumans() }}</p></div><form action="{{ route('tasks.restore', [$project, $task->id]) }}" method="post">@csrf<button class="button button--secondary" type="submit">Restaurar</button></form></div>
        @empty
            <div class="panel-empty"><p>La papelera está vacía.</p></div>
        @endforelse
    </section>
</x-layouts.app>
