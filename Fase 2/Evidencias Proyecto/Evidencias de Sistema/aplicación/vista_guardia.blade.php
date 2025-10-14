@extends('layouts.app')

@section('title','Vista del guardia')

@push('styles')
  @vite(['resources/css/guardia.css', 'resources/js/guardia.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <div class="page">
    <div class="layout">
      <div class="card">
        <h1>Vehículos en el taller</h1>
        <table class="tabla-vehiculos">
          <thead>
            <tr>
              <th>Patente</th>
              <th>Hora de entrada</th>
              <th>Hora de salida</th>
              <th>Estado</th>
              <th>Nombre</th>
              <th>Sucursal</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>ABCD23</td>
              <td>09:00</td>
              <td>--</td>
              <td>En el taller</td>
              <td>Miguel</td>
              <td>Santa Marta</td>
              <td class="acciones">
                <button class="btn btn-edit">Editar</button>
                <button class="btn btn-done">Terminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <aside class="side-panel">
        <div class="mini-card">
          <h3>Acciones</h3>
          <p>Agregar nuevo vehículo al taller.</p>
          <button id="btn-open-modal" class="btn btn-primary" type="button">
            Agregar vehículo
          </button>
        </div>
      </aside>
    </div>
  </div>

  <div id="modal-agregar" class="modal hidden">
    <div class="modal-dialog modal-sm">
      <button class="modal-close" id="btn-close-modal" aria-label="Cerrar">×</button>
      <h2>Agregar vehículo</h2>

      <form class="form-grid" onsubmit="return false;">
        <label>Patente
          <input type="text" placeholder="ABCD23">
        </label>

        <label>Nombre 
          <input type="text" placeholder="Nombre Apellido">
        </label>

        <label>Sucursal
          <input type="text" placeholder="Santa Marta">
        </label>

        <label>Fotografías
          <input type="file" accept="image/*">
        </label>

        <label>Hora de entrada
          <input type="time" value="09:00">
        </label>

        <div class="form-actions">
          <button class="btn" id="btn-cancelar" type="button">Cancelar</button>
          <button class="btn btn-primary" type="button">Notificar</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  @vite(['resources/js/guardia.js'])
@endpush