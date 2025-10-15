<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recepción | Solicitudes de ingreso</title>

  
</head>
<body>
@extends('layouts.app')

@section('title','Recepción | Solicitudes de ingreso')

@push('styles')
  @vite(['resources/css/recepcionista.css', 'resources/js/recepcionista.js'])  
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')

  <main class="page">
    <div class="layout layout-single">
      <div class="card">
        <div class="card-header">
          <h1>Solicitudes de ingreso</h1>

          {{-- Botón para simular una llegada de vehículo (solo demo de UI) --}}
          <button id="btn-simular" class="btn btn-primary">Simular solicitud</button>
        </div>

        {{-- Notificación superior (tipo aviso) --}}
        <div id="alert-solicitud" class="inline-alert hidden">
          <span class="bell">🔔</span>
          <span>Solicitud de vehículo entrante</span>
          <button id="alert-aceptar" class="btn btn-success btn-sm">Aceptar</button>
        </div>

        {{-- Estado vacío --}}
        <div id="empty-state" class="empty">
          <img src="{{ asset('images/empty-inbox.svg') }}" alt="" class="empty-ill">
          <p>No hay solicitudes pendientes.</p>
        </div>

        {{-- Tabla de solicitudes (oculta hasta que haya alguna) --}}
        <table id="tabla-solicitudes" class="tabla" style="display:none;">
          <thead>
            <tr>
              <th>Patente</th>
              <th>Chofer</th>
              <th>Hora de entrada</th>
              <th>Motivo de ingreso</th>
              <th class="col-acciones">Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-solicitudes">
            {{-- Filas se agregan por JS --}}
          </tbody>
        </table>
      </div>
    </div>
  </main>

  {{-- Toasts (notificaciones flotantes) --}}
  <div id="toast-area" class="toast-area"></div>

  {{-- Template de fila (para JS) --}}
  <template id="tpl-fila">
    <tr>
      <td class="td-patente">ABCD23</td>
      <td class="td-chofer">Miguel Pérez</td>
      <td class="td-hora">09:05</td>
      <td class="td-motivo">Entrega de insumos</td>
      <td class="acciones">
        <button class="btn btn-outline btn-aceptar">Aceptar ingreso</button>
        <button class="btn btn-outline btn-rechazar">Rechazar ingreso</button>
      </td>
    </tr>
  </template>
@endsection

@push('scripts')
  @vite(['resources/js/recepcionista.js'])
@endpush