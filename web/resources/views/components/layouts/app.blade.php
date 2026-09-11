@props(['title', 'projects', 'activeProject' => null])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Espacio privado de TaskFlow">
    <title>{{ $title }} | TaskFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php($accountActive = request()->routeIs('account.*'))
    <a class="skip-link" href="#contenido">Saltar al contenido</a>
    <div class="app-shell">
        <aside class="sidebar" data-navigation data-open="false" aria-label="Navegación principal">
            <a class="sidebar__brand" href="{{ route('dashboard') }}">
                <span class="sidebar__brand-mark" aria-hidden="true"><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 6h14M5 12h9M5 18h6" stroke-linecap="round"/></svg></span>
                TaskFlow
            </a>
            <p class="sidebar__section-label">Espacio de trabajo</p>
            <nav>
                <ul class="sidebar__nav">
                    <li><a class="sidebar__link {{ request()->routeIs('dashboard') ? 'sidebar__link--active' : '' }}" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>Mis proyectos</a></li>
                    @if ($activeProject)
                        <li><a class="sidebar__link {{ request()->routeIs('projects.show', 'tasks.*') ? 'sidebar__link--active' : '' }}" href="{{ route('projects.show', $activeProject) }}" @if(request()->routeIs('projects.show', 'tasks.*')) aria-current="page" @endif><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 7 2 2 4-4M4 15l2 2 4-4M13 7h7M13 15h7" stroke-linecap="round" stroke-linejoin="round"/></svg>Tareas</a></li>
                        <li><a class="sidebar__link {{ request()->routeIs('calendar.*', 'events.*') ? 'sidebar__link--active' : '' }}" href="{{ route('calendar.index', $activeProject) }}" @if(request()->routeIs('calendar.*', 'events.*')) aria-current="page" @endif><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18" stroke-linecap="round"/></svg>Calendario</a></li>
                    @endif
                </ul>
            </nav>
            <div class="sidebar__divider"></div>
            <p class="sidebar__section-label">Proyectos</p>
            <ul class="sidebar__project-list">
                @forelse ($projects->whereNull('archived_at')->take(6) as $navigationProject)
                    <li><a class="sidebar__link {{ $activeProject?->is($navigationProject) ? 'sidebar__link--active' : '' }}" href="{{ route('projects.show', $navigationProject) }}"><span class="sidebar__project-dot"></span>{{ $navigationProject->name }}</a></li>
                @empty
                    <li><span class="sidebar__link">Todavía no hay proyectos</span></li>
                @endforelse
            </ul>
            <div class="sidebar__footer">
                <a class="sidebar__link {{ $accountActive ? 'sidebar__link--active' : '' }}" href="{{ route('account.edit') }}" @if($accountActive) aria-current="page" @endif><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4.5 21a7.5 7.5 0 0 1 15 0" stroke-linecap="round"/></svg>Mi cuenta</a>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="sidebar__link sidebar__button" type="submit"><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 5H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h5M14 8l4 4-4 4M8 12h10" stroke-linecap="round" stroke-linejoin="round"/></svg>Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <div class="workspace">
            <header class="topbar">
                <div class="topbar__left">
                    <button class="topbar__menu" type="button" data-navigation-toggle aria-expanded="false" aria-label="Abrir navegación"><svg class="topbar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg></button>
                    <div><p class="topbar__context">{{ $activeProject ? 'Proyecto activo' : ($accountActive ? 'Cuenta personal' : 'Espacio de trabajo') }}</p><p class="topbar__project">{{ $activeProject?->name ?? ($accountActive ? 'Mi cuenta' : 'Mis proyectos') }}</p></div>
                </div>
                <div class="topbar__right"><span class="topbar__user-name">{{ auth()->user()->full_name }}</span><span class="avatar" aria-hidden="true">{{ auth()->user()->initials }}</span></div>
            </header>
            <main class="dashboard" id="contenido">
                @if (session('status'))<p class="form-message form-message--success" role="status">{{ session('status') }}</p>@endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
