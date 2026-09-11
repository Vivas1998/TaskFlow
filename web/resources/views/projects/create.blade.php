<x-layouts.app title="Nuevo proyecto" :projects="$projects">
    <div class="page-narrow">
        <a class="back-link" href="{{ route('dashboard') }}">← Volver a mis proyectos</a>
        <div class="dashboard__heading"><div><p class="dashboard__eyebrow">Nuevo espacio</p><h1 class="dashboard__title">Crear proyecto</h1><p class="dashboard__subtitle">Te convertirás automáticamente en su primer propietario.</p></div></div>
        <section class="panel form-panel">
            <form class="form" action="{{ route('projects.store') }}" method="post">
                @csrf
                <div class="form-field"><label class="form-field__label" for="name">Nombre del proyecto</label><input class="form-field__input" id="name" name="name" type="text" value="{{ old('name') }}" maxlength="120" required autofocus>@error('name')<p class="form-field__error">{{ $message }}</p>@enderror</div>
                <div class="form-field"><label class="form-field__label" for="description">Descripción <span class="form-field__optional">Opcional</span></label><textarea class="form-field__input form-field__input--textarea" id="description" name="description" maxlength="2000">{{ old('description') }}</textarea>@error('description')<p class="form-field__error">{{ $message }}</p>@enderror</div>
                <div class="form__actions"><a class="button button--secondary" href="{{ route('dashboard') }}">Cancelar</a><button class="button button--primary" type="submit">Crear proyecto</button></div>
            </form>
        </section>
    </div>
</x-layouts.app>
