<x-layouts.app title="Editar proyecto" :projects="$projects" :active-project="$project">
    <div class="page-narrow">
        <a class="back-link" href="{{ route('projects.show', $project) }}">← Volver al proyecto</a>
        <div class="dashboard__heading"><div><p class="dashboard__eyebrow">Administración</p><h1 class="dashboard__title">Editar proyecto</h1></div></div>
        <section class="panel form-panel">
            <form class="form" action="{{ route('projects.update', $project) }}" method="post">
                @csrf @method('PUT')
                <div class="form-field"><label class="form-field__label" for="name">Nombre del proyecto</label><input class="form-field__input" id="name" name="name" type="text" value="{{ old('name', $project->name) }}" maxlength="120" required autofocus>@error('name')<p class="form-field__error">{{ $message }}</p>@enderror</div>
                <div class="form-field"><label class="form-field__label" for="description">Descripción <span class="form-field__optional">Opcional</span></label><textarea class="form-field__input form-field__input--textarea" id="description" name="description" maxlength="2000">{{ old('description', $project->description) }}</textarea>@error('description')<p class="form-field__error">{{ $message }}</p>@enderror</div>
                <div class="form__actions"><a class="button button--secondary" href="{{ route('projects.show', $project) }}">Cancelar</a><button class="button button--primary" type="submit">Guardar cambios</button></div>
            </form>
        </section>
    </div>
</x-layouts.app>
