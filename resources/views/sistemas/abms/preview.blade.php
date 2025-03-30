@extends('layouts.app')

@section('content')
<div class="container">
  <h3>🔧 Configurar ABM para: <strong>{{ $modelo }}</strong></h3>
 
  <form method="POST" action="{{ url('/sistemas/abms/generar') }}">
    @csrf
    <input type="hidden" name="tabla" value="{{ $modelo }}">
    <input type="hidden" name="namespace_controlador" value="{{ $namespace }}">
    <input type="hidden" name="carpeta_vistas" value="{{ $carpetaVistas }}">
    <input type="hidden" name="modelo" value="{{ $modelo }}">

    <div class="mb-4">
      <label class="form-label">📁 Carpeta del Controlador (namespace)</label>
      <div class="mb-3 form-check">
<input type="hidden" name="sobrescribir" value="0">
<input type="checkbox" class="form-check-input" id="sobrescribir" name="sobrescribir" value="1">
<label class="form-check-label" for="sobrescribir">Sobrescribir controlador si ya existe</label>
</div>
      <input type="text" class="form-control" value="{{ $namespace }}" readonly>
    </div>

    <div class="mb-4">
      <label class="form-label">📁 Carpeta para las Vistas (en resources/views)</label>
      <input type="text" class="form-control" value="{{ $carpetaVistas }}" readonly>
    </div>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Campo</th>
          <th>Tipo</th>
          <th>
  Incluir<br>
  <input type="checkbox" id="checkAllIncluir">
</th>

          <!-- <th>Incluir</th> -->
          <th>Input</th>
          <th>Tabla Ref</th>
          <th>Campo Mostrar</th>
        </tr>
      </thead>
      <tbody>
        @foreach($campos as $campo => $config)
        <tr>
          <td>{{ $campo }}</td>
          <td>{{ $config['tipo'] ?? '' }}</td>
          <td>
            <input type="checkbox" name="campos[{{ $campo }}][incluir]" value="1" {{ !empty($config['incluir']) ? 'checked' : '' }} {{ !empty($config['autoincremental']) ? 'disabled' : '' }}>
            @if(!empty($config['autoincremental']))
              <input type="hidden" name="campos[{{ $campo }}][incluir]" value="0">
              <input type="hidden" name="campos[{{ $campo }}][autoincremental]" value="1">
            @endif
          </td>
          <td>
            <select name="campos[{{ $campo }}][tipo_input]" class="form-control">
              <option value="text" {{ ($config['tipo_input'] ?? '') == 'text' ? 'selected' : '' }}>Input</option>
              <option value="textarea" {{ ($config['tipo_input'] ?? '') == 'textarea' ? 'selected' : '' }}>Textarea</option>
              <option value="select" {{ ($config['tipo_input'] ?? '') == 'select' ? 'selected' : '' }}>Combo box</option>
              <option value="checkbox" {{ ($config['tipo_input'] ?? '') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
              <option value="date" {{ ($config['tipo_input'] ?? '') == 'date' ? 'selected' : '' }}>Fecha</option>
            </select>
          </td>
          <td>
            <input type="text" name="campos[{{ $campo }}][tabla_ref]" class="form-control" value="{{ $config['tabla_ref'] ?? '' }}">
          </td>
          <td>
            <input type="text" name="campos[{{ $campo }}][campo_mostrar]" class="form-control" value="{{ $config['campo_mostrar'] ?? '' }}">
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <button type="submit" class="btn btn-success">Generar ABM</button>
  </form>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const masterCheckbox = document.getElementById('checkAllIncluir');
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="campos"][name$="[incluir]"]');

    masterCheckbox.addEventListener('change', function () {
      checkboxes.forEach(checkbox => {
        if (!checkbox.disabled) {
          checkbox.checked = masterCheckbox.checked;
        }
      });
    });
  });
</script>

@endsection
