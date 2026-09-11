<x-layouts.guest title="Acceder">
    <div class="auth-card__heading">
        <p class="auth-card__eyebrow">Tu espacio de trabajo</p>
        <h1 class="auth-card__title">Inicia sesión</h1>
        <p class="auth-card__subtitle">Accede a tus proyectos, tareas y calendario.</p>
    </div>

    @if (session('status'))
        <p class="form-message form-message--success" role="status">{{ session('status') }}</p>
    @endif

    <form class="form" action="{{ route('login') }}" method="post">
        @csrf
        <div class="form-field">
            <label class="form-field__label" for="email">Correo electrónico</label>
            <input class="form-field__input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus aria-describedby="email-error">
            @error('email')<p class="form-field__error" id="email-error">{{ $message }}</p>@enderror
        </div>
        <div class="form-field">
            <div class="form-field__label-row">
                <label class="form-field__label" for="password">Contraseña</label>
                <a class="form-field__link" href="{{ route('password.request') }}">¿La has olvidado?</a>
            </div>
            <input class="form-field__input" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <label class="form-check"><input name="remember" type="checkbox" value="1"><span>Recordarme en este dispositivo</span></label>
        <button class="button button--primary form__submit" type="submit">Entrar en TaskFlow</button>
    </form>

    <p class="auth-card__alternative">¿Aún no tienes cuenta? <a href="{{ route('register') }}">Crear una cuenta</a></p>
</x-layouts.guest>
