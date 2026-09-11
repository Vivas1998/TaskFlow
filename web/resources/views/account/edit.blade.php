<x-layouts.app title="Mi cuenta" :projects="$projects">
    <div class="account-page">
        <div class="dashboard__heading">
            <div>
                <p class="dashboard__eyebrow">Cuenta personal</p>
                <h1 class="dashboard__title">Mi cuenta</h1>
                <p class="dashboard__subtitle">Consulta tus datos y mantén segura tu contraseña.</p>
            </div>
        </div>

        <div class="account-grid">
            <section class="account-profile" aria-labelledby="account-profile-title">
                <div class="account-profile__identity">
                    <span class="account-profile__avatar" aria-hidden="true">{{ auth()->user()->initials }}</span>
                    <div>
                        <p class="account-profile__eyebrow">Perfil</p>
                        <h2 id="account-profile-title">{{ auth()->user()->full_name }}</h2>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <dl class="account-profile__details">
                    <div><dt>Nombre</dt><dd>{{ auth()->user()->first_name }}</dd></div>
                    <div><dt>Apellidos</dt><dd>{{ auth()->user()->last_name }}</dd></div>
                    <div><dt>Correo electrónico</dt><dd>{{ auth()->user()->email }}</dd></div>
                </dl>
            </section>

            <section class="account-security" aria-labelledby="account-security-title">
                <div class="account-security__header">
                    <span class="account-security__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 4.6 2.8 8.2 7 10 4.2-1.8 7-5.4 7-10V6l-7-3Z" stroke-linejoin="round"/><path d="m9 12 2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    <div><p class="account-profile__eyebrow">Seguridad</p><h2 id="account-security-title">Cambiar contraseña</h2></div>
                </div>

                <form class="account-security__form" action="{{ route('account.password.update') }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-field">
                        <label class="form-field__label" for="current_password">Contraseña actual</label>
                        <input class="form-field__input" id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                        @error('current_password')<p class="form-field__error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-field__label" for="password">Nueva contraseña</label>
                        <input class="form-field__input" id="password" name="password" type="password" autocomplete="new-password" required>
                        <p class="form-field__help">Utiliza al menos 12 caracteres, mayúsculas, minúsculas y números.</p>
                        @error('password')<p class="form-field__error">{{ $message }}</p>@enderror
                    </div>

                    <div class="form-field">
                        <label class="form-field__label" for="password_confirmation">Repetir nueva contraseña</label>
                        <input class="form-field__input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    </div>

                    <div class="account-security__notice"><span aria-hidden="true">i</span><p>Al guardar se cerrarán las demás sesiones abiertas de esta cuenta.</p></div>
                    <button class="button button--primary account-security__submit" type="submit">Actualizar contraseña</button>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
