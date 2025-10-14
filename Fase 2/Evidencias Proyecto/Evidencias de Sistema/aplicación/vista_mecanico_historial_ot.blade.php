@extends('layouts.app')

@section('title','Ver Historial OT')

@push('styles')
  @vite(['resources/css/vista_mecanico_historial_ot.css', 'resources/js/vista_mecanico_historial_ot.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
<div class="page">
    <header class="head">
      <h1>Historial de OT</h1>
      <a class="btn btn-ghost" href="{{ url('/mecanico') }}">← Volver</a>
    </header>

    <div class="tools">
      <div class="search">
        <input id="q" type="search" placeholder="Buscar por patente, vehículo…">
      </div>
    </div>

    <section class="list">
      {{-- Items de ejemplo. Al conectar backend, renderea tu colección --}}
      <article class="item" data-vehiculo="Toyota Hilux" data-patente="KJTR-21">
        <div class="cols">
          <div class="col"><span class="lbl">Fecha</span><span>12/08/2025 09:10</span></div>
          <div class="col"><span class="lbl">Duración</span><span>2 h 15 m</span></div>
          <div class="col"><span class="lbl">Vehículo</span><span>Toyota Hilux</span></div>
          <div class="col"><span class="lbl">Patente</span><span>KJTR-21</span></div>
        </div>
        <button class="btn btn-primary ver" data-id="OT-101">Ver</button>
      </article>

      <article class="item" data-vehiculo="Nissan NP300" data-patente="BZFG-83">
        <div class="cols">
          <div class="col"><span class="lbl">Fecha</span><span>05/07/2025 14:30</span></div>
          <div class="col"><span class="lbl">Duración</span><span>3 h 40 m</span></div>
          <div class="col"><span class="lbl">Vehículo</span><span>Nissan NP300</span></div>
          <div class="col"><span class="lbl">Patente</span><span>BZFG-83</span></div>
        </div>
        <button class="btn btn-primary ver" data-id="OT-099">Ver</button>
      </article>

      <article class="item" data-vehiculo="Chevrolet D-Max" data-patente="KRRX-55">
        <div class="cols">
          <div class="col"><span class="lbl">Fecha</span><span>22/06/2025 10:05</span></div>
          <div class="col"><span class="lbl">Duración</span><span>1 h 50 m</span></div>
          <div class="col"><span class="lbl">Vehículo</span><span>Chevrolet D-Max</span></div>
          <div class="col"><span class="lbl">Patente</span><span>KRRX-55</span></div>
        </div>
        <button class="btn btn-primary ver" data-id="OT-092">Ver</button>
      </article>

      <article class="item" data-vehiculo="Ford Ranger" data-patente="PHZX-10">
        <div class="cols">
          <div class="col"><span class="lbl">Fecha</span><span>11/05/2025 08:20</span></div>
          <div class="col"><span class="lbl">Duración</span><span>4 h 05 m</span></div>
          <div class="col"><span class="lbl">Vehículo</span><span>Ford Ranger</span></div>
          <div class="col"><span class="lbl">Patente</span><span>PHZX-10</span></div>
        </div>
        <button class="btn btn-primary ver" data-id="OT-081">Ver</button>
      </article>
    </section>
  </div>

  {{-- Modal simple (demo) --}}
  <dialog id="dlgOT">
    <div class="dlg-card">
      <h3 class="dlg-title">Detalle de OT</h3>
      <p class="dlg-body">Contenido de ejemplo para la OT <span id="dlgId"></span>. Aquí luego cargas información real.</p>
      <div class="dlg-actions">
        <button class="btn btn-ghost" id="dlgClose">Cerrar</button>
      </div>
    </div>
  </dialog>
@endsection

@push('scripts')
  @vite(['resources/js/vista_mecanico_historial_ot.js'])
@endpush