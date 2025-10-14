<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recuperar contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/password_email.css','resources/js/password_email.js'])
</head>
<body>

  <div class="page">
    <div class="toolbar">
      <a class="btn-back" href="{{ url('/login') }}">← Volver</a>
    </div>

    <div class="card">
      <h2 class="title">¿Olvidaste tu contraseña?</h2>
      <p class="desc">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

      {{-- mensaje de éxito del backend --}}
      @if (session('status'))
        <div id="okBox" class="alert success">{{ session('status') }}</div>
      @else
        <div id="okBox" class="alert success hidden"></div>
      @endif

      {{-- errores de validación del backend --}}
      @if ($errors->any())
        <ul class="err" style="margin:8px 0 0 18px">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      @endif

      <form id="forgotForm" method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="group">
          <label for="email">Correo</label>
          <input id="email" name="email" type="email" autocomplete="email" required autofocus>
          <small class="msg" data-for="email"></small>
        </div>

        <button id="btnSend" type="submit" class="btn-primary">Enviar enlace de recuperación</button>
      </form>
    </div>
  </div>

</body>
</html>
