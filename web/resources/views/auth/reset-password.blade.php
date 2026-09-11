<x-layouts.guest title="Cambiar contraseña">
    <div class="auth-card__heading">
        <p class="auth-card__eyebrow">Nueva contraseña</p>
        <h1 class="auth-card__title">Recupera tu cuenta</h1>
        <p class="auth-card__subtitle">Al guardar la nueva contraseña se cerrarán las demás sesiones abiertas.</p>
    </div>

    <form class="form" action="{{ route('password.update') }}" method="post">
        @csrf
        <input name="token" type="hidden" value="{{ $token }}">
        <div class="form-field">
            <label class="form-field__label" for="email">Correo electrónico</label>
            <input class="form-field__input" id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus>
            @error('email')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field">
            <label class="form-field__label" for="password">Nueva contraseña</label>
            <input class="form-field__input" id="password" name="password" type="password" autocomplete="new-password" required>
            @error('password')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field">
            <label class="form-field__label" for="password_confirmation">Repite la nueva contraseña</label>
            <input class="form-field__input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button class="button button--primary form__submit" type="submit">Guardar contraseña</button>
    </form>
</x-layouts.guest>
