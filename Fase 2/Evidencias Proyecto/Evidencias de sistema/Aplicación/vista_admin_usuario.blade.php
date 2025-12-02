@extends('layouts.app')

@section('title','Administrador | Gestión de usuarios')

@push('styles')
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
  @vite(['resources/css/admin_usuario.css','resources/js/admin_usuario.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
<main class="page">
    <div class="res-toolbar">
      <a href="{{ url('/administrador') }}" class="res-btn-azul">← Volver</a>
    </div>
    
  <div class="usuarios-layout">
    {{-- ================= LISTA / TABLA ================= --}}
    <section class="card usuarios-card">
      <header class="card-header">
        <h1>Gestión de usuarios</h1>
      </header>

      {{-- Mensajes --}}
      @if (session('success')) <div class="alert success">{{ session('success') }}</div> @endif
      @if (session('error'))   <div class="alert error">{{ session('error') }}</div>   @endif
      @if ($errors->any())
        <div class="alert error">
          <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="tabla-wrap">
        <table class="tabla" id="tabla-usuarios">
          <colgroup>
            <col style="width:120px"><col><col><col><col><col>
          </colgroup>
          <thead>
            <tr>
              <th>Acciones</th>
              <th>Nombre completo</th>
              <th>Rol</th>
              <th>Correo</th>
              <th>Teléfono</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="tbody-usuarios">
            @forelse($usuarios as $u)
              @php
                $roleNames   = $u->roles->pluck('rol_nombre')->map(fn($r)=>mb_strtoupper($r))->toArray();
                $eliminables = ['CHOFER','RECEPCIONISTA','GUARDIA'];
                $isDeletable = count(array_intersect($roleNames, $eliminables)) > 0;
                $estaActivo  = (int)$u->usuario_estado === 1;
                $nextEstado  = $estaActivo ? 0 : 1;
              @endphp
              <tr class="user-row"
                  data-id="{{ $u->usuario_id }}"
                  data-nombre="{{ $u->usuario_nombre }}"
                  data-email="{{ $u->usuario_email }}"
                  data-telefono="{{ $u->usuario_telefono ?? '—' }}"
                  data-roles="{{ $u->roles->pluck('rol_nombre')->implode(', ') }}"
                  data-estado="{{ $u->usuario_estado ? 'Activo' : 'Inactivo' }}"
                  data-estado-bool="{{ (int)$u->usuario_estado }}"  {{-- ➕ útil para el modal editar --}}
                  data-creado="{{ optional($u->created_at)->format('d/m/Y H:i') }}">
                <td class="acciones">
                  <div class="row-actions">
                    {{-- Ver --}}
                    <button type="button" class="icon-btn view js-open-view" title="Ver" data-id="{{ $u->usuario_id }}">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                    {{-- Editar (ahora con ESTADO editable) --}}
                    <button type="button" class="icon-btn edit js-open-edit" title="Editar" data-id="{{ $u->usuario_id }}">
                      <i class="bi bi-pencil-fill"></i>
                    </button>

                    @if ($isDeletable)
                      <form id="del-{{ $u->usuario_id }}" action="{{ route('admin.usuarios.destroy',$u->usuario_id) }}" method="POST" class="hidden">
                        @csrf @method('DELETE')
                      </form>
                      <button type="button" class="icon-btn danger js-open-delete"
                              title="Eliminar"
                              data-id="{{ $u->usuario_id }}"
                              data-nombre="{{ $u->usuario_nombre }}">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    @else
                      <button type="button"
                              class="icon-btn lock js-open-toggle"
                              title="{{ $estaActivo ? 'Desactivar' : 'Reactivar' }}"
                              data-id="{{ $u->usuario_id }}"
                              data-nombre="{{ $u->usuario_nombre }}"
                              data-estado-next="{{ $nextEstado }}"
                              data-action="{{ route('admin.usuarios.toggle',$u->usuario_id) }}">
                        <i class="bi {{ $estaActivo ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                      </button>
                    @endif
                  </div>
                </td>
                <td>{{ $u->usuario_nombre }}</td>
                <td>
                  @foreach($u->roles as $r)
                    {{ $r->rol_nombre }}@if(!$loop->last), @endif
                  @endforeach
                </td>
                <td>{{ $u->usuario_email }}</td>
                <td>{{ $u->usuario_telefono ?? '—' }}</td>
                <td>
                  @if($u->usuario_estado)
                    <span class="badge badge-activo">Activo</span>
                  @else
                    <span class="badge badge-inactivo">Inactivo</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center">No hay usuarios registrados.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- ================= PANEL DERECHO ================= --}}
  <aside class="panel">
    <div class="panel-card">
      <h3>Administración de usuarios</h3>
      <div class="panel-actions">
        <button class="btn btn-outline" id="btn-reset-masivo">Resetear contraseña</button>
        <button class="btn btn-primary" id="btn-abrir-modal">Agregar nuevo usuario</button>
      </div>
    </div>

    <div class="panel-card">
      <h3>Filtros de búsqueda</h3>
      <form id="filtros-form" method="GET" action="{{ route('admin.usuarios.index') }}" class="filtros">
        <div class="filtro">
          <label for="filtro-rol">Búsqueda por rol</label>
          <select id="filtro-rol" name="rol" class="input">
            <option value="">Seleccionar</option>
            @foreach($roles as $r)
              <option value="{{ $r->rol_id }}" {{ (string)($rol ?? '') === (string)$r->rol_id ? 'selected' : '' }}>
                {{ $r->rol_nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="filtro">
          <label for="filtro-nombre">Búsqueda por nombre</label>
          <div class="search">
            <input id="filtro-nombre" name="q" class="input" type="search" value="{{ $q ?? '' }}" placeholder="Buscar nombre">
            <button id="btn-buscar" class="btn btn-primary" type="submit">Buscar</button>
            <a id="btn-limpiar-filtros" class="btn btn-outline" href="{{ route('admin.usuarios.index') }}">Limpiar</a>
          </div>
        </div>
      </form>
    </div>

    <div class="panel-card">
      <h3>Asignar chofer a vehículo</h3>
      <form method="POST" action="{{ route('admin.asignarChofer') }}" class="form-grid" style="gap:10px">
        @csrf

        <label>Seleccionar vehículo
          <select name="vehiculo_id" class="input" required>
            <option value="">-- Seleccionar vehículo --</option>
            @foreach(\App\Models\Vehiculo::orderBy('vehiculo_patente')->get() as $v)
              <option value="{{ $v->vehiculo_id }}">{{ $v->vehiculo_patente }}</option>
            @endforeach
          </select>
        </label>

        <label>Seleccionar chofer
          <select name="usuario_id" class="input" required>
            <option value="">-- Seleccionar chofer --</option>
            @foreach($usuarios->filter(fn($u) => $u->roles->contains('rol_nombre','Chofer')) as $ch)
              <option value="{{ $ch->usuario_id }}">{{ $ch->usuario_nombre }}</option>
            @endforeach
          </select>
        </label>

        <button type="submit" class="btn btn-primary" style="margin-top:5px;">
          <i class="bi bi-person-check"></i> Asignar chofer
        </button>
      </form>
    </div>
  </aside>

  </div>
</main>

{{-- ================= MODALES ================= --}}

{{-- Modal: Crear usuario --}}
<div id="modal" class="modal hidden">
  <div class="modal-dialog">
    <button class="modal-close" id="modal-close"><i class="bi bi-x-lg"></i></button>
    <h2>Agregar usuario</h2>

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="form-grid" autocomplete="off">
      @csrf
      <label>Nombre completo
        <input name="usuario_nombre" type="text" class="input" value="{{ old('usuario_nombre') }}" placeholder="Nombre Apellido" required>
      </label>

      <label>Correo
        <input name="usuario_email" type="email" class="input" value="{{ old('usuario_email') }}" placeholder="correo@ejemplo.com" required>
      </label>

      <label>Contraseña
        <input id="u_pass" name="usuario_password" type="password" class="input" placeholder="Mínimo 8 caracteres" minlength="8" required>
      </label>

      <label>Confirmar contraseña
        <input id="u_pass_confirmation" name="usuario_password_confirmation" type="password" class="input" placeholder="Repite la contraseña" minlength="8" required>
      </label>

      <label>Teléfono
        <input name="usuario_telefono" type="tel" class="input" value="{{ old('usuario_telefono') }}" placeholder="+56 9 1234 5678">
      </label>

      <label>Rol
        <select name="roles[]" class="input" required>
          <option value="" disabled @selected(old('roles.0')===null)>Seleccionar</option>
          @foreach($roles as $r)
            <option value="{{ $r->rol_id }}" @selected(old('roles.0') == $r->rol_id)>{{ $r->rol_nombre }}</option>
          @endforeach
        </select>
      </label>

      <label>Estado
        <select name="usuario_estado" id="create_estado" class="input" required>
          <option value="1" @selected(old('usuario_estado','1')=='1')>Activo</option>
          <option value="0" @selected(old('usuario_estado')=='0')>Inactivo</option>
        </select>
      </label>

      <div class="form-actions">
        <button class="btn" id="modal-cancelar" type="button">Cancelar</button>
        <button class="btn btn-primary" type="submit" id="modal-guardar">Guardar</button>
      </div>
    </form>
  </div>
</div>

<div id="modal-edit" class="modal hidden">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#modal-edit"><i class="bi bi-x-lg"></i></button>
    <h2>Editar usuario</h2>
    <div id="edit-error" class="alert error hidden" style="margin-bottom:8px;"></div>

    <form id="form-edit" method="POST" class="form-grid">
      @csrf @method('PUT')

      <label>Nombre completo
        <input name="usuario_nombre" id="edit_nombre" type="text" class="input" required>
      </label>

      <label>Correo
        <input name="usuario_email" id="edit_email" type="email" class="input" required>
      </label>

      <label>Teléfono
        <input name="usuario_telefono" id="edit_telefono" type="tel" class="input">
      </label>

      <label>Rol
        <select name="roles[]" id="edit_roles" class="input" required>
          @foreach($roles as $r)
            <option value="{{ $r->rol_id }}">{{ $r->rol_nombre }}</option>
          @endforeach
        </select>
      </label>

      
      <div class="form-actions">
        <button class="btn" type="button" data-close="#modal-edit">Cancelar</button>
        <button class="btn btn-primary" type="submit">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Ver usuario --}}
<div id="modal-view" class="modal hidden">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#modal-view"><i class="bi bi-x-lg"></i></button>
    <h2>Detalles del usuario</h2>

    <div class="user-details">
      <p><strong>Nombre:</strong> <span id="view_nombre"></span></p>
      <p><strong>Correo:</strong> <span id="view_email"></span></p>
      <p><strong>Teléfono:</strong> <span id="view_telefono"></span></p>
      <p><strong>Rol(es):</strong> <span id="view_roles"></span></p>
      <p><strong>Estado:</strong> <span id="view_estado"></span></p>
      <p><strong>Fecha de creación:</strong> <span id="view_creado"></span></p>
    </div>

    <div class="form-actions">
      <button class="btn" type="button" data-close="#modal-view">Cerrar</button>
    </div>
  </div>
</div>

{{-- Modal: Resetear contraseña --}}
<div id="modal-reset" class="modal hidden">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#modal-reset"><i class="bi bi-x-lg"></i></button>
    <h2>Resetear contraseña</h2>
    <div id="reset-error" class="alert error hidden" style="margin-bottom:8px;"></div>

    <form id="form-reset" method="POST" class="form-grid" autocomplete="off">
      @csrf @method('PUT')
      {{-- La action se setea por JS con el usuario seleccionado --}}
      <label>Nueva contraseña
        <input id="reset_pass" name="usuario_password" type="password" class="input" minlength="8" required placeholder="Mínimo 8 caracteres">
      </label>
      <label>Confirmar nueva contraseña
        <input id="reset_pass_conf" name="usuario_password_confirmation" type="password" class="input" minlength="8" required placeholder="Repite la contraseña">
      </label>
      <div class="form-actions">
        <button class="btn" type="button" data-close="#modal-reset">Cancelar</button>
        <button class="btn btn-primary" type="submit">Cambiar contraseña</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal: Confirmación genérica (solo eliminar) --}}
