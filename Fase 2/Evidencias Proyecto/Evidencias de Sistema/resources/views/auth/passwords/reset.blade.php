<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Restablecer contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/password_reset.css','resources/js/password_reset.js'])
</head>
<body>

  <div class="page">
    <div class="toolbar">
      <a class="btn-back" href="{{ url('/login') }}">← Volver</a>
    </div>

    <div class="card">
      <h2 class="title">Restablecer contraseña</h2>
      <p class="desc">Ingresa tu correo y define una nueva contraseña.</p>

      {{-- errores del backend --}}
      @if ($errors->any())
        <ul class="err" style="margin:8px 0 0 18px;color:#ef4444">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      @endif

      {{-- opcional: caja de "éxito" local (normalmente tu backend redirige a /login) --}}
      <div id="doneBox" class="alert success hidden">Procesando…</div>

      <form id="resetForm" method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="group">
          <label for="email">Correo</label>
          <input id="email" name="email" type="email" autocomplete="email" required>
          <small class="msg" data-for="email"></small>
        </div>

        <div class="group">
          <label for="password">Nueva contraseña</label>
          <div class="pass-wrap">
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            <button class="toggle" type="button" aria-label="Mostrar/ocultar">👁️</button>
          </div>
          <small class="msg" data-for="password"></small>
        </div>

        <div class="group">
          <label for="password_confirmation">Confirmar contraseña</label>
          <div class="pass-wrap">
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="toggle" type="button" aria-label="Mostrar/ocultar">👁️</button>
          </div>
          <small class="msg" data-for="password_confirmation"></small>
        </div>

        <button id="btnReset" type="submit" class="btn-primary">Guardar nueva contraseña</button>
      </form>
    </div>
  </div>

</body>
</html>
