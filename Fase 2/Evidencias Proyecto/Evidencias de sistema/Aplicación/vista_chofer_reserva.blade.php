@extends('layouts.app')

@section('title','Reservar hora')

@push('styles')
  @vite(['resources/css/chofer_reserva.css','resources/css/navbar.css'])
@endpush

@section('content')
  <div class="res-page">

    <div class="res-toolbar">
      <a href="{{ url('/chofer') }}" class="res-btn-azul">← Volver</a>
    </div>

    <h2 class="res-title">Reservar hora</h2>

    <form method="POST"
          action="{{ route('chofer.reserva.store') }}"
          id="formReserva"
          class="res-card mockup"
          data-slots-endpoint="{{ route('chofer.reserva.slots') }}">
      @csrf

      {{-- Calendario --}}
      <header class="cal-head">
        <button id="cal-prev" class="cal-nav" type="button" aria-label="Mes anterior">‹</button>
        <h3 id="cal-title" class="cal-title">MES AÑO</h3>
        <button id="cal-next" class="cal-nav" type="button" aria-label="Mes siguiente">›</button>
      </header>

      <div class="cal-box">
        <table class="cal-table" aria-label="Calendario mensual">
          <thead>
            <tr>
              <th>D</th><th>L</th><th>M</th><th>M</th><th>J</th><th>V</th><th>S</th>
            </tr>
          </thead>
          <tbody id="cal-body"></tbody>
        </table>
      </div>

      {{-- Controles debajo (vehículo / motivo / hora / reservar) --}}
      <div class="mock-controls">
        <div class="mc-field">
          <label for="vehiculo_id" class="lbl">Vehículo</label>
          <select id="vehiculo_id" name="vehiculo_id" class="sel" required>
            <option value="" selected disabled>Seleccione…</option>
            @foreach($vehiculos as $v)
              <option value="{{ $v->vehiculo_id }}">
                {{ $v->vehiculo_patente }} — {{ $v->vehiculo_marca }} {{ $v->vehiculo_modelo }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="mc-field">
          <label for="motivo" class="lbl">Motivo</label>
          <select id="motivo" class="sel" required>
            <option value="" selected disabled>Seleccione…</option>
            @foreach($motivos as $m)
              <option value="{{ $m->ts_id }}" data-duracion="{{ (int)($m->duracion_minutos ?? 60) }}">
                {{ $m->ts_nombre }}
              </option>
            @endforeach
          </select>
          <input type="hidden" id="motivoCode" name="tipo_servicio_id">
        </div>

        {{-- Selector de hora (se carga por JS) --}}
        <div class="mc-field">
          <label class="lbl">Hora disponible</label>
          <div id="slot-wrap" class="slot-wrap">
            <div class="slot-empty">Selecciona fecha y motivo para ver horas disponibles</div>
          </div>
        </div>

        <div class="mc-actions">
          <button type="submit" class="reservar-btn" id="btnReservar" disabled>Reservar</button>
        </div>
      </div>

      {{-- Hidden fields que completa el JS --}}
      <input type="hidden" id="fechaSeleccionada" name="fecha">
      <input type="hidden" id="horaInicio"        name="hora_inicio">
      <input type="hidden" id="horaFin"           name="hora_fin">
    </form>

    @if (session('ok'))
      {{-- TOAST centrado --}}
      <div id="toastOk" class="res-toast res-toast--success res-toast--center" role="status" aria-live="polite">
        <div class="res-toast__icon">✔</div>
        <div class="res-toast__body">
          <div class="res-toast__title">Reserva creada</div>
          <div class="res-toast__msg">{{ session('ok') }}</div>
        </div>
        <button class="res-toast__close" type="button" aria-label="Cerrar">×</button>
      </div>
    @endif

  </div>
@endsection

@push('scripts')
  @vite(['resources/js/chofer_reserva.js','resources/js/navbar.js'])
@endpush
