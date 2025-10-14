@extends('layouts.app')

@section('title','Ver Documentación')
@push('styles')
  @vite(['resources/css/chofer_documentacion.css', 'resources/js/chofer_documentacion.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <div class="doc-page">

    <div class="doc-toolbar">
      <a href="{{ url('/chofer') }}" class="doc-btn-azul">← Volver</a>
    </div>

    <h2 class="doc-title">Documentación asociada al vehículo</h2>

    <div class="doc-card">
      <div class="doc-grid">
        <div class="doc-label">Seguro SOAP</div>
        <a href="#" class="doc-btn">Descargar</a>

        <div class="doc-label">Revisión técnica</div>
        <a href="#" class="doc-btn">Descargar</a>

        <div class="doc-label">Permiso de circulación</div>
        <a href="#" class="doc-btn">Descargar</a>

        <div class="doc-label">Gases</div>
        <a href="#" class="doc-btn">Descargar</a>
      </div>
    </div>
  </div>

  @endsection

@push('scripts')
  @vite(['resources/js/chofer_documentacion.js'])
@endpush