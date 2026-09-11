<x-layouts.guest title="Crear cuenta">
    <div class="auth-card__heading">
        <p class="auth-card__eyebrow">Empieza en unos minutos</p>
        <h1 class="auth-card__title">Crea tu cuenta</h1>
        <p class="auth-card__subtitle">Después podrás crear un proyecto o unirte a uno existente.</p>
    </div>

    <form class="form" action="{{ route('register') }}" method="post">
        @csrf
        <div class="form__columns">
            <div class="form-field">
                <label class="form-field__label" for="first_name">Nombre</label>
                <input class="form-field__input" id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" autocomplete="given-name" required autofocus>
                @error('first_name')<p class="form-field__error">{{ $message }}</p>@enderror
            </div>
            <div class="form-field">
                <label class="form-field__label" for="last_name">Apellidos</label>
                <input class="form-field__input" id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" autocomplete="family-name" required>
                @error('last_name')<p class="form-field__error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="form-field">
            <label class="form-field__label" for="email">Correo electrónico</label>
            <input class="form-field__input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field">
            <label class="form-field__label" for="password">Contraseña</label>
            <input class="form-field__input" id="password" name="password" type="password" autocomplete="new-password" required aria-describedby="password-help">
            <p class="form-field__help" id="password-help">Mínimo 12 caracteres, con mayúsculas, minúsculas y números.</p>
            @error('password')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field">
            <label class="form-field__label" for="password_confirmation">Repite la contraseña</label>
            <input class="form-field__input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
        </div>
        <button class="button button--primary form__submit" type="submit">Crear mi cuenta</button>
    </form>

    <p class="auth-card__alternative">¿Ya tienes cuenta? <a href="{{ route('login') }}">Iniciar sesión</a></p>
</x-layouts.guest>
