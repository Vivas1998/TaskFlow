@props(['title'])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Acceso privado a TaskFlow">
    <title>{{ $title }} | TaskFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-shell">
        <section class="auth-intro" aria-labelledby="taskflow-brand">
            <a class="auth-intro__brand" href="{{ route('login') }}" id="taskflow-brand">
                <span class="sidebar__brand-mark" aria-hidden="true">
                    <svg class="sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 6h14M5 12h9M5 18h6" stroke-linecap="round" /></svg>
                </span>
                TaskFlow
            </a>
            <div class="auth-intro__content">
                <p class="auth-intro__eyebrow">Organización compartida</p>
                <p class="auth-intro__title">Proyectos claros.<br>Tareas que avanzan.</p>
                <p class="auth-intro__text">Un espacio privado para coordinar equipos pequeños sin perder fechas, responsables ni contexto.</p>
            </div>
            <p class="auth-intro__footer">Entorno local de desarrollo · Versión 1.0</p>
        </section>

        <section class="auth-content">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </section>
    </main>
</body>
</html>
