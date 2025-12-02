@extends('layouts.app')

@section('title','Solicitar Repuesto')

@push('styles')
  @vite(['resources/css/vista_mecanico_solicitar.css', 'resources/js/vista_mecanico_solicitar.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <div class="page">
    <header class="head">
      <h1>Solicitar repuesto</h1>
      <a class="btn btn-ghost" href="{{ url('/mecanico') }}">← Volver</a>
    </header>

    {{-- Mensajes --}}
    @if (session('status'))
      <div class="alert ok">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert err">
        <ul>
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card">
      <form id="frmRepuesto" method="POST" action="{{ route('mecanico.repuesto.store') }}">
        @csrf

        <div class="group">
          <label for="repuesto">Tipo de repuesto</label>
          <input id="repuesto" name="repuesto" type="text" placeholder="Ej: Filtro de aceite" required>
          <small class="msg" data-for="repuesto"></small>
        </div>

        <div class="group small">
          <label for="cantidad">Cantidad</label>
          <input id="cantidad" name="cantidad" type="number" min="1" step="1" inputmode="numeric" required>
          <small class="msg" data-for="cantidad"></small>
        </div>

        <div class="group">
          <label for="mecanico_id">Mecánico solicitante</label>
          <select id="mecanico_id" name="mecanico_id" required>
            <option value="">Seleccionar…</option>
            <option value="1">Juan</option>
            <option value="2">Miguel</option>
            <option value="3">Benja</option>
          </select>
          <small class="msg" data-for="mecanico_id"></small>
        </div>

        <div class="actions">
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  @vite(['resources/js/vista_mecanico_solicitar.js'])
@endpush