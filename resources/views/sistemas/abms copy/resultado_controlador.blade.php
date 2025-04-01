@extends('layouts.app')

@section('content')
<div class="container">
  <h3>📦 Controlador generado con éxito</h3>

  <p><strong>Controlador:</strong> {{ $controllerNombre }} → <code>{{ $controllerPath }}</code></p>

  <pre><code>{{ $controllerCode }}</code></pre>

  <form method="POST" action="{{ url('/sistemas/abms/generar-vistas') }}">
    @csrf
    <input type="hidden" name="modelo" value="{{ $modelo }}">
    <input type="hidden" name="campos_json" value="{{ json_encode($campos) }}">
    <button class="btn btn-success">Generar Vistas</button>
  </form>
</div>
@endsection
