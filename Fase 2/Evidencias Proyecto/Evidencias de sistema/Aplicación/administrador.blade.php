@extends('layouts.app')

@section('title','Administrador')

@push('styles')
  @vite(['resources/css/admin.css', 'resources/js/admin.js'])
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
@endpush

@section('content')
    <main class="page">
    <div class="admin-wrap">
      <div class="admin-card">
        <h1>Bienvenido, <span class="username">{{ $userName ?? 'Nombre de usuario' }}</span></h1>

        <div class="menu-grid vertical">
            
          <a href="{{ url('/administrador/usuarios') }}" class="menu-button">
            <span class="menu-title">Gestión de usuarios</span>
            <span class="menu-desc">Crear, Editar, Eliminar</span>
          </a>
            
          <a href="{{ route('admin.vehiculos.index') }}" class="menu-button">
            <span class="menu-title">Gestión de vehículos</span>
            <span class="menu-desc">Crear, Editar, Eliminar</span>
          </a>
            
          <a href="{{ route('admin.ots.index') }}" class="menu-button">
            <span class="menu-title">Visualización de OT</span>
            <span class="menu-desc">Órdenes de trabajo en curso</span>
          </a>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
  @vite(['resources/js/admin.js'])
@endpush