@extends('layouts.app')

@section('title','Vista del Mecanico')

@push('styles')
  @vite(['resources/css/vista_mecanico.css', 'vista_mecanico.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <div id="asignacionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" hidden>
    <div class="toast__icon">🔔</div>
    <div class="toast__content">
      <strong>Nuevo vehículo asignado</strong>
      <p>Se te ha asignado una nueva OT.</p>
    </div>
    <button type="button" class="toast__btn" data-toast-dismiss>Aceptar</button>
  </div>

  <div class="page">
    <header class="head">
      <h1 class="title">Lista de vehículos pendientes</h1>
      <a href="{{ url('/mecanico/historial_ot') }}" class="btn btn-outline" id="btnMisOT">Ver mis OT</a>
    </header>

    <!-- Filtros simples (opcional) -->
    <section class="filters" aria-label="filtros">
      <div class="f-row">
        <label>Buscar:</label>
        <input id="filtroTexto" type="text" placeholder="Patente / Nº OT / Motivo">
      </div>
      <div class="f-row">
        <label>Estado:</label>
        <select id="filtroEstado">
          <option value="">Todos</option>
          <option value="pendiente">Pendiente</option>
          <option value="en-curso">En curso</option>
          <option value="pausado">Pausado</option>
        </select>
      </div>
    </section>

    <section id="listaOT" class="ot-list">

      <article class="ot-card" data-estado="pendiente" data-id="OT-0001">
        <header class="ot-head">
          <div><span class="lbl">N° de OT</span><strong>OT-0001</strong></div>
          <div><span class="lbl">Patente</span><strong>ABCZ-12</strong></div>
          <div><span class="lbl">Motivo de ingreso</span><span>Revisión técnica</span></div>
          <div><span class="lbl">Fecha/hora asignación</span><span>12-10-2025 10:30</span></div>
          <div class="estado">
            <span class="lbl">Estado actual</span>
            <span class="badge badge-pend">Pendiente</span>
          </div>
        </header>

        <div class="ot-actions">
          <button class="btn btn-info" data-action="info">Info vehículo</button>
          <button class="btn btn-outline" data-action="comentario">Agregar comentarios</button>
          <button class="btn btn-outline" data-action="foto">Agregar foto</button>
          <button class="btn btn-primary" data-action="iniciar">Iniciar trabajo</button>
          <button class="btn btn-outline" data-action="pausar" disabled>Pausar</button>
          <button class="btn btn-outline" data-action="repuesto">Solicitar repuesto</button>
          <button class="btn btn-danger" data-action="finalizar" disabled>Finalizar</button>
        </div>
      </article>

      <article class="ot-card" data-estado="en-curso" data-id="OT-0002">
        <header class="ot-head">
          <div><span class="lbl">N° de OT</span><strong>OT-0002</strong></div>
          <div><span class="lbl">Patente</span><strong>KJRT-88</strong></div>
          <div><span class="lbl">Motivo de ingreso</span><span>Mantención preventiva</span></div>
          <div><span class="lbl">Fecha/hora asignación</span><span>12-10-2025 09:10</span></div>
          <div class="estado">
            <span class="lbl">Estado actual</span>
            <span class="badge badge-run">En curso</span>
          </div>
        </header>

        <div class="ot-actions">
          <button class="btn btn-info" data-action="info">Info vehículo</button>
          <button class="btn btn-outline" data-action="comentario">Agregar comentarios</button>
          <button class="btn btn-outline" data-action="foto">Agregar foto</button>
          <button class="btn btn-primary" data-action="iniciar" disabled>Iniciar trabajo</button>
          <button class="btn btn-outline" data-action="pausar">Pausar</button>
          <button class="btn btn-outline" data-action="repuesto">Solicitar repuesto</button>
          <button class="btn btn-danger" data-action="finalizar">Finalizar</button>
        </div>
      </article>

      <article class="ot-card" data-estado="pausado" data-id="OT-0003">
        <header class="ot-head">
          <div><span class="lbl">N° de OT</span><strong>OT-0003</strong></div>
          <div><span class="lbl">Patente</span><strong>TT-45-99</strong></div>
          <div><span class="lbl">Motivo de ingreso</span><span>Reparación</span></div>
          <div><span class="lbl">Fecha/hora asignación</span><span>11-10-2025 17:40</span></div>
          <div class="estado">
            <span class="lbl">Estado actual</span>
            <span class="badge badge-pause">Pausado</span>
          </div>
        </header>

        <div class="ot-actions">
          <button class="btn btn-info" data-action="info">Info vehículo</button>
          <button class="btn btn-outline" data-action="comentario">Agregar comentarios</button>
          <button class="btn btn-outline" data-action="foto">Agregar foto</button>
          <button class="btn btn-primary" data-action="iniciar">Reanudar</button>
          <button class="btn btn-outline" data-action="pausar" disabled>Pausar</button>
          <button class="btn btn-outline" data-action="repuesto">Solicitar repuesto</button>
          <button class="btn btn-danger" data-action="finalizar" disabled>Finalizar</button>
        </div>
      </article>

    </section>
  </div>
  @endsection

@push('scripts')
  @vite(['resources/js/vista_mecanico.js'])
@endpush



