@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Crear nuevo registro de __MODELO__</h2>

    <form action="{{ route('__NOMBRE_RUTA__.store') }}" method="POST">
        @csrf

        @foreach ($campos as $campo => $config)
            @if (!empty($config['incluir']))
                <div class="mb-3">
                    <label for="{{ $campo }}" class="form-label">{{ $campo }}</label>

                    @if (isset($config['tipo']) && $config['tipo'] === 'boolean')
                        <select class="form-select" id="{{ $campo }}" name="{{ $campo }}">
                            <option value="S">Sí</option>
                            <option value="N">No</option>
                        </select>
                    @else
                        <input type="text" class="form-control" id="{{ $campo }}" name="{{ $campo }}">
                    @endif
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('__NOMBRE_RUTA__.index') }}" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>
@endsection
