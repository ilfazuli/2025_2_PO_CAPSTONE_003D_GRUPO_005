@extends('layouts.app')

@section('title','Administrador | Gestión de vehículos')

@push('styles')
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/admin_vehiculo.css', 'resources/js/admin_vehiculo.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <main class="page">
    <div class="usuarios-layout">
      <!-- IZQUIERDA: TABLA -->
      <section class="card usuarios-card">
        <header class="card-header">
          <h1>Gestión de vehículos</h1>
        </header>

        <div class="filtros filtros--mobile">
          <div class="filtro">
            <label for="filtro-nombre-m">Búsqueda por patente</label>
            <div class="search">
              <input id="filtro-nombre-m" class="input" type="search" placeholder="Buscar…">
              <button class="btn btn-light" id="btn-buscar-m">🔎</button>
            </div>
          </div>
        </div>

        <div class="tabla-wrap">
          <table class="tabla" id="tabla-usuarios">
            <colgroup>
              <col style="width:80px">
              <col style="width:120px">
              <col style="width:130px">
              <col style="width:170px">
              <col style="width:80px">
              <col style="width:150px">
              <col style="width:150px">
              <col style="width:150px">
              <col style="width:160px">
            </colgroup>

            <thead>
              <tr>
                <th class="col-acciones">Acc.</th>
                <th>Patente</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Año</th>
                <th>Kilometraje actual</th>
                <th>Tipo de vehículo</th>
                <th>Estado del vehículo</th>
                <th>Chofer asignado</th>
              </tr>
            </thead>

            <tbody id="tbody-usuarios">
              <tr>
                <td class="acciones">
                  <div class="row-actions">
                    <button class="icon-btn danger" title="Eliminar"><i class="bi bi-trash3-fill"></i></button>
                    <button class="icon-btn edit"   title="Editar"><i class="bi bi-pencil-fill"></i></button>
                  </div>
                </td>
                <td>ABCD23</td>
                <td>Toyota</td>
                <td>Hilux 2.4</td>
                <td>2025</td>
                <td>12000</td>
                <td>Pickup</td>
                <td><span class="badge badge-activo">Operativo</span></td>
                <td class="td-multiline">Miguel Fazio</td>
              </tr>
            </tbody>
          </table>

          <div id="empty" class="empty hidden">
            <p>No se encontró vehículo.</p>
          </div>
        </div>
      </section>

      <aside class="panel">
        <div class="panel-card">
          <h3>Administración de vehículo</h3>
          <div class="panel-actions">
            <button class="btn btn-outline" id="btn-open-docs">Agregar documentación</button>
            <button class="btn btn-primary" id="btn-open-vehiculo">Agregar nuevo vehículo</button>
          </div>
        </div>

        <div class="panel-card">
          <h3>Filtros de búsqueda</h3>
          <div class="filtro">
            <label for="filtro-nombre">Búsqueda por patente</label>
            <div class="search">
              <input id="filtro-nombre" class="input" type="search" placeholder="Buscar…">
              <button class="btn btn-light" id="btn-buscar"><i class="bi bi-search"></i></button>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <div id="modal-vehiculo" class="modal hidden">
    <div class="modal-dialog">
      <button class="modal-close" data-close="#modal-vehiculo"><i class="bi bi-x-lg"></i></button>
      <h2>Agregar vehículo</h2>

      <form class="form-grid" onsubmit="return false">
        <label for="patente">Patente
          <input id="patente" name="patente" class="input" type="text"
                 placeholder="ABC123 / ABCD23" maxlength="8" autocomplete="off"
                 pattern="[A-Za-z0-9\-]{5,8}" title="Solo letras/números (5–8), sin espacios">
        </label>

        <label for="marca">Marca
          <input id="marca" name="marca" class="input" type="text" placeholder="Toyota">
        </label>

        <label for="modelo">Modelo
          <input id="modelo" name="modelo" class="input" type="text" placeholder="Hilux 2.4 D-4D">
        </label>

        <label for="anio">Año
          <input id="anio" name="anio" class="input" type="number" min="1990" max="2100" step="1">
        </label>

        <label for="km">Kilometraje actual
          <input id="km" name="kilometraje" class="input" type="number" min="0" step="1">
        </label>

        <label for="tipo">Tipo de vehículo
          <select id="tipo" name="tipo" class="input">
            <option value="" selected disabled>Seleccionar</option>
            <option value="auto">Auto</option>
            <option value="pickup">Pickup</option>
            <option value="furgon">Furgón</option>
            <option value="camion">Camión</option>
            <option value="bus">Bus</option>
            <option value="otro">Otro</option>
          </select>
        </label>

        <label for="estado">Estado del vehículo
          <select id="estado" name="estado" class="input">
            <option value="operativo" selected>Operativo</option>
            <option value="mantenimiento">En mantenimiento</option>
            <option value="fuera_servicio">Fuera de servicio</option>
            <option value="baja">De baja</option>
          </select>
        </label>

        <label for="asignacion">Asignación
          <select id="asignacion" name="asignacion" class="input">
            <option value="" selected>Sin asignar</option>
            <option value="logistica">Logística</option>
            <option value="ventas">Ventas</option>
            <option value="servicio">Servicio técnico</option>
          </select>
        </label>

        <div class="form-actions">
          <button class="btn" type="button" data-close="#modal-vehiculo">Cancelar</button>
          <button class="btn btn-primary" type="button" id="btn-guardar-vehiculo">Agregar vehículo</button>
        </div>
      </form>
    </div>
  </div>

  <div id="modal-docs" class="modal hidden">
    <div class="modal-dialog">
      <button class="modal-close" data-close="#modal-docs"><i class="bi bi-x-lg"></i></button>
      <h2>Documentación del vehículo</h2>
      <span class="hint">Formatos aceptados: JPG/PNG/PDF (máx. 10 MB)</span>

      <div class="docs-toolbar">
        <button class="btn btn-outline" type="button" id="btn-add-doc-row">
          <i class="bi bi-plus-lg"></i> Añadir documento
        </button>
      </div>

      <div class="docs-grid" id="docs-grid">
        <div class="doc-row" data-doc="">
          <div class="thumb">
            <img class="thumb-img hidden" alt="Vista previa">
            <div class="thumb-placeholder">Vista<br>previa</div>
          </div>

          <div class="upload">
            <input class="input doc-name" type="text" placeholder="Nombre del documento (p. ej., SOAT, Permiso)">
            <div class="upload-line">
              <input class="file-input" type="file" accept=".jpg,.jpeg,.png,.pdf" hidden>
              <button class="btn btn-outline btn-upload" type="button">
                <i class="bi bi-upload"></i> Subir archivo
              </button>
              <span class="filename">Ningún archivo seleccionado</span>
            </div>
          </div>

          <div class="expiry">
            <label>Fecha de vencimiento</label>
            <div class="date-wrap">
              <input type="date" class="input date-input">
              <i class="bi bi-calendar3 calendar-ico" aria-hidden="true"></i>
            </div>
          </div>

          <div class="doc-actions">
            <button class="icon-btn doc-remove" type="button" title="Eliminar fila">
              <i class="bi bi-trash3"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="form-actions center">
        <button class="btn" type="button" data-close="#modal-docs">Cancelar</button>
        <button class="btn btn-primary" type="button" id="btn-guardar-docs">Guardar</button>
      </div>
    </div>
  </div>

  <div id="toast-area" class="toast-area"></div>
@endsection

@push('scripts')
  @vite(['resources/js/admin_vehiculo.js'])
@endpush