<div id="confirm-modal" class="modal hidden">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#confirm-modal"><i class="bi bi-x-lg"></i></button>
    <h2 id="confirm-title">Confirmación</h2>
    <p id="confirm-text" style="margin:10px 0 16px;"></p>
    <div class="form-actions">
      <button class="btn" type="button" data-close="#confirm-modal">Cancelar</button>
      <button class="btn btn-primary" type="button" id="confirm-accept">Confirmar</button>
    </div>
  </div>
</div>

{{-- Modal: Activar/Desactivar (candado) --}}
<div id="modal-toggle" class="modal hidden">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#modal-toggle"><i class="bi bi-x-lg"></i></button>
    <h2 id="toggle-title">Estado de la cuenta</h2>

    <div id="toggle-error" class="alert error hidden" style="margin-bottom:8px;"></div>

    <form id="form-toggle" method="POST" class="form-grid" autocomplete="off">
      @csrf
      @method('PUT')

      <input type="hidden" name="usuario_estado" id="toggle_estado">  {{-- 0 o 1 --}}

      <div id="toggle-info" class="alert info" style="margin:6px 0"></div>

      {{-- Solo si vamos a ACTIVAR (0→1) --}}
      <div id="toggle_password_block" class="hidden">
        <label>Nueva contraseña
          <input id="toggle_pass" name="usuario_password" type="password"
                 class="input" minlength="8" placeholder="Mínimo 8 caracteres">
        </label>
        <label>Confirmar contraseña
          <input id="toggle_pass_conf" name="usuario_password_confirmation" type="password"
                 class="input" minlength="8" placeholder="Repite la contraseña">
        </label>
      </div>

      <div class="form-actions">
        <button class="btn" type="button" data-close="#modal-toggle">Cancelar</button>
        <button class="btn btn-primary" type="submit" id="toggle-submit">Confirmar</button>
      </div>
    </form>
  </div>
</div>

<div id="toast-area" class="toast-area"></div>

<div id="toggle-flags"
     data-open="{{ session('open_modal') }}"
     data-error="{{ session('toggle_error') }}"></div>

@endsection

@push('scripts')
  @vite(['resources/js/admin_usuario.js','resources/js/navbar.js'])

  {{-- Reabrir modal crear si hubo errores de validación del create --}}
  @if ($errors->any() && old('_token') && request()->routeIs('admin.usuarios.store'))
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('modal')?.classList.remove('hidden');
      });
    </script>
  @endif

  {{-- Reabrir modal toggle si viene error de misma contraseña, etc. --}}
  @if (session('open_modal') || session('toggle_error'))
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const which = @json(session('open_modal'));
        const toggleErr = @json(session('toggle_error'));
        if (which === 'toggle') {
          const modal = document.getElementById('modal-toggle');
          const errBox = document.getElementById('toggle-error');
          modal?.classList.remove('hidden');
          if (errBox && toggleErr) {
            errBox.textContent = toggleErr;
            errBox.classList.remove('hidden');
          }
        }
      });
    </script>
  @endif
@endpush
