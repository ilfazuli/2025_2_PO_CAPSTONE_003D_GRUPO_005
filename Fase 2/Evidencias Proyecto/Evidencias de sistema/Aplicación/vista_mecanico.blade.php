@extends('layouts.app')

@section('title', 'Vista del Mecánico')
@push('styles')
    @vite(['resources/css/vista_mecanico.css'])
    @vite(['resources/css/navbar.css'])
@endpush

@section('content')
    {{-- Toast de asignación --}}
    <div id="asignacionToast"
         class="toast"
         role="alert"
         aria-live="assertive"
         aria-atomic="true"
         hidden>
        <div class="toast__icon"></div>

        <div class="toast__content">
            <strong>Nuevo vehículo asignado</strong>
            <p>Se te ha asignado una nueva OT.</p>
        </div>

        <button type="button"
                class="toast__btn"
                data-toast-dismiss>
            Aceptar
        </button>
    </div>

    <div class="page">
        {{-- Header --}}
        <header class="head">
            <h1 class="title">Lista de vehículos pendientes</h1>

            <a href="{{ url('/mecanico/historial_ot') }}"
               class="btn btn-outline"
               id="btnMisOT">
                Ver mis OT
            </a>
        </header>

        {{-- Filtros --}}
        <section class="filters" aria-label="filtros">
            <div class="f-row">
                <label for="filtroTexto">Buscar:</label>
                <input id="filtroTexto"
                       type="text"
                       placeholder="Patente / Nº OT / Motivo">
            </div>

            <div class="f-row">
                <label for="filtroEstado">Estado:</label>
                <select id="filtroEstado">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en-curso">En curso</option>
                    <option value="pausado">Pausado</option>
                    <option value="finalizado">Finalizado</option>
                </select>
            </div>
        </section>

        {{-- Lista de OT --}}
        <section id="listaOT" class="ot-list">
           @forelse ($ots as $ot)
              @php
                // Estado "crudo" desde BD
                $estadoRaw = $ot->estado_mecanico ?? 'PENDIENTE';

                // Normalización de estado para data-attributes
                $estadoData = match ($estadoRaw) {
                    'PENDIENTE'      => 'pendiente',
                    'EN_TRABAJO'     => 'en-curso',
                    'PAUSADO'        => 'pausado',
                    'FINALIZADO_MEC' => 'finalizado',
                    default          => 'pendiente',
                };

                // Clase visual del badge
                $badgeClass = match ($estadoData) {
                    'pendiente'  => 'badge-pend',
                    'en-curso'   => 'badge-run',
                    'pausado'    => 'badge-pause',
                    'finalizado' => 'badge-done',
                    default      => 'badge-pend',
                };

                // Texto a mostrar en el badge
                $badgeLabel = match ($estadoData) {
                    'pendiente'  => 'Pendiente',
                    'en-curso'   => 'En curso',
                    'pausado'    => 'Pausado',
                    'finalizado' => 'Finalizado',
                    default      => 'Pendiente',
                };

                // Motivo de ingreso: tipo de servicio si existe, si no origen, si no fallback
                $motivo = optional(optional($ot->agendamiento)->tipoServicio)->ts_nombre
                    ?? $ot->origen
                    ?? 'Trabajo asignado';

                //  Tiempo base EN MINUTOS que viene desde la BD
                $tiempoBaseMin = $ot->tiempo_trabajo_min ?? 0;

                // Lo pasamos a SEGUNDOS para el contador en vivo
                $tiempoBaseSeg = $tiempoBaseMin * 60;
                $h = floor($tiempoBaseSeg / 3600);
                $m = floor(($tiempoBaseSeg % 3600) / 60);
                $s = $tiempoBaseSeg % 60;
                $tiempoFmt = sprintf('%02d:%02d:%02d', $h, $m, $s);

                // Lógica de habilitación de botones
                $puedeIniciar   = in_array($estadoData, ['pendiente', 'pausado']);
                $esReanudar     = $estadoData === 'pausado';
                $puedePausar    = $estadoData === 'en-curso';
                $puedeFinalizar = $estadoData === 'en-curso';
            @endphp


              <article class="ot-card"
                      data-estado="{{ $estadoData }}"
                      data-id="{{ $ot->id }}"
                      data-vehiculo-id="{{ optional($ot->vehiculo)->vehiculo_id }}">
                  {{-- Header de la OT --}}
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
                          <span class="lbl">Motivo de ingreso</span>
                          <span>{{ $motivo }}</span>
                      </div>

                      <div>
                          <span class="lbl">Fecha/hora asignación</span>
                          <span>{{ optional($ot->apertura_ts)->format('d-m-Y H:i') }}</span>
                      </div>

                      <div>
                          <span class="lbl">Tiempo trabajado</span>
                          <span class="ot-tiempo"
                                data-tiempo-base="{{ $tiempoBaseSeg }}"
                                data-ultimo-inicio="{{ $ot->ultimo_inicio_tramo ?? '' }}">
                                {{ $tiempoFmt }} {{-- formato HH:MM:SS --}}
                        </span>
                      </div>

                      <div class="estado">
                          <span class="lbl">Estado actual</span>
                          <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                      </div>
                  </header>

                  {{-- Acciones --}}
                  <div class="ot-actions">
                      <button class="btn btn-info"
                              data-action="info"
                              data-vehiculo-id="{{ optional($ot->vehiculo)->vehiculo_id }}">
                          Info vehículo
                      </button>

                      <button class="btn btn-outline"
                              data-action="comentario">
                          Agregar comentarios
                      </button>

                      <button class="btn btn-outline"
                              data-action="foto">
                          Agregar foto
                      </button>

                      {{-- Iniciar / Reanudar --}}
                      <button class="btn btn-primary"
                              data-action="iniciar"
                              @if (! $puedeIniciar) disabled @endif>
                          {{ $esReanudar ? 'Reanudar' : 'Iniciar trabajo' }}
                      </button>

                      {{-- Pausar --}}
                      <button class="btn btn-outline"
                              data-action="pausar"
                              @if (! $puedePausar) disabled @endif>
                          Pausar
                      </button>

                      {{-- Finalizar --}}
                      <button class="btn btn-danger"
                              data-action="finalizar"
                              @if (! $puedeFinalizar) disabled @endif>
                          Finalizar
                      </button>
                  </div>
              </article>
          @empty
              <p class="muted">No tienes OTs asignadas por ahora.</p>
          @endforelse

        </section>
    </div>
    <input type="file"
        id="inputFotoOt"
        accept="image/*"
        style="display:none">




    <dialog id="dlgInfo" class="dlg">
        <form method="dialog" class="dlg-card">
            <header>
                <h3>Detalle del vehículo</h3>
            </header>
            <div class="dlg-body">
                <p><strong>Folio OT:</strong> <span id="infoFolio"></span></p>
                <p><strong>Patente:</strong> <span id="infoPatente"></span></p>
                <p><strong>Marca:</strong> <span id="infoMarca"></span></p>
                <p><strong>Modelo:</strong> <span id="infoModelo"></span></p>
                <p><strong>Año:</strong> <span id="infoAnio"></span></p>

                <h4>Motivo de ingreso</h4>
                <p id="infoMotivo"></p>

                <h4>Comentarios del mecánico</h4>
                <ul id="infoComentarios" class="info-comentarios"></ul>

                <h4>Fotos</h4>
                <div id="infoFotos" class="info-fotos">
                </div>
            </div>
            <footer class="dlg-actions">
                <button class="btn" value="close">Cerrar</button>
            </footer>
        </form>
    </dialog>
    {{-- Modal: Agregar comentario --}}
    <dialog id="dlgComentario" class="dlg">
        <form method="dialog" class="dlg-card" id="formComentario">
            <header class="dlg-header">
                <h3>Agregar comentario</h3>
            </header>
            <div class="dlg-body">
                <p class="dlg-hint">
                    Este comentario quedará asociado a la OT y se verá en “Info vehículo”.
                </p>
                <textarea id="comentarioTexto"
                          rows="4"
                          maxlength="2000"
                          placeholder="Describe el avance, hallazgos o recomendaciones…"></textarea>
            </div>
            <footer class="dlg-actions">
                <button class="btn" value="cancel">Cancelar</button>
                <button class="btn btn-primary" id="btnComentarioGuardar" value="ok">
                    Guardar comentario
                </button>
            </footer>
        </form>
    </dialog> 
@include('partials.mecanico_info_modal')
@endsection

@push('scripts')
    @vite(['resources/js/navbar.js'])
    @vite(['resources/js/vista_mecanico.js'])
@endpush
