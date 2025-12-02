<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Iniciar sesión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/login.css', 'resources/js/login.js'])

</head>

<body>
  <div class="login-page">
    <div class="login-card">
      <h1 class="login-title">Iniciar sesión</h1>
      @if ($errors->any())
        <div class="alert error">
          {{ $errors->first() }}
        </div>
      @endif

      <form id="loginForm" method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="form-group">
          <label for="email">Correo</label>
          <input
            id="email"
            name="email"
            type="email"
            placeholder="tucorreo@laflotapepsiman.cl"
            autocomplete="username"
            value="{{ old('email') }}"
            required
          >
          <small
            class="msg {{ $errors->has('email') ? 'is-error' : '' }}"
            data-for="email"
          >
            @error('email')
              {{ $message }}
            @enderror
          </small>
        </div>

        <div class="form-group">
          <label for="password">Contraseña</label>
          <div class="password-wrap">
            <input
              id="password"
              name="password"
              type="password"
              placeholder="••••••••"
              autocomplete="current-password"
              required
            >
            <button
              type="button"
              class="toggle-pass"
              aria-label="Mostrar u ocultar contraseña"
            >
              <i class="bi bi-eye-fill"></i>
            </button>
          </div>
          <small
            class="msg {{ $errors->has('password') ? 'is-error' : '' }}"
            data-for="password"
          >
            @error('password')
              {{ $message }}
            @enderror
          </small>
        </div>

        <div class="form-row">
          <label class="remember">
            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Recordarme
          </label>
          <a href="{{ route('password.request') }}" class="link">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-primary" id="btnLogin">Entrar</button>
      </form>
    </div>
  </div>
</body>
</html>
