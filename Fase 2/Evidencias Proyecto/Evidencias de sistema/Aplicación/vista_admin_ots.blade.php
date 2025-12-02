@extends('layouts.app')

@section('title','Administrador | Visualización de OT')

@push('styles')
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
  @vite(['resources/css/admin_ots.css','resources/js/admin_ots.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
<main class="page page--ots">
  <div class="res-toolbar">
    <a href="{{ route('admin.home') }}" class="res-btn-azul">← Volver</a>
  </div>

  <div class="ots-layout">

    {{-- ================== COLUMNA IZQUIERDA: TABLA ================== --}}
    <section class="card card-main">
      <header class="card-header card-header--flex">
        <div>
          <h1>Órdenes de trabajo</h1>
          <p class="sub">Visualiza y filtra las OT del taller.</p>
        </div>

        <div class="header-actions">
          {{-- Filtros --}}
          <form method="GET"
                action="{{ route('admin.ots.index') }}"
                class="form-filtros-ot">
            <div class="filtros-row">
              <div class="filtro">
                <label for="folio">Folio</label>
                <input id="folio" name="folio" type="text"
                       class="input"
                       placeholder="Buscar folio…"
                       value="{{ $folio }}">
              </div>

              <div class="filtro">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="input">
                  <option value="">(Todos)</option>
                  <option value="ABIERTA"      {{ $estado === 'ABIERTA' ? 'selected' : '' }}>Abierta</option>
                  <option value="EN_EJECUCION" {{ $estado === 'EN_EJECUCION' ? 'selected' : '' }}>En ejecución</option>
                  <option value="CERRADA"      {{ $estado === 'CERRADA' ? 'selected' : '' }}>Cerrada</option>
                </select>
              </div>

              <div class="filtro">
                <label for="mecanico_id">Mecánico</label>
                <select id="mecanico_id" name="mecanico_id" class="input">
                  <option value="">(Todos)</option>
                  @foreach($mecanicosFiltro as $m)
                    <option value="{{ $m->usuario_id }}"
                      {{ (string)$mecanicoId === (string)$m->usuario_id ? 'selected' : '' }}>
                      {{ $m->usuario_nombre }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="filtro filtro--acciones">
                <button type="submit" class="btn btn-primary sm">
                  <i class="bi bi-filter"></i> Filtrar
                </button>
                <a href="{{ route('admin.ots.index') }}" class="btn btn-outline sm">
                  Limpiar
                </a>
              </div>
            </div>
          </form>

          {{-- Exportar CSV (se lleva los mismos filtros del query string) --}}
            <a href="{{ route('admin.ots.export', request()->only(['fecha','estado','mecanico_id'])) }}"
            class="btn btn-outline sm"
            data-click="export-ots">
            Exportar a Excel
            </a>
        </div>
      </header>

      <div class="tabla-wrap">
        <table class="tabla tabla-ots" aria-label="Órdenes de trabajo">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Estado</th>
              <th>Apertura</th>
              <th>Cierre</th>
              <th>Prioridad</th>
              <th>Origen</th>
              <th>Patente</th>
              <th>Mecánico</th>
            </tr>
          </thead>
          <tbody>
            @forelse($ots as $ot)
              <tr>
                <td class="col-folio" data-folio="{{ $ot->folio }}">{{ $ot->folio }}</td>
                <td>{{ $ot->estado }}</td>
                <td>{{ optional($ot->apertura_ts)->format('d-m-Y H:i') }}</td>
                <td>{{ optional($ot->cierre_ts)->format('d-m-Y H:i') ?? '—' }}</td>
                <td>{{ $ot->prioridad }}</td>
                <td>{{ $ot->origen }}</td>
                <td class="col-patente">{{ $ot->vehiculo->vehiculo_patente ?? '—' }}</td>
                <td>{{ optional($ot->mecanico)->usuario_nombre ?? 'Sin asignar' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center muted">
                  No se encontraron OT con los filtros seleccionados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="paginacion">
        {{ $ots->links() }}
      </div>
    </section>

    {{-- ================== COLUMNA DERECHA: RANKING MECÁNICOS ================== --}}
    <aside class="panel panel-side">
      <div class="panel-card">
        <h3>Visualizaciones</h3>

        <div class="chart-card">
          <h6 class="chart-title">OT por Estado</h6>
          <div style="height:220px;">
            <canvas id="estadoChart"></canvas>
          </div>
        </div>

        <div class="chart-card" style="margin-top:12px;">
          <h6 class="chart-title">OT cerradas por mecánico</h6>
          <div style="height:220px;">
            <canvas id="mecanicoChart"></canvas>
          </div>
        </div>
      </div>

      <div class="panel-card" style="margin-top:12px;">
        <h3>OT cerradas por mecánico (lista)</h3>
        @if($ranking->isEmpty())
          <p class="muted">Aún no hay OT cerradas.</p>
        @else
          <ul class="ranking-list">
            @foreach($ranking as $row)
              <li class="ranking-item">
                <div class="rk-main">
                  <span class="rk-nombre">{{ $row['nombre'] }}</span>
                  <span class="rk-count">{{ $row['total_cerradas'] }} OT</span>
                </div>
                @if($row['email'])
                  <div class="rk-email">{{ $row['email'] }}</div>
                @endif
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </aside>
<div id="toast-area" class="toast-area"></div>
  </div> {{-- .ots-layout --}}
</main>

@endsection
<script>
  // Datos preprocesados en controller
  const OTS_POR_ESTADO = @json($otsPorEstado);
  const OTS_POR_MECANICO = @json($otsPorMecanico);
</script>
