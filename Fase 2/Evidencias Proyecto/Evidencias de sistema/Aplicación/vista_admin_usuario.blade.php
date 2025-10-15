@extends('layouts.app')

@section('title','Administrador | Gestión de usuarios')

@push('styles')
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/admin_usuario.css', 'resources/js/admin_usuario.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <main class="page">
    <div class="usuarios-layout">
      <section class="card usuarios-card">
        <header class="card-header">
          <h1>Gestión de usuarios</h1>
        </header>

        {{-- Mensajería --}}
        @if (session('success'))
          <div class="alert success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="alert error">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
          <div class="alert error">
            <ul>
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="filtros filtros--mobile">
          <div class="filtro">
            <label for="filtro-rol-m">Búsqueda por rol</label>
            <select id="filtro-rol-m" class="input">
              <option value="">Seleccionar</option>
              <option>Mecánico</option>
              <option>Admin</option>
              <option>Jefe de taller</option>
              <option>Chofer</option>
              <option>Guardia</option>
              <option>Recepcionista</option>
            </select>
          </div>
          <div class="filtro">
            <label for="filtro-nombre-m">Búsqueda por nombre</label>
            <div class="search">
              <input id="filtro-nombre-m" class="input" type="search" placeholder="Buscar…">
              <button class="btn btn-light" id="btn-buscar-m">🔎</button>
            </div>
          </div>
        </div>

        <div class="tabla-wrap">
          <table class="tabla" id="tabla-usuarios">
            {{-- define anchos de columnas --}}
            <colgroup>
              <col style="width:120px">   
              <col>                       
              <col>                       
              <col>                       
              <col>                       
              <col>                       
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
                <tr>
                  <td class="acciones">
                    <div class="row-actions">
                      <button type="button"class="icon-btn view" title="Ver" data-id="{{ $u->usuario_id }}">
                        <i class="bi bi-eye-fill"></i>
                      </button>

                      <button type="button" class="icon-btn edit" title="Editar" data-id="{{ $u->usuario_id }}">
                        <i class="bi bi-pencil-fill"></i>
                      </button>
                      <form action="{{ route('admin.usuarios.destroy', $u->usuario_id) }}"method="POST" class="inline delete-form" onsubmit="return confirm('¿Eliminar al usuario {{ $u->usuario_nombre }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn danger" title="Eliminar">
                          <i class="bi bi-trash3-fill"></i>
                        </button>
                      </form>
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
                <tr>
                  <td colspan="6" class="text-center">No hay usuarios registrados.</td>
                </tr>
              @endforelse
            </tbody>
          </table>

          <div id="empty" class="empty hidden">
            <p>No se encontraron usuarios.</p>
          </div>
        </div>

      </section>
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
          <div class="filtros">
            <div class="filtro">
              <label for="filtro-rol">Búsqueda por rol</label>
              <select id="filtro-rol" class="input">
                <option value="">Seleccionar</option>
                <option>Mecánico</option>
                <option>Admin</option>
                <option>Jefe de taller</option>
                <option>Chofer</option>
                <option>Guardia</option>
                <option>Recepcionista</option>
              </select>
            </div>

            <div class="filtro">
              <label for="filtro-nombre">Búsqueda por nombre</label>
              <div class="search">
                <input id="filtro-nombre" class="input" type="search" placeholder="Buscar…">
                <button class="btn btn-light" id="btn-buscar"><i class="bi bi-search"></i></button>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <div id="modal" class="modal hidden">
    <div class="modal-dialog">
      <button class="modal-close" id="modal-close"><i class="bi bi-x-lg"></i></button>
      <h2>Agregar usuario</h2>

      <form method="POST" action="{{ route('admin.usuarios.store') }}" class="form-grid" autocomplete="off">
        @csrf

        <label>Nombre completo
          <input name="usuario_nombre" type="text" class="input"
                 value="{{ old('usuario_nombre') }}"
                 placeholder="Nombre Apellido" required>
        </label>

        <label>Correo
          <input name="usuario_email" type="email" class="input"
                 value="{{ old('usuario_email') }}"
                 placeholder="correo@ejemplo.com" required>
        </label>

        <label>Contraseña
          <input id="u_pass" name="usuario_password" type="password" class="input"
                 placeholder="Mínimo 8 caracteres" minlength="8" required>
        </label>

        <label>Confirmar contraseña
          <input id="u_pass_confirmation" name="usuario_password_confirmation" type="password"
                 class="input" placeholder="Repite la contraseña" minlength="8" required>
        </label>

        <label>Teléfono
          <input name="usuario_telefono" type="tel" class="input"
                 value="{{ old('usuario_telefono') }}"
                 placeholder="+56 9 1234 5678">
        </label>

        <label>Rol
          <select name="roles[]" class="input" required>
            <option value="" disabled @selected(old('roles.0')===null)>Seleccionar</option>
            @foreach($roles as $r)
              <option value="{{ $r->rol_id }}" @selected(old('roles.0') == $r->rol_id)>
                {{ $r->rol_nombre }}
              </option>
            @endforeach
          </select>
        </label>

        <label>Estado
          <select name="usuario_estado" class="input" required>
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

  <div id="toast-area" class="toast-area"></div>
@endsection

@push('scripts')
  @vite(['resources/js/admin_usuario.js','resources/js/navbar.js'])

  {{-- Abrir modal automáticamente si hubo errores de validación --}}
  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const m = document.getElementById('modal');
        m && m.classList.remove('hidden');
      });
    </script>
  @endif
@endpush




