<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Panel de desarrollo de TaskFlow">
    <title>Panel | TaskFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#contenido">Saltar al contenido</a>

    <div class="app-shell">
        <aside class="sidebar" data-navigation data-open="false" aria-label="Navegación principal">
            <div class="sidebar__brand">
                <span class="sidebar__brand-mark" aria-hidden="true">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 6h14M5 12h9M5 18h6" stroke-linecap="round" /></svg>
                </span>
                TaskFlow
            </div>

            <p class="sidebar__section-label">Espacio de trabajo</p>
            <nav>
                <ul class="sidebar__nav">
                    <li><a class="sidebar__link sidebar__link--active" href="#" aria-current="page"><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>Inicio</a></li>
                    <li><a class="sidebar__link" href="#tareas"><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m4 7 2 2 4-4M4 15l2 2 4-4M13 7h7M13 15h7" stroke-linecap="round" stroke-linejoin="round"/></svg>Tareas</a></li>
                    <li><a class="sidebar__link" href="#calendario"><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18" stroke-linecap="round"/></svg>Calendario</a></li>
                </ul>
            </nav>

            <div class="sidebar__divider"></div>
            <p class="sidebar__section-label">Mis proyectos</p>
            <ul class="sidebar__project-list">
                <li><a class="sidebar__link" href="#"><span class="sidebar__project-dot"></span>TaskFlow 1.0</a></li>
                <li><a class="sidebar__link" href="#"><span class="sidebar__project-dot sidebar__project-dot--green"></span>Homelab</a></li>
            </ul>

            <div class="sidebar__footer">
                <a class="sidebar__link" href="#"><svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1A8 8 0 0 0 15 6l-.3-2.6h-4L10.4 6A8 8 0 0 0 9 7.1l-2.4-1-2 3.4 2 1.5a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1a8 8 0 0 0 1.4.9l.3 2.7h4l.3-2.7a8 8 0 0 0 1.5-.9l2.4 1 2-3.4-2-1.5a7 7 0 0 0 .1-1Z"/></svg>Configuración</a>
            </div>
        </aside>

        <div class="workspace">
            <header class="topbar">
                <div class="topbar__left">
                    <button class="topbar__menu" type="button" data-navigation-toggle aria-expanded="false" aria-label="Abrir navegación"><svg class="topbar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg></button>
                    <div><p class="topbar__context">Proyecto activo</p><p class="topbar__project">TaskFlow 1.0</p></div>
                </div>
                <div class="topbar__right">
                    <button class="topbar__button" type="button"><svg class="topbar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" stroke-linecap="round" stroke-linejoin="round"/></svg><span class="topbar__button-label">Notificaciones</span></button>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button class="topbar__button" type="submit">Cerrar sesión</button>
                    </form>
                    <span class="avatar" aria-label="Usuario {{ auth()->user()->full_name }}">{{ auth()->user()->initials }}</span>
                </div>
            </header>

            <main class="dashboard" id="contenido">
                <div class="dashboard__heading">
                    <div>
                        <p class="dashboard__eyebrow">{{ ucfirst(now()->translatedFormat('l, j \d\e F')) }}</p>
                        <h1 class="dashboard__title">Buenos días, {{ auth()->user()->first_name }}</h1>
                        <p class="dashboard__subtitle">Aquí tienes lo importante para avanzar hoy.</p>
                    </div>
                    <div class="dashboard__actions">
                        <a class="button button--secondary" href="#calendario"><svg class="button__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>Nuevo evento</a>
                        <a class="button button--primary" href="#tareas"><svg class="button__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke-linecap="round"/></svg>Nueva tarea</a>
                    </div>
                </div>

                <section class="summary-grid" aria-label="Resumen del proyecto">
                    <article class="summary-card"><p class="summary-card__label">Sin empezar</p><div class="summary-card__value-row"><p class="summary-card__value">8</p><span class="summary-card__signal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="8"/></svg></span></div></article>
                    <article class="summary-card"><p class="summary-card__label">En progreso</p><div class="summary-card__value-row"><p class="summary-card__value">4</p><span class="summary-card__signal" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4a8 8 0 1 1-8 8"/><path d="M12 4v8H4"/></svg></span></div></article>
                    <article class="summary-card"><p class="summary-card__label">Completadas</p><div class="summary-card__value-row"><p class="summary-card__value">17</p><span class="summary-card__signal summary-card__signal--success" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div></article>
                    <article class="summary-card"><p class="summary-card__label">Vencen esta semana</p><div class="summary-card__value-row"><p class="summary-card__value">3</p><span class="summary-card__signal summary-card__signal--warning" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v5M12 17h.01" stroke-linecap="round"/><path d="M10.3 3.8 2.8 17a2 2 0 0 0 1.74 3h14.92a2 2 0 0 0 1.74-3L13.7 3.8a2 2 0 0 0-3.4 0Z"/></svg></span></div></article>
                </section>

                <div class="dashboard__grid">
                    <section class="panel" id="tareas">
                        <div class="panel__header"><div><h2 class="panel__title">Tareas prioritarias</h2><p class="panel__meta">Ordenadas por fecha límite</p></div><a class="panel__link" href="#">Ver todas</a></div>
                        <ul class="task-list">
                            <li class="task-item"><button class="task-item__check" type="button" aria-label="Marcar Diseño de la base de datos como completada"></button><div><p class="task-item__title">Diseño de la base de datos</p><p class="task-item__detail">Hoy · Arquitectura</p></div><span class="task-item__priority task-item__priority--urgent">Urgente</span><div class="task-item__avatars" aria-label="Asignada a Pablo y Laura"><span class="avatar">PG</span><span class="avatar">LM</span></div></li>
                            <li class="task-item"><button class="task-item__check" type="button" aria-label="Marcar Preparar flujo de registro como completada"></button><div><p class="task-item__title">Preparar flujo de registro</p><p class="task-item__detail">Mañana · Autenticación</p></div><span class="task-item__priority">Alta</span><div class="task-item__avatars" aria-label="Asignada a Pablo"><span class="avatar">PG</span></div></li>
                            <li class="task-item"><button class="task-item__check" type="button" aria-label="Marcar Validar diseño adaptable como completada"></button><div><p class="task-item__title">Validar diseño adaptable</p><p class="task-item__detail">14 sep · Interfaz</p></div><span class="task-item__priority">Alta</span><div class="task-item__avatars" aria-label="Asignada a Laura"><span class="avatar">LM</span></div></li>
                            <li class="task-item"><button class="task-item__check" type="button" aria-label="Marcar Documentar entorno local como completada"></button><div><p class="task-item__title">Documentar entorno local</p><p class="task-item__detail">16 sep · Documentación</p></div><span class="task-item__priority">Alta</span><div class="task-item__avatars" aria-label="Asignada a Pablo"><span class="avatar">PG</span></div></li>
                        </ul>
                    </section>

                    <section class="panel" id="calendario">
                        <div class="panel__header"><div><h2 class="panel__title">Septiembre 2026</h2><p class="panel__meta">Calendario del proyecto</p></div><a class="panel__link" href="#">Abrir calendario</a></div>
                        <div class="calendar-preview">
                            <div class="calendar-preview__weekdays" aria-hidden="true"><span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span></div>
                            <div class="calendar-preview__grid" aria-label="Vista mensual de septiembre de 2026">
                                <span class="calendar-preview__day calendar-preview__day--muted">31</span><span class="calendar-preview__day">1</span><span class="calendar-preview__day">2</span><span class="calendar-preview__day">3</span><span class="calendar-preview__day">4</span><span class="calendar-preview__day">5</span><span class="calendar-preview__day">6</span>
                                <span class="calendar-preview__day">7</span><span class="calendar-preview__day calendar-preview__day--event">8</span><span class="calendar-preview__day">9</span><span class="calendar-preview__day calendar-preview__day--today">10</span><span class="calendar-preview__day">11</span><span class="calendar-preview__day">12</span><span class="calendar-preview__day">13</span>
                                <span class="calendar-preview__day calendar-preview__day--event">14</span><span class="calendar-preview__day">15</span><span class="calendar-preview__day calendar-preview__day--event">16</span><span class="calendar-preview__day">17</span><span class="calendar-preview__day">18</span><span class="calendar-preview__day">19</span><span class="calendar-preview__day">20</span>
                                <span class="calendar-preview__day">21</span><span class="calendar-preview__day">22</span><span class="calendar-preview__day">23</span><span class="calendar-preview__day">24</span><span class="calendar-preview__day">25</span><span class="calendar-preview__day">26</span><span class="calendar-preview__day">27</span>
                                <span class="calendar-preview__day">28</span><span class="calendar-preview__day">29</span><span class="calendar-preview__day">30</span><span class="calendar-preview__day calendar-preview__day--muted">1</span><span class="calendar-preview__day calendar-preview__day--muted">2</span><span class="calendar-preview__day calendar-preview__day--muted">3</span><span class="calendar-preview__day calendar-preview__day--muted">4</span>
                            </div>
                            <div class="agenda"><p class="agenda__label">Próximos</p><div class="agenda__item"><span class="agenda__bar"></span><p class="agenda__title">Revisión semanal</p><p class="agenda__time">10:00</p></div><div class="agenda__item"><span class="agenda__bar agenda__bar--green"></span><p class="agenda__title">Demo de interfaz</p><p class="agenda__time">16:30</p></div></div>
                        </div>
                    </section>
                </div>

                <p class="prototype-note">Primera interfaz con datos de ejemplo; todavía no están conectados a la base de datos.</p>
            </main>
        </div>
    </div>
</body>
</html>
