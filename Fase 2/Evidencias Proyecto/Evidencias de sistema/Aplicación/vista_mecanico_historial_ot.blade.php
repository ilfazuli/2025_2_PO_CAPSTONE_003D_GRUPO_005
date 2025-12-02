@extends('layouts.app')

@section('title', 'Historial de mis OT')

@push('styles')
    @vite(['resources/css/vista_mecanico.css'])
    @vite(['resources/css/navbar.css'])
@endpush

@section('content')
    <div class="page">
        <header class="head">
            <h1 class="title">Historial de mis OT</h1>
            <a href="{{ url('/mecanico') }}" class="btn btn-outline">Volver a pendientes</a>
        </header>

        <section class="filters" aria-label="filtros">
            <div class="f-row">
                <label for="filtroTextoH">Buscar:</label>
                <input id="filtroTextoH"
                       type="text"
                       placeholder="Patente / Nº OT / Motivo">
            </div>
        </section>

        <section id="listaHistorial" class="ot-list">
            @forelse ($ots as $ot)
                @php
                    $motivo = optional(optional($ot->agendamiento)->tipoServicio)->ts_nombre
                        ?? $ot->origen
                        ?? 'Trabajo asignado';

                    $tiempoMin = $ot->tiempo_trabajo_min ?? 0;
                    $seg = $tiempoMin * 60;
                    $h = floor($seg / 3600);
                    $m = floor(($seg % 3600) / 60);
                    $s = $seg % 60;
                    $tiempoFmt = sprintf('%02d:%02d:%02d', $h, $m, $s);

                    $estadoLabel = $ot->estado === 'CERRADA' ? 'Cerrada por Jefe de Taller' : 'Finalizada por mecánico';
                @endphp

                <article class="ot-card"
                         data-id="{{ $ot->id }}">
                    <header class="ot-head">
                        <div>
                            <span class="lbl">N° de OT</span>
                            <strong>{{ $ot->folio }}</strong>
                        </div>
                        <div>
                            <span class="lbl">Patente</span>
                            <strong>{{ optional($ot->vehiculo)->vehiculo_patente ?? 'SIN PATENTE' }}</strong>
                        </div>
                        <div>
                            <span class="lbl">Motivo</span>
                            <span>{{ $motivo }}</span>
                        </div>
                        <div>
                            <span class="lbl">Fecha apertura</span>
                            <span>{{ optional($ot->apertura_ts)->format('d-m-Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="lbl">Tiempo total trabajado</span>
                            <span>{{ $tiempoFmt }}</span>
                        </div>
                        <div class="estado">
                            <span class="lbl">Estado</span>
                            <span class="badge badge-done">{{ $estadoLabel }}</span>
                        </div>
                    </header>
                    <div class="ot-actions">
                    </div>
                </article>
            @empty
                <p class="muted">Aún no tienes órdenes finalizadas.</p>
            @endforelse
        </section>
    </div>


    @include('partials.mecanico_info_modal') {{-- o copia el mismo <dialog id="dlgInfo"> de la otra vista --}}
@endsection

@push('scripts')
    @vite(['resources/js/navbar.js'])
    @vite(['resources/js/vista_mecanico_historial_ot.js'])
@endpush
