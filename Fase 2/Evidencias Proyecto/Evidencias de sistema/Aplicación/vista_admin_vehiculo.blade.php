@extends('layouts.app')

@section('title','Administrador | Gestión de vehículos')

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite([
    'resources/css/admin_vehiculo.css','resources/js/admin_vehiculo.js',
    'resources/css/navbar.css','resources/js/navbar.js'
  ])
@endpush

@section('content')
<main class="page page--vehiculos">
  <div class="res-toolbar">
    <a href="{{ url('/administrador') }}" class="res-btn-azul">← Volver</a>
  </div>

  <div class="vehiculos-layout">

    {{-- ===================== IZQUIERDA: TABLA ===================== --}}
    <section class="card vehiculos-card">
      <header class="card-header">
        <h1>Gestión de vehículos</h1>
      </header>

      <div class="tabla-wrap">
        <table class="tabla" id="tabla-vehiculos" aria-label="Tabla de vehículos">
          <colgroup>
            <col style="width:92px">
            <col style="width:140px">
            <col style="width:160px">
            <col style="width:200px">
            <col style="width:90px">
            <col style="width:150px">
            <col style="width:160px">
            <col style="width:150px">
            <col style="width:220px">
          </colgroup>

          <thead>
            <tr>
              <th class="col-acciones">Acciones</th>
              <th>Patente</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Año</th>
              <th>Kilometraje</th>
              <th>Tipo</th>
              <th>Estado</th>
              <th>Chofer</th>
            </tr>
          </thead>

          <tbody id="tbody-vehiculos">
            @forelse($vehiculos as $v)
              @php
                $estado = $v->estado_vehiculo;
                $estadoLabel = ucfirst(str_replace('_',' ', $estado));
                $badgeClass = match ($estado) {
                  'activo'        => 'badge-activo',
                  'en_mantencion' => 'badge-pending',
                  'inactivo'      => 'badge-inactivo',
                  'baja'          => 'badge-danger',
                  default         => 'badge-neutral',
                };
                $tipoNombre   = optional($v->tipo)->tipo_nombre ?? '—';
                $choferNombre = optional($v->chofer)->usuario_nombre ?? 'Sin chofer';
              @endphp

              <tr
                data-id="{{ $v->vehiculo_id }}"
                data-estado="{{ $estado }}"
                data-tipo="{{ Str::lower($tipoNombre) }}"
                data-chofer="{{ Str::lower($choferNombre) }}"
                data-patente="{{ Str::lower($v->vehiculo_patente) }}"
              >
                <td class="acciones">
                  <div class="row-actions">
                    <form id="delv-{{ $v->vehiculo_id }}" class="hidden"
                          action="{{ route('admin.vehiculos.destroy', $v->vehiculo_id) }}"
                          method="POST">
                      @csrf @method('DELETE')
                    </form>

                    <button class="icon-btn danger js-del"
                            title="Eliminar"
                            data-id="{{ $v->vehiculo_id }}"
                            data-patente="{{ $v->vehiculo_patente }}">
                      <i class="bi bi-trash3-fill"></i>
                    </button>

                    <button class="icon-btn edit js-edit"
                            title="Editar"
                            data-id="{{ $v->vehiculo_id }}"
                            data-patente="{{ $v->vehiculo_patente }}"
                            data-marca="{{ $v->vehiculo_marca }}"
                            data-modelo="{{ $v->vehiculo_modelo }}"
                            data-anio="{{ $v->anio }}"
                            data-km="{{ $v->vehiculo_kilometraje_actual }}"
                            data-tipo-id="{{ $v->tipo_vehiculo_id }}"
                            data-estado="{{ $v->estado_vehiculo }}">
                      <i class="bi bi-pencil-fill"></i>
                    </button>
                  </div>
                </td>

                <td>{{ $v->vehiculo_patente }}</td>
                <td>{{ $v->vehiculo_marca ?? '—' }}</td>
                <td>{{ $v->vehiculo_modelo ?? '—' }}</td>
                <td>{{ $v->anio ?? '—' }}</td>
                <td>{{ number_format((int)$v->vehiculo_kilometraje_actual, 0, ',', '.') }}</td>
                <td>{{ $tipoNombre }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $estadoLabel }}</span></td>
                <td>{{ $choferNombre }}</td>
              </tr>
            @empty
              <tr><td colspan="9" class="text-center">No hay vehículos registrados.</td></tr>
            @endforelse
          </tbody>
        </table>

        <div id="empty" class="empty {{ count($vehiculos) ? 'hidden' : '' }}">
          <p>No se encontraron vehículos.</p>
        </div>
      </div>
    </section>

    {{-- ===================== DERECHA: PANEL ===================== --}}
    <aside class="panel">
      <div class="panel-card">
        <h3>Administración</h3>
        <h1></h1>
        <div class="panel-actions">
          <button class="btn btn-primary" id="btn-open-vehiculo">
            <i class="bi bi-plus-circle"></i> Nuevo vehículo
          </button>

          {{-- NUEVO BOTÓN: SUBIR DOCUMENTACIÓN --}}
          <button class="btn btn-outline" id="btn-open-docs" disabled>
            <i class="bi bi-folder-symlink"></i> Subir documentación
          </button>
        </div>
      </div>

      <div class="panel-card">
        <h3>Filtros de búsqueda</h3>
        <h1></h1>
        <form id="filtros-form" class="filtros" onsubmit="return false">
          <div class="filtro">
            <label for="filtro-patente">Búsqueda por patente</label>
            <div class="search">
              <input id="filtro-patente" class="input" type="search" placeholder="Buscar patente…">
              <button type="button" id="btn-buscar"  class="btn btn-primary">Buscar</button>
              <button type="button" id="btn-limpiar" class="btn btn-outline">Limpiar</button>
            </div>
          </div>

          <div class="filtro">
            <label for="filtro-tipo">Tipo de vehículo</label>
            <select id="filtro-tipo" class="input">
              <option value="">(Todos)</option>
              @foreach($tipos as $t)
                <option value="{{ Str::lower($t->tipo_nombre) }}">{{ $t->tipo_nombre }}</option>
              @endforeach
            </select>
          </div>

          <div class="filtro">
            <label for="filtro-estado">Estado</label>
            <select id="filtro-estado" class="input">
              <option value="">(Todos)</option>
              <option value="activo">Activo</option>
              <option value="en_mantencion">En mantención</option>
              <option value="inactivo">Inactivo</option>
              <option value="baja">Baja</option>
            </select>
          </div>

          <div class="filtro">
            <label for="filtro-chofer">Chofer asignado</label>
            <select id="filtro-chofer" class="input">
              <option value="">(Todos)</option>
              <option value="sin chofer">Sin chofer</option>
              @isset($choferes)
                @foreach($choferes as $c)
                  <option value="{{ Str::lower($c->usuario_nombre) }}">{{ $c->usuario_nombre }}</option>
                @endforeach
              @endisset
            </select>
          </div>
        </form>
      </div>
    </aside>
  </div>
