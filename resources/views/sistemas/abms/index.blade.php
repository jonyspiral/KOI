@extends('layouts.app')

{{--
  Archivo: index.blade.php
  Versión: 1.1.0
  Última actualización: 2025-03-31
  Descripción: Formulario para seleccionar modelo y carpetas de controlador/vistas para generar un ABM.
--}}

@section('content')
<div class="container">
  <h2 class="mb-4">🛠️ Crear ABM desde Modelos</h2>

  <form method="POST" action="{{ route('sistemas.abms.preview.redirect') }}">
    @csrf

    {{-- Selección de modelo --}}
    <div class="mb-4">
      <label class="form-label">Seleccioná el modelo</label>
      <select name="modelo" class="form-select" required>
        <option value="">-- Elegí un modelo --</option>
        @foreach ($modelos as $modelo)
          <option value="{{ $modelo }}" {{ old('modelo') == $modelo ? 'selected' : '' }}>
            {{ $modelo }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Carpeta del Controlador --}}
    <div class="mb-4">
      <label class="form-label">
        <i class="bi bi-folder2-open"></i> Carpeta del Controlador (namespace)
      </label>
      <input type="text" name="namespace" class="form-control" 
             placeholder="Ej: Abms, Produccion, Produccion/Ordenes" 
             value="{{ old('namespace', 'Produccion') }}" required>
    </div>

    {{-- Carpeta de las Vistas --}}
    <div class="mb-4">
      <label class="form-label">
        <i class="bi bi-folder2-open"></i> Carpeta para las Vistas (en resources/views)
      </label>
      <input type="text" name="carpeta_vistas" class="form-control" 
             placeholder="Ej: abms/rutasproduccion, produccion/rutas" 
             value="{{ old('carpeta_vistas', 'produccion/abms') }}" required>
             <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Siguiente</button>

    {{-- Botón que lleva al Importador de Tablas --}}
    <a href="{{ route('sistemas.importar.form') }}" class="btn btn-outline-secondary">
        Import
    </a>
</div>
<div class="mt-4">
    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#infoAbm" aria-expanded="false">
        ¿Cómo funciona esta pantalla?
    </button>
    <div class="collapse" id="infoAbm">
        <div class="card card-body mt-2">
            <h5 class="mb-2">📘 Importar Tablas y Crear ABMs en KOI</h5>
            <ul class="mb-1">
                <li>Primero, importá una tabla desde SQL Server usando el botón <strong>Import</strong>.</li>
                <li>Luego seleccioná un modelo MySQL generado y completá el namespace y carpeta de vistas.</li>
                <li>Presioná <strong>Siguiente</strong> para previsualizar los campos y generar el ABM.</li>
            </ul>
            <small class="text-muted">Ruta de importador: <code>Route::get('/form')</code> en <code>sistemas.importar.form</code></small>
        </div>
    </div>
</div>

@endsection
