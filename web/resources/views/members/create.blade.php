<x-layouts.app title="Añadir miembro" :projects="$projects" :active-project="$project">
    <div class="page-narrow page-narrow--wide">
        <a class="back-link" href="{{ route('dashboard') }}">← Volver a mis proyectos</a>

        <section class="member-invite" aria-labelledby="member-invite-title">
            <div class="member-invite__aside">
                <span class="member-invite__icon" aria-hidden="true">+</span>
                <p class="member-invite__eyebrow">{{ $project->name }}</p>
                <h1 class="member-invite__title" id="member-invite-title">Añadir miembro</h1>
                <p class="member-invite__text">Incorpora a una persona al proyecto para que pueda ver y gestionar sus tareas y eventos.</p>
                <div class="member-invite__note">
                    <span aria-hidden="true">i</span>
                    <p>La persona ya debe tener una cuenta de TaskFlow con el correo que introduzcas.</p>
                </div>
            </div>

            <form class="member-invite__form" action="{{ route('project-members.store', $project) }}" method="post">
                @csrf
                <div class="form-field">
                    <label class="form-field__label" for="email">Correo electrónico</label>
                    <input class="form-field__input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nombre@ejemplo.com" autocomplete="email" required autofocus>
                    @error('email')<p class="form-field__error">{{ $message }}</p>@enderror
                </div>

                <fieldset class="role-options">
                    <legend class="form-field__label">Rol en el proyecto</legend>
                    <label class="role-option">
                        <input name="role" type="radio" value="member" @checked(old('role', 'member') === 'member')>
                        <span class="role-option__control" aria-hidden="true"></span>
                        <span><strong>Miembro</strong><small>Puede trabajar con tareas y eventos.</small></span>
                    </label>
                    <label class="role-option">
                        <input name="role" type="radio" value="owner" @checked(old('role') === 'owner')>
                        <span class="role-option__control" aria-hidden="true"></span>
                        <span><strong>Propietario</strong><small>También puede administrar el proyecto y sus miembros.</small></span>
                    </label>
                    @error('role')<p class="form-field__error">{{ $message }}</p>@enderror
                </fieldset>

                <div class="member-invite__actions">
                    <a class="button button--secondary" href="{{ route('dashboard') }}">Cancelar</a>
                    <button class="button button--primary" type="submit">Añadir miembro</button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.app>
