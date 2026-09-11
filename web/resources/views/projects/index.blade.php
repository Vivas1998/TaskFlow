<x-layouts.app title="Mis proyectos" :projects="$projects">
    <div class="dashboard__heading">
        <div><p class="dashboard__eyebrow">Espacio de trabajo</p><h1 class="dashboard__title">Mis proyectos</h1><p class="dashboard__subtitle">Solo puedes ver los proyectos de los que formas parte.</p></div>
        <div class="dashboard__actions"><a class="button button--primary" href="{{ route('projects.create') }}">Nuevo proyecto</a></div>
    </div>

    @if ($projects->isEmpty())
        <section class="empty-state"><span class="empty-state__icon" aria-hidden="true">+</span><h2 class="empty-state__title">Crea tu primer proyecto</h2><p class="empty-state__text">Serás su propietario y podrás añadir personas que ya tengan una cuenta.</p><a class="button button--primary" href="{{ route('projects.create') }}">Crear proyecto</a></section>
    @else
        <section class="project-grid" aria-label="Proyectos disponibles">
            @foreach ($projects as $project)
                <article class="project-card {{ $project->isArchived() ? 'project-card--archived' : '' }}">
                    <div class="project-card__top">
                        <span class="project-card__mark" aria-hidden="true">{{ mb_strtoupper(mb_substr($project->name, 0, 1)) }}</span>
                        <div class="project-card__actions">
                            @if ($project->isArchived())
                                <span class="status-badge">Archivado</span>
                            @else
                                @can('manageMembers', $project)
                                    <a class="project-card__add-member" href="{{ route('project-members.create', $project) }}" aria-label="Añadir miembro a {{ $project->name }}" title="Añadir miembro"><span aria-hidden="true">+</span></a>
                                @endcan
                            @endif
                        </div>
                    </div>
                    <div><h2 class="project-card__title"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></h2><p class="project-card__description">{{ $project->description ?: 'Sin descripción.' }}</p></div>
                    <div class="project-card__footer"><span>{{ $project->members_count }} {{ $project->members_count === 1 ? 'miembro' : 'miembros' }}</span><span>{{ $project->pivot->role === 'owner' ? 'Propietario' : 'Miembro' }}</span></div>
                </article>
            @endforeach
        </section>
    @endif
</x-layouts.app>
