@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar registro de __MODELO__</h2>

    <form action="{{ route('__NOMBRE_RUTA__.update', $registro->id) }}" method="POST">
        @csrf
        @method('PUT')

        @foreach ($campos as $campo => $config)
            @if (!empty($config['incluir']))
                <div class="mb-3">
                    <label for="{{ $campo }}" class="form-label">{{ $campo }}</label>

                    @if (isset($config['tipo']) && $config['tipo'] === 'boolean')
                        <select class="form-select" id="{{ $campo }}" name="{{ $campo }}">
                            <option value="S" {{ $registro->$campo === 'S' ? 'selected' : '' }}>Sí</option>
                            <option value="N" {{ $registro->$campo === 'N' ? 'selected' : '' }}>No</option>
                        </select>
                    @else
                        <input type="text" class="form-control" id="{{ $campo }}" name="{{ $campo }}" value="{{ $registro->$campo }}">
                    @endif
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn btn-primary">💾 Actualizar</button>
        <a href="{{ route('__NOMBRE_RUTA__.index') }}" class="btn btn-secondary">↩️ Cancelar</a>
    </form>
</div>
@endsection
