@extends('layouts.app')

@section('title','Reservar hora')

@push('styles')
  @vite(['resources/css/chofer_reserva.css', 'resources/js/chofer_reserva.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <div class="res-page">

    {{-- Botón volver --}}
    <div class="res-toolbar">
      <a href="{{ url('/chofer') }}" class="res-btn-azul">← Volver</a>
    </div>

    <h2 class="res-title">Reservar hora</h2>

    <div class="res-card">
      {{-- Encabezado calendario --}}
      <header class="cal-head">
        <button id="cal-prev" class="cal-nav" aria-label="Mes anterior">&lt;</button>
        <h3 id="cal-title" class="cal-title">OCTUBRE 2025</h3>
        <button id="cal-next" class="cal-nav" aria-label="Mes siguiente">&gt;</button>
      </header>

      {{-- Calendario (el <tbody> lo llena JS) --}}
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

      {{-- Controles: motivo (izq) y botón (centro) --}}
      <div class="res-controls mockup-layout">
        <div class="col-left">
          <label for="motivo" class="lbl">Motivo</label>
          <select id="motivo" class="sel">
            <option value="" selected disabled>Seleccione…</option>
            <option value="reparacion">Reparación</option>
            <option value="revision_tecnica">Revisión Técnica</option>
            <option value="mantencion_preventiva">Mantención preventiva</option>
          </select>
        </div>

        <div class="col-center">
          <button type="button" class="reservar-btn" disabled>Reservar</button>
        </div>
      </div>
    </div>

    {{-- Para tu lógica posterior --}}
    <input type="hidden" id="fechaSeleccionada" name="fecha">
  </div>
@endsection

@push('scripts')
  @vite(['resources/js/chofer_reserva.js'])
@endpush