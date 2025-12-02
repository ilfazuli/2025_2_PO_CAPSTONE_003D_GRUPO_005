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

  <h2 class="doc-title">Documentación del vehículo</h2>

  @if(!$vehiculo)
      <p class="doc-empty">No tienes un vehículo asignado actualmente.</p>
  @else
      <p class="doc-subtitle">Patente: {{ $vehiculo->vehiculo_patente }}</p>

      <div class="doc-card">

        {{-- ===================== SOAP ===================== --}}
        @php $docSoap = $docsPorTipo['soap'] ?? null; @endphp
        <div class="doc-row">
          <div class="doc-col-left">Seguro SOAP</div>
          <div class="doc-col-middle">
            @if($docSoap && $docSoap->fecha_vencimiento)
              Vence: {{ $docSoap->fecha_vencimiento->format('d-m-Y') }}
            @elseif($docSoap)
              Sin información de vencimiento
            @else
              —
            @endif
          </div>
          <div class="doc-col-right">
            @if($docSoap)
              @php
                $url = asset('storage/'.$docSoap->archivo_path);
                $ext = pathinfo($docSoap->archivo_path, PATHINFO_EXTENSION);
              @endphp
              <a href="{{ $url }}" target="_blank" class="doc-btn">Ver</a>
              <a href="{{ $url }}"
                 download="SOAP_{{ $vehiculo->vehiculo_patente }}.{{ $ext }}"
                 class="doc-btn-secondary">
                Descargar
              </a>
            @else
              <div class="doc-btn-disabled">No disponible</div>
            @endif
          </div>
        </div>

        <hr class="doc-separator">

        {{-- ===================== Revisión técnica ===================== --}}
        @php $docRev = $docsPorTipo['revision_tecnica'] ?? null; @endphp
        <div class="doc-row">
          <div class="doc-col-left">Revisión técnica</div>
          <div class="doc-col-middle">
            @if($docRev && $docRev->fecha_vencimiento)
              Vence: {{ $docRev->fecha_vencimiento->format('d-m-Y') }}
            @elseif($docRev)
              Sin información de vencimiento
            @else
              —
            @endif
          </div>
          <div class="doc-col-right">
            @if($docRev)
              @php
                $url = asset('storage/'.$docRev->archivo_path);
                $ext = pathinfo($docRev->archivo_path, PATHINFO_EXTENSION);
              @endphp
              <a href="{{ $url }}" target="_blank" class="doc-btn">Ver</a>
              <a href="{{ $url }}"
                 download="Revision_{{ $vehiculo->vehiculo_patente }}.{{ $ext }}"
                 class="doc-btn-secondary">
                Descargar
              </a>
            @else
              <div class="doc-btn-disabled">No disponible</div>
            @endif
          </div>
        </div>

        <hr class="doc-separator">

        {{-- ===================== Permiso de circulación ===================== --}}
        @php $docPerm = $docsPorTipo['permiso_circulacion'] ?? null; @endphp
        <div class="doc-row">
          <div class="doc-col-left">Permiso de circulación</div>
          <div class="doc-col-middle">
            @if($docPerm && $docPerm->fecha_vencimiento)
              Vence: {{ $docPerm->fecha_vencimiento->format('d-m-Y') }}
            @elseif($docPerm)
              Sin información de vencimiento
            @else
              —
            @endif
          </div>
          <div class="doc-col-right">
            @if($docPerm)
              @php
                $url = asset('storage/'.$docPerm->archivo_path);
                $ext = pathinfo($docPerm->archivo_path, PATHINFO_EXTENSION);
              @endphp
              <a href="{{ $url }}" target="_blank" class="doc-btn">Ver</a>
              <a href="{{ $url }}"
                 download="PermisoCirculacion_{{ $vehiculo->vehiculo_patente }}.{{ $ext }}"
                 class="doc-btn-secondary">
                Descargar
              </a>
            @else
              <div class="doc-btn-disabled">No disponible</div>
            @endif
          </div>
        </div>

        <hr class="doc-separator">

        {{-- ===================== Padrón ===================== --}}
        @php $docPadron = $docsPorTipo['padron'] ?? null; @endphp
        <div class="doc-row">
          <div class="doc-col-left">Padrón</div>
          <div class="doc-col-middle">
            @if($docPadron && $docPadron->fecha_vencimiento)
              Vence: {{ $docPadron->fecha_vencimiento->format('d-m-Y') }}
            @elseif($docPadron)
              Sin información de vencimiento
            @else
              —
            @endif
          </div>
          <div class="doc-col-right">
            @if($docPadron)
              @php
                $url = asset('storage/'.$docPadron->archivo_path);
                $ext = pathinfo($docPadron->archivo_path, PATHINFO_EXTENSION);
              @endphp
              <a href="{{ $url }}" target="_blank" class="doc-btn">Ver</a>
              <a href="{{ $url }}"
                 download="Padron_{{ $vehiculo->vehiculo_patente }}.{{ $ext }}"
                 class="doc-btn-secondary">
                Descargar
              </a>
            @else
              <div class="doc-btn-disabled">No disponible</div>
            @endif
          </div>
        </div>

        <hr class="doc-separator">

        {{-- ===================== Gases ===================== --}}
        @php $docGases = $docsPorTipo['gases'] ?? null; @endphp
        <div class="doc-row">
          <div class="doc-col-left">Gases</div>
          <div class="doc-col-middle">
            @if($docGases && $docGases->fecha_vencimiento)
              Vence: {{ $docGases->fecha_vencimiento->format('d-m-Y') }}
            @elseif($docGases)
              Sin información de vencimiento
            @else
              —
            @endif
          </div>
          <div class="doc-col-right">
            @if($docGases)
              @php
                $url = asset('storage/'.$docGases->archivo_path);
                $ext = pathinfo($docGases->archivo_path, PATHINFO_EXTENSION);
              @endphp
              <a href="{{ $url }}" target="_blank" class="doc-btn">Ver</a>
              <a href="{{ $url }}"
                 download="Gases_{{ $vehiculo->vehiculo_patente }}.{{ $ext }}"
                 class="doc-btn-secondary">
                Descargar
              </a>
            @else
              <div class="doc-btn-disabled">No disponible</div>
            @endif
          </div>
        </div>

        <hr class="doc-separator">

        {{-- ===================== Otros documentos ===================== --}}
        @php $docOtros = $docsPorTipo['otros'] ?? null; @endphp
        <div class="doc-row">
          <div class="doc-col-left">Otros documentos</div>
          <div class="doc-col-middle">
            @if($docOtros && $docOtros->fecha_vencimiento)
              Vence: {{ $docOtros->fecha_vencimiento->format('d-m-Y') }}
            @elseif($docOtros)
              Sin información de vencimiento
            @else
              —
            @endif
          </div>
          <div class="doc-col-right">
            @if($docOtros)
              @php
                $url = asset('storage/'.$docOtros->archivo_path);
                $ext = pathinfo($docOtros->archivo_path, PATHINFO_EXTENSION);
              @endphp
              <a href="{{ $url }}" target="_blank" class="doc-btn">Ver</a>
              <a href="{{ $url }}"
                 download="Otro_{{ $vehiculo->vehiculo_patente }}.{{ $ext }}"
                 class="doc-btn-secondary">
                Descargar
              </a>
            @else
              <div class="doc-btn-disabled">No disponible</div>
            @endif
          </div>
        </div>

      </div>

  @endif

</div>
@endsection

@push('scripts')
  @vite(['resources/js/chofer_documentacion.js'])
@endpush