</main>

{{-- ===================== MODAL CREAR/EDITAR VEHÍCULO ===================== --}}
<div id="modal-vehiculo" class="modal hidden" aria-hidden="true" role="dialog" aria-labelledby="veh-modal-title">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#modal-vehiculo" aria-label="Cerrar">
      <i class="bi bi-x-lg"></i>
    </button>
    <h2 id="veh-modal-title">Nuevo vehículo</h2>

    <form id="form-vehiculo" class="form-grid" onsubmit="return false" autocomplete="off">
      @csrf

      <label>Patente
        <input id="v_patente" name="vehiculo_patente" class="input" type="text"
               placeholder="ABC123 / ABCD23" maxlength="8"
               pattern="[A-Za-z0-9\-]{5,8}" title="Solo letras/números (5–8)" required>
      </label>

      <label>Marca
        <input id="v_marca" name="vehiculo_marca" class="input" type="text" placeholder="Toyota">
      </label>

      <label>Modelo
        <input id="v_modelo" name="vehiculo_modelo" class="input" type="text" placeholder="Hilux 2.4 D-4D">
      </label>

      <label>Año
        <input id="v_anio" name="anio" class="input" type="number" min="1900" max="2100" step="1">
      </label>

      <label>Kilometraje
        <input id="v_km" name="vehiculo_kilometraje_actual" class="input" type="number" min="0" step="1">
      </label>

      <label>Tipo de vehículo
        <select id="v_tipo" name="tipo_vehiculo_id" class="input" required>
          <option value="" disabled selected>Seleccionar</option>
          @foreach($tipos as $t)
            <option value="{{ $t->tipo_vehiculo_id }}">{{ $t->tipo_nombre }}</option>
          @endforeach
        </select>
      </label>

      <label>Estado
        <select id="v_estado" name="estado_vehiculo" class="input" required>
          <option value="activo">Activo</option>
          <option value="en_mantencion">En mantención</option>
          <option value="inactivo">Inactivo</option>
          <option value="baja">Baja</option>
        </select>
      </label>

      <div class="form-actions">
        <button type="button" class="btn" data-close="#modal-vehiculo">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar-vehiculo">Guardar</button>
      </div>
    </form>
  </div>
