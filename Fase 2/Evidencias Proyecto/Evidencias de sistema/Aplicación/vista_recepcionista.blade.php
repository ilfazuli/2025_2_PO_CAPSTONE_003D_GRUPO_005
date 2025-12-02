@extends('layouts.app')

@section('title','Recepción | Solicitudes de ingreso')

@push('styles')
  @vite(['resources/css/recepcionista.css','resources/js/recepcionista.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
<main class="page">
  <div class="layout recep-layout">

    {{-- ================== COLUMNA PRINCIPAL ================== --}}
    <section class="card card-main">
      <header class="card-header">
        <div>
          <h1>Solicitudes de ingreso</h1>
          <p class="sub">Recepción de vehículos que el guardia envía al taller.</p>
        </div>

        <form method="GET" action="{{ route('recep.home') }}" class="filtro-fecha">
          <label for="fecha" class="lbl-fecha">Fecha</label>
          <input
            id="fecha"
            type="date"
            name="fecha"
            class="input"
            value="{{ request('fecha', $fecha ?? now()->toDateString()) }}"
          >
          <button type="submit" class="btn btn-outline sm">Filtrar</button>
        </form>
      </header>

      {{-- Mensajes flash --}}
      @if (session('success'))
        <div class="alert success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert error">{{ session('error') }}</div>
      @endif

      {{-- =============== PENDIENTES =============== --}}
      <div class="block">
        <div class="block-header">
          <h2>Solicitudes pendientes</h2>
          <span class="badge badge-tag">
            {{ count($pendientes ?? []) }} pendiente(s)
          </span>
        </div>

        <div class="tabla-wrap">
          <table class="tabla tabla-solicitudes">
            <thead>
              <tr>
                <th>Patente</th>
                <th>Chofer</th>
                <th>Hora entrada</th>
                <th>Origen</th>
                <th class="col-acciones">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($pendientes ?? []) as $sol)
                @php
                    $patente = optional($sol->vehiculo)->vehiculo_patente
                              ?? ($sol->patente_manual ?? '—');

                    // Chofer: primero desde agendamiento, luego desde vehículo
                    if ($sol->agendamiento && $sol->agendamiento->usuario) {
                        $chofer = $sol->agendamiento->usuario->usuario_nombre;
                    } elseif ($sol->vehiculo && $sol->vehiculo->chofer) {
                        $chofer = $sol->vehiculo->chofer->usuario_nombre;
                    } else {
                        $chofer = '—';
                    }

                    $horaEntrada = optional($sol->ingreso_ts)->format('H:i');

                    // Origen: usamos la RELACIÓN, no solo el id
                    $origen = $sol->agendamiento ? 'Con reserva' : 'Sin reserva';
                @endphp


                <tr>
                  <td>{{ $patente }}</td>
                  <td>{{ $chofer }}</td>
                  <td>{{ $horaEntrada }}</td>
                  <td>
                    @if($sol->agendamiento)
                      <span class="badge badge-neutral">Reserva</span>
                    @else
                      <span class="badge badge-pending">Sin reserva</span>
                    @endif
                  </td>
                  <td class="acciones">
                    {{-- ACEPTAR --}}
                    <form method="POST"
                          action="{{ route('recep.solicitud.aceptar', $sol->checkin_id) }}"
                          class="inline-form">
                      @csrf
                      <button type="submit"
                              class="btn btn-primary sm"
                              onclick="return confirm('¿Aceptar ingreso de {{ $patente }} y crear OT?');">
                        Aceptar
                      </button>
                    </form>

                    {{-- RECHAZAR --}}
                    <form method="POST"
                          action="{{ route('recep.solicitud.rechazar', $sol->checkin_id) }}"
                          class="inline-form">
                      @csrf
                      <button type="submit"
                              class="btn btn-outline sm"
                              onclick="return confirm('¿Rechazar solicitud de {{ $patente }}?');">
                        Rechazar
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center muted">
                    No hay solicitudes pendientes para la fecha seleccionada.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- =============== HISTORIAL DEL DÍA =============== --}}
      <div class="block block-historial">
        <div class="block-header">
          <h2>Historial del día</h2>
        </div>
        <div class="tabla-wrap">
          <table class="tabla tabla-historial">
            <thead>
              <tr>
                <th>Patente</th>
                <th>Chofer</th>
                <th>Hora entrada</th>
                <th>Hora decisión</th>
                <th>Estado recepción</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($historial ?? []) as $h)
              @php
                  $patenteH = optional($h->vehiculo)->vehiculo_patente
                            ?? ($h->patente_manual ?? '—');

                  // Misma lógica de prioridad: primero reserva, luego vehículo
                  if ($h->agendamiento && $h->agendamiento->usuario) {
                      $choferH = $h->agendamiento->usuario->usuario_nombre;
                  } elseif ($h->vehiculo && $h->vehiculo->chofer) {
                      $choferH = $h->vehiculo->chofer->usuario_nombre;
                  } else {
                      $choferH = '—';
                  }

                  $entradaH = optional($h->ingreso_ts)->format('H:i');
                  $decision = optional($h->updated_at)->format('H:i');
              @endphp
                <tr>
                  <td>{{ $patenteH }}</td>
                  <td>{{ $choferH }}</td>
                  <td>{{ $entradaH }}</td>
                  <td>{{ $decision }}</td>
                  <td>
                    @if($h->estado_recepcion === 'ACEPTADA')
                      <span class="badge badge-activo">Aceptada</span>
                    @elseif($h->estado_recepcion === 'RECHAZADA')
                      <span class="badge badge-error">Rechazada</span>
                    @else
                      <span class="badge badge-neutral">{{ $h->estado_recepcion }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center muted">
                    Aún no hay decisiones registradas para la fecha seleccionada.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    {{-- ================== COLUMNA LATERAL ================== --}}
    <aside class="side-panel">
      <div class="mini-card">
        <h3>Resumen rápido</h3>
        <ul class="list-resumen">
          <li>
            <span class="lbl">Pendientes:</span>
            <span class="val">{{ count($pendientes ?? []) }}</span>
          </li>
          <li>
            <span class="lbl">Procesadas hoy:</span>
            <span class="val">{{ count($historial ?? []) }}</span>
          </li>
        </ul>
      </div>

      <div class="mini-card">
        <h3>Flujo</h3>
        <ol class="help">
          <li>El guardia registra el vehículo en portería.</li>
          <li>La solicitud aparece aquí como <b>pendiente</b>.</li>
          <li>Al <b>aceptar</b>, se crea la Orden de Trabajo.</li>
          <li>Al <b>rechazar</b>, se marca como solicitud rechazada.</li>
        </ol>
      </div>
    </aside>

  </div>
</main>

<div id="toast-area" class="toast-area"></div>
@endsection
