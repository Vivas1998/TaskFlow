<x-layouts.app title="Calendario" :projects="$projects" :active-project="$project">
    <div class="dashboard__heading"><div><p class="dashboard__eyebrow">{{ $project->name }}</p><h1 class="dashboard__title">Calendario</h1><p class="dashboard__subtitle">Eventos y tareas marcadas para mostrarse en el calendario.</p></div>@if(!$project->isArchived())<div class="dashboard__actions"><a class="button button--secondary" href="{{ route('events.trash', $project) }}">Papelera ({{ $trashedEventCount }})</a><a class="button button--primary" href="{{ route('events.create', $project) }}">Nuevo evento</a></div>@endif</div>
    <div class="calendar-legend" aria-label="Leyenda de responsables y estados">
        @foreach ($project->members as $member)
            <span><i class="calendar-legend__dot" style="--legend-color: {{ $member->displayColor() }}"></i>{{ $member->full_name }}</span>
        @endforeach
        <span><i class="calendar-legend__dot" style="--legend-color: {{ \App\Support\ColorPalette::GENERIC }}"></i>Tarea compartida</span>
        <span><i class="calendar-legend__dot calendar-legend__dot--event-colors"></i>Evento: color elegido</span>
        <span><i class="calendar-legend__state calendar-legend__state--completed">✓</i>Completada</span>
        <span><i class="calendar-legend__state calendar-legend__state--cancelled">×</i>Cancelada</span>
    </div>
    <section class="panel calendar-panel">
        <p class="calendar-status" data-calendar-status role="status">Cargando calendario…</p>
        <div class="calendar" data-calendar data-feed-url="{{ route('calendar.feed', $project) }}" data-create-event-url="{{ $project->isArchived() ? '' : route('events.create', $project) }}"></div>
    </section>
</x-layouts.app>
