@extends('layouts.app')

@section('content')
<div class="container">
  <h2 class="mb-4">🛠️ Crear ABM desde Modelos</h2>

  <form method="POST" action="{{ url('/sistemas/abms/configurar') }}">
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
    </div>

    <button type="submit" class="btn btn-primary">Siguiente</button>
  </form>
</div>
@endsection