</div>

{{-- ===================== MODAL DOCUMENTACIÓN VEHÍCULO ===================== --}}
<div id="modal-documentos" class="modal hidden" aria-hidden="true" role="dialog" aria-labelledby="docs-modal-title">
  <div class="modal-dialog modal-dialog--lg">
    <button class="modal-close" data-close="#modal-documentos" aria-label="Cerrar">
      <i class="bi bi-x-lg"></i>
    </button>

    <h2 id="docs-modal-title">
      Documentación del vehículo <span id="doc-vehiculo-label">—</span>
    </h2>

    {{-- Formulario subir / editar documento --}}
    <form id="form-doc" class="form-grid form-doc" onsubmit="return false" autocomplete="off">
      @csrf
      <input type="hidden" id="doc_id" name="doc_id" value="">

      <label>Tipo de documento
        <select id="doc_tipo" name="tipo" class="input" required>
          <option value="" disabled selected>Seleccionar</option>
          <option value="PERMISO_CIRCULACION">Permiso de circulación</option>
          <option value="REVISION_TECNICA">Revisión técnica</option>
          <option value="SEGURO_OBLIGATORIO">Seguro obligatorio (SOAP)</option>
          <option value="GASES">Gases</option>
          <option value="PADRON">Padrón</option>
          <option value="OTRO">Otro</option>
        </select>
      </label>

      <label>Fecha emisión
        <input type="date" id="doc_emision" name="fecha_emision" class="input">
      </label>

      <label>Fecha vencimiento
        <input type="date" id="doc_vencimiento" name="fecha_vencimiento" class="input">
      </label>

      <label class="full">Archivo (imagen o PDF)
        <input type="file" id="doc_archivo" name="archivo" class="input" accept="image/*,.pdf">
      </label>

      <label class="full">Descripción (opcional)
        <input type="text" id="doc_descripcion" name="descripcion" class="input" placeholder="Ej: Permiso 2025 sucursal Maipú">
      </label>

      <div class="form-actions">
        <button type="button" class="btn" id="btn-doc-cancelar">Limpiar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar-doc">Guardar documento</button>
      </div>
    </form>

    {{-- Tabla documentos ya subidos --}}
    <div class="block block-docs-list">
      <div class="block-header">
        <h3>Documentos cargados</h3>
      </div>

      <div class="tabla-wrap">
        <table class="tabla tabla-docs">
          <thead>
            <tr>
              <th>Tipo</th>
              <th>Emisión</th>
              <th>Vencimiento</th>
              <th>Archivo</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-docs">
            <tr>
              <td colspan="5" class="text-center muted">Selecciona un vehículo para ver su documentación.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<div id="toast-area" class="toast-area"></div>
@endsection
