@extends('layouts.app')

@section('title','Administrador')

@push('styles')
  @vite(['resources/css/vista_jefe_taller.css'])
  @vite(['resources/css/navbar.css'])
@endpush

@section('content')
  <main class="page jt-page">
    <header class="jt-header">
      <h1>Panel Jefe de Taller</h1>
    </header>

    <section class="jt-grid">
      <!-- KPIs -->
      <article class="card kpi">
        <h3>OTs abiertas</h3>
        <div class="kpi-value" id="kpiAbiertas">--</div>
        <small class="muted">Pendiente/En proceso</small>
      </article>

      <article class="card kpi">
        <h3>En ejecución</h3>
        <div class="kpi-value" id="kpiEjecucion">--</div>
        <small class="muted">Con mecánico asignado</small>
      </article>


      <article class="card kpi ok">
        <h3>OTs finalizadas hoy</h3>
        <div class="kpi-value" id="kpiFinalizadas">--</div>
        <small class="muted">Últimas 24h</small>
      </article>

        <!-- Calendario simple -->
      <article class="card">
        <header class="card-header"><h2>Agenda del taller (hoy)</h2></header>
        <ul class="list" id="listaAgenda"><!-- JS render --></ul>
      </article>

      <!-- Tabla OTs -->
      <article class="card span-2">
        <header class="card-header">
          <h2>Órdenes de trabajo</h2>
          <div class="filters">
            <input id="filtroPatente" type="text" placeholder="Filtrar por patente…">
            <select id="filtroEstado">
              <option value="">Estado: todos</option>
              <option value="pendiente">Pendiente</option>
              <option value="en_proceso">En proceso</option>
              <option value="finalizada">Finalizada</option>
            </select>
          </div>
        </header>
        <div class="table-wrap">
          <table class="tbl" id="tblOTs">
            <thead>
              <tr>
                <th>Folio</th><th>Patente</th><th>Prioridad</th><th>Estado</th><th>Asignado a</th><th>Acciones</th>
              </tr>
            </thead>
            <tbody><!-- JS render --></tbody>
          </table>
        </div>
      </article>
    </section>
  </main>

  <!-- Modal asignación -->
  <dialog id="dlgAsignar" class="dlg">
    <form method="dialog" class="dlg-card">
      <header><h3>Asignar mecánico</h3></header>
      <div class="dlg-body">
        <input type="hidden" id="dlgOtId">
        <label class="fld">
          <span>Seleccione mecánico</span>
          <select id="dlgMecanico"></select>
        </label>
      </div>
      <footer class="dlg-actions">
        <button class="btn ghost" value="cancel">Cancelar</button>
        <button class="btn" id="btnAsignarConfirm" value="ok">Asignar</button>
      </footer>
    </form>
  </dialog>
@endsection

@push('scripts')
  @vite(['resources/js/navbar.js'])
  @vite(['resources/js/vista_jefe_taller.js'])
@endpush
