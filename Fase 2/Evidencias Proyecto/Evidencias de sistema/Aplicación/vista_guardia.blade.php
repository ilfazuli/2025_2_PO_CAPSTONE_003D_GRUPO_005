@extends('layouts.app')

@section('title','Vista del guardia')

@push('styles')
  @vite(['resources/css/guardia.css','resources/js/guardia.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
<div class="page">
  <div class="layout guardia-layout">

    {{-- =========================================
         IZQUIERDA: Vehículos en el taller
    ========================================== --}}
    <section class="card">
      <header class="card-header">
        <div>
          <h1>Vehículos en el taller</h1>
          <div class="card-subtitle">Fecha: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</div>
        </div>

        <button class="btn btn-primary" id="btn-open-modal-vehiculo">
          + Agregar vehículo
        </button>
      </header>

      {{-- Mensajes flash --}}
      @if (session('success'))
        <div class="alert success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert error">{{ session('error') }}</div>
      @endif

      <div class="tabla-wrap">
        <table class="tabla">
          <thead>
            <tr>
              <th>Patente</th>
              <th>Hora entrada</th>
              <th>Guardia</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
          @forelse(($ingresos ?? []) as $ing)
            <tr>
              <td>{{ $ing->vehiculo->vehiculo_patente ?? '—' }}</td>
              <td>{{ optional($ing->ingreso_ts)->format('H:i') }}</td>
              <td>{{ optional($ing->guardia)->usuario_nombre ?? '—' }}</td>
              <td>
                <span class="badge badge-activo">En taller</span>
              </td>
              <td>
                <form method="POST"
                      action="{{ route('guardia.checkin.finish', $ing->checkin_id) }}"
                      class="inline-form js-finish-form">
                  @csrf
                  @method('PUT')

                  <button type="submit"
                          class="btn btn-primary btn-sm js-terminar-ingreso"
                          data-patente="{{ $ing->vehiculo->vehiculo_patente ?? '' }}">
                    Terminar
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">
                No hay vehículos en el taller.
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- =========================================
         DERECHA: Reservas + Historial
    ========================================== --}}
    <aside class="side-panel">
      <div class="card">
        {{-- Reservas de hoy --}}
        <div class="card-header" style="margin-bottom: 8px;">
          <div>
            <h2 class="reservas-title">Reservas de hoy</h2>
            <div class="reservas-subtitle">Vehículos que deberían llegar al taller.</div>
          </div>
        </div>

        <div class="tabla-wrap">
          <table class="tabla">
            <thead>
              <tr>
                <th>Hora</th>
                <th>Patente</th>
                <th>Chofer</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
            @forelse(($reservas ?? []) as $ag)
              <tr>
                <td>{{ \Carbon\Carbon::parse($ag->hora_inicio)->format('H:i') }}</td>
                <td>{{ $ag->vehiculo->vehiculo_patente ?? '—' }}</td>
                {{-- Chofer de la reserva (agendamiento->usuario) --}}
                <td>{{ optional($ag->usuario)->usuario_nombre ?? '—' }}</td>
                <td>
                  @if($ag->estado_reserva === 'Atendido')
                    <span class="badge badge-neutral">Atendido</span>
                  @elseif($ag->estado_reserva === 'En taller')
                    <span class="badge badge-activo">En taller</span>
                  @else
                    <span class="badge badge-neutral">Pendiente</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">
                  No hay reservas para hoy.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>


        {{-- Historial de vehículos atendidos --}}
        <div class="mt-12">
          <h2 class="reservas-title">Historial de hoy</h2>
          <div class="reservas-subtitle">Vehículos que ya fueron atendidos.</div>
        </div>

        <div class="tabla-wrap mt-8">
          <table class="tabla">
            <thead>
              <tr>
                <th>Patente</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th>Guardia</th>
              </tr>
            </thead>
            <tbody>
            @forelse(($historial ?? []) as $reg)
              <tr>
                <td>{{ $reg->vehiculo->vehiculo_patente ?? '—' }}</td>
                <td>{{ optional($reg->ingreso_ts)->format('H:i') }}</td>
                <td>{{ optional($reg->salida_ts)->format('H:i') }}</td>
                <td>{{ optional($reg->guardia)->usuario_nombre ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">
                  Aún no hay vehículos finalizados hoy.
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </aside>

  </div>
</div>

{{-- =======================
     MODAL: Agregar vehículo
========================== --}}
<div id="modal-vehiculo" class="modal hidden" role="dialog" aria-modal="true">
  <div class="modal-dialog">
    <button class="modal-close" data-close="#modal-vehiculo" aria-label="Cerrar">×</button>
    <h2>Agregar vehículo</h2>

    <form
      method="POST"
      action="{{ route('guardia.checkin.store') }}"
      class="form-grid"
      enctype="multipart/form-data"
      autocomplete="off"
    >
      @csrf

      <label class="full">Patente
        <input name="patente" type="text" class="input"
               placeholder="ABCD23" required maxlength="8"
               pattern="[A-Za-z0-9\-]{5,8}">
      </label>

      <input type="hidden" name="agendamiento_id" value="">

      <label class="full">Fotografías (frontal, lateral, trasera)
        <input name="fotos[]" type="file" class="input" accept="image/*" multiple>
      </label>

      <label>Hora de entrada
        <input name="hora_entrada" type="time" class="input" required>
      </label>

      <div class="form-actions">
        <button type="button" class="btn" data-close="#modal-vehiculo">Cancelar</button>
        <button type="submit" class="btn btn-primary">Notificar</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
  @vite(['resources/js/guardia.js'])
@endpush
