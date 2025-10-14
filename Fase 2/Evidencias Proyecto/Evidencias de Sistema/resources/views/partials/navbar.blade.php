@php
  $u = auth()->user();
  $displayName = $u?->usuario_nombre ?? $u?->usuario_email ?? 'Usuario';
  // Iniciales: primeras letras de nombre y apellido (o dos primeras del string)
  $parts = preg_split('/\s+/', trim($displayName));
  $ini = '';
  if (count($parts) >= 2) { $ini = mb_substr($parts[0],0,1).mb_substr($parts[1],0,1); }
  else { $ini = mb_substr($parts[0] ?? 'U',0,1).mb_substr($parts[0] ?? 'U',1,1); }
  $ini = mb_strtoupper($ini);
@endphp

<nav class="nv">
  <div class="nv__wrap">
    <a class="nv__brand" href="{{ url('/') }}">
      <span class="nv__logo"></span><span>La Flota Pepsiman</span>
    </a>

    <div class="nv__spacer"></div>

    @auth
    <div class="nv__user">
      <button class="nv__userbtn" id="nvUserBtn" aria-haspopup="true" aria-expanded="false" aria-label="Abrir menú de usuario">
        <span class="nv__avatar" aria-hidden="true">{{ $ini }}</span>
        <span class="nv__name">{{ $displayName }}</span>
        <svg class="nv__chev" viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
          <path d="M5 7l5 6 5-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </button>

      <div class="nv__menu" id="nvUserMenu" role="menu" aria-labelledby="nvUserBtn">
        {{-- Cambiar contraseña (crea la ruta cuando tengas la vista) --}}
        <a class="nv__item" role="menuitem" href="#" >Perfil</a>

        <form method="POST" action="{{ route('logout') }}" role="none">
          @csrf
          <button type="submit" class="nv__item nv__danger" role="menuitem">Cerrar sesión</button>
        </form>
      </div>
    </div>
    @endauth

    @guest
      <a class="nv__btn" href="{{ route('login') }}">Entrar</a>
    @endguest
  </div>
</nav>
