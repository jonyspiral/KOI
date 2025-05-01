@extends('layouts.app')

{{-- 
  Archivo: index.blade.php
  Versión: 2.3.0
  Última actualización: 2025-04-30
  Descripción: Crea y visualiza automáticamente la configuración del ABM en un solo paso.
--}}

@section('content')
<div class="container">
  <h2 class="mb-4">🛠️ Crear Nuevo ABM</h2>

  {{-- ⚙️ Formulario para seleccionar modelo y cargar configuración --}}
  <form method="GET" action="{{ route('sistemas.abms.crear') }}">
    {{-- No hace falta @csrf si es GET --}}

    {{-- Selección de modelo --}}
    <div class="mb-4">
      <label class="form-label">📦 Seleccioná el Modelo</label>
      <select name="modelo" class="form-select" required onchange="this.form.submit()">
        <option value="">-- Elegí un modelo --</option>
        @foreach ($modelos as $modelo)
          <option value="{{ $modelo }}" {{ (old('modelo', $modeloSeleccionado) == $modelo) ? 'selected' : '' }}>
            {{ $modelo }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Namespace --}}
    <div class="mb-4">
      <label class="form-label">📂 Namespace del Controlador</label>
      <input type="text" name="namespace" class="form-control" 
             placeholder="Ej: Produccion, Abms" 
             value="{{ old('namespace', $namespaceSeleccionado) }}" required>
    </div>

    {{-- Carpeta de Vistas --}}
    <div class="mb-4">
      <label class="form-label">🗂️ Carpeta para las Vistas (en resources/views)</label>
      <input type="text" name="carpeta_vistas" class="form-control" 
             placeholder="Ej: produccion/abms" 
             value="{{ old('carpeta_vistas', $carpetaSeleccionada) }}" required>
    </div>

    <div class="d-flex gap-2 mt-3">
      <button type="submit" class="btn btn-primary">Actualizar Configuración</button>
      <a href="{{ route('sistemas.importar.form') }}" class="btn btn-outline-secondary">Importar Tabla</a>
    </div>
  </form>

  {{-- 📋 Previsualización --}}
  @if ($modeloSeleccionado && !empty($configJson))
    <div class="mt-5">
      <h4>📋 Configuración Detectada:</h4>
      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>Campo</th>
            <th>Label</th>
            <th>Incluir</th>
            <th>Readonly</th>
            <th>Orden</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($configJson['campos'] ?? [] as $campo => $meta)
            <tr>
              <td><code>{{ $campo }}</code></td>
              <td>{{ $meta['label_custom'] ?? ucfirst($campo) }}</td>
              <td>{{ !empty($meta['incluir']) ? '✔️' : '❌' }}</td>
              <td>{{ !empty($meta['readonly']) ? '✔️' : '❌' }}</td>
              <td>{{ $meta['orden'] ?? 0 }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="mt-4">
        <a href="{{ route('sistemas.abms.preview', ['modelo' => $modeloSeleccionado]) }}" class="btn btn-success">
          🚀 Continuar a Previsualizar ABM
        </a>
      </div>
    </div>
  @endif
</div>
@endsection
