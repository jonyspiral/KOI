@extends('layouts.app')

@section('content')
<div class="container">
  <h3>🧩 Modelo generado: <code>{{ $modelo }}</code></h3>

  <p>📂 Archivo creado en: <code>{{ $modeloPath }}</code></p>

  <h5>📄 Código generado</h5>
  <pre><code>{{ $modeloCode }}</code></pre>

  <a href="{{ url()->previous() }}" class="btn btn-secondary mt-3">Volver</a>
</div>
<form method="POST" action="{{ route('abms.finalizar') }}">
  @csrf
  <input type="hidden" name="campos" value="{{ json_encode($campos) }}">
  <button class="btn btn-success mt-4">✅ Finalizar ABM (Modelo + Controlador + Vistas)</button>
</form>

@endsection