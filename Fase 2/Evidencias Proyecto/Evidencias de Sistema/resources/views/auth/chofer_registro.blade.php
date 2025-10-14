<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro de Chofer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/chofer_registro.css','resources/js/chofer_registro.js'])
</head>
<body>

  <div class="page">
    <div class="toolbar">
      <a class="btn-back" href="{{ url('/login') }}">← Volver</a>
    </div>

    <div class="card">
      <h2 class="title">Crear cuenta de Chofer</h2>
      <p class="desc">Completa los datos para registrarte como chofer.</p>
      @if ($errors->any())
        <ul class="err" style="margin:8px 0 0 18px;color:#ef4444">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      @endif

      <form id="registerForm" method="POST" action="{{ url('/registro/chofer') }}" novalidate>
        @csrf

        <div class="group">
          <label for="nombre">Nombre completo</label>
          <input id="nombre" name="nombre" type="text" autocomplete="name" required>
          <small class="msg" data-for="nombre"></small>
        </div>

        <div class="group">
          <label for="email">Correo</label>
          <input id="email" name="email" type="email" autocomplete="email" required>
          <small class="msg" data-for="email"></small>
        </div>

        <div class="group">
          <label for="telefono">Teléfono</label>
          <input id="telefono" name="telefono" type="tel" inputmode="numeric" autocomplete="tel" placeholder="+56 9 1234 5678" required>
          <small class="msg" data-for="telefono"></small>
        </div>

        <div class="group">
          <label for="patente">Patente (opcional)</label>
          <input id="patente" name="patente" type="text" placeholder="ABC D12 o AA-BB-11">
          <small class="msg" data-for="patente"></small>
        </div>

        <div class="group">
          <label for="password">Contraseña</label>
          <div class="pass-wrap">
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            <button class="toggle" type="button" aria-label="Mostrar/ocultar">👁️</button>
          </div>
          <small class="msg" data-for="password"></small>

          <div class="checklist">
            <span data-rule="len">Mín. 8 caracteres</span>
            <span data-rule="upper">Mayúscula</span>
            <span data-rule="lower">Minúscula</span>
            <span data-rule="digit">Número</span>
            <span data-rule="spec">Símbolo (!@#$%^&*)</span>
          </div>
        </div>

        <div class="group">
          <label for="password_confirmation">Confirmar contraseña</label>
          <div class="pass-wrap">
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="toggle" type="button" aria-label="Mostrar/ocultar">👁️</button>
          </div>
          <small class="msg" data-for="password_confirmation"></small>
        </div>

        <label class="terms">
          <input type="checkbox" id="terms" name="terms">
          Acepto los términos y condiciones
        </label>
        <small class="msg" data-for="terms"></small>

        <button id="btnRegister" type="submit" class="btn-primary" disabled>Crear cuenta</button>
      </form>
    </div>
  </div>

</body>
</html>
