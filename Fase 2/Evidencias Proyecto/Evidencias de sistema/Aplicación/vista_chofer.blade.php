@extends('layouts.app')

@section('title','Chofer')

@push('styles')
  @vite(['resources/css/chofer.css', 'resources/js/chofer.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
  <div class="home-page">
    <h2>Bienvenido, Chofer ¿Qué desea hacer hoy?</h2>
    <div class="home-box">
      <div class="home-opcion">
        <a href="{{ url('/chofer/reservar-hora') }}">Reservar hora</a>
      </div>
      <div class="home-opcion">
        <a href="{{ url('/chofer/ver-documentacion') }}">Ver documentación</a>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  @vite(['resources/js/chofer.js'])
@endpush