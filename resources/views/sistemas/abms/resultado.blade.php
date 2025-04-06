@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">✅ ABM generado exitosamente</h2>

    <div class="alert alert-success">
        <strong>{{ $modelo }}</strong> fue configurado correctamente.
    </div>

    <ul class="list-group mb-4">
        <li class="list-group-item">
            <strong>📁 Vistas creadas en:</strong> <code>resources/views/{{ $carpeta_vistas }}</code>
        </li>
        <li class="list-group-item">
            <strong>📂 Controlador generado en:</strong> <code>{{ $controller_path }}</code>
        </li>
        @if (!empty($request_path))
        <li class="list-group-item">
            <strong>🛡️ FormRequest generado en:</strong> <code>{{ $request_path }}</code>
        </li>
        @endif
        <li class="list-group-item">
            <strong>🧠 Configuración guardada en:</strong> <code>resources/meta_abms/config_form_{{ $modelo }}.json</code>
        </li>
    </ul>

    <div class="d-flex gap-2">
        <a href="{{ route('sistemas.abms.preview', ['modelo' => $modelo]) }}" class="btn btn-primary">
            🔍 Ver configuración de campos
        </a>
        <a href="{{ route('sistemas.abms.crear') }}" class="btn btn-secondary">
            ↩️ Volver al inicio
        </a>
        {{-- ✅ Ir al ABM recién creado --}}
        <a href="{{ url(Str::of($carpeta_vistas)->start('/')) }}" class="btn btn-success">
            📋 Ir al ABM generado
        </a>
    </div>
</div>
@endsection
