@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🎉 ABM generado con éxito</h2>

    <div class="alert alert-success">
        El ABM para el modelo <strong>{{ $modelo }}</strong> fue generado correctamente.
    </div>

    <div class="mb-3">
        <i class="bi bi-folder"></i>
        <strong>Vistas generadas en:</strong>
        <code>resources/views/{{ $carpeta_vistas }}</code>
    </div>

    <div class="mb-3">
        <i class="bi bi-file-earmark-code"></i>
        <strong>Controlador generado en:</strong>
        <code>{{ $controller_path }}</code>
    </div>

    @if ($request_path)
        <div class="mb-3">
            <i class="bi bi-shield-check"></i>
            <strong>Request generado en:</strong>
            <code>{{ $request_path }}</code>
        </div>
    @endif

    <p>💡 Asegurate de agregar la ruta correspondiente en <code>routes/web.php</code>, por ejemplo:</p>

    <pre><code>Route::resource('{{ Str::kebab($modelo) }}', {{ $modelo }}Controller::class);</code></pre>

    <div class="mt-4 d-flex gap-2">
        {{-- 🔄 Crear otro ABM --}}
        <a href="{{ route('sistemas.abms.crear') }}" class="btn btn-primary">
            ⏎ Crear otro ABM
        </a>

        {{-- ✅ Ir al ABM recién creado --}}
        <a href="{{ url(Str::of($carpeta_vistas)->start('/')) }}" class="btn btn-success">
            📋 Ir al ABM generado
        </a>
    </div>
</div>
@endsection
