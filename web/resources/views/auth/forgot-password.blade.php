<x-layouts.guest title="Recuperar contraseña">
    <div class="auth-card__heading">
        <p class="auth-card__eyebrow">Recuperar acceso</p>
        <h1 class="auth-card__title">¿Has olvidado la contraseña?</h1>
        <p class="auth-card__subtitle">Escribe tu correo y te enviaremos un enlace válido durante 60 minutos.</p>
    </div>

    @if (session('status'))
        <p class="form-message form-message--success" role="status">{{ session('status') }}</p>
    @endif

    <form class="form" action="{{ route('password.email') }}" method="post">
        @csrf
        <div class="form-field">
            <label class="form-field__label" for="email">Correo electrónico</label>
            <input class="form-field__input" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
        <button class="button button--primary form__submit" type="submit">Enviar enlace</button>
    </form>

    <p class="auth-card__alternative"><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
</x-layouts.guest>
