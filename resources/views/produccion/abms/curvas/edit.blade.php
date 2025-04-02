@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar registro de {{ $modelo }}</h2>

    <form action="{{ route('produccion.abms.curva.update', $registro->id) }}" method="POST">
        @csrf
        @method('PUT')
     
        @foreach ($campos as $campo => $meta)
                        @php
                    $type = $meta['input_type'] ?? 'text';
                    $default = $meta['default'] ?? '';
                    $isBoolean = !empty($meta['is_boolean']);
                    $isMaxPlusOne = !empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus']);
                    $inputId = 'input_' . $campo;
                    $value = old($campo, $registro->$campo ?? $default);
                @endphp

            @if (!empty($meta['incluir']))
                <div class="mb-3">
                    <label for="{{ $campo }}" class="form-label">{{ $campo }}</label>
                 
                    @if ($isBoolean)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="{{ $campo }}" id="{{ $campo }}" value="S"
                                {{ $registro->$campo === 'S' ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $campo }}">
                                Marcar si corresponde
                            </label>
                        </div>

                    @else
                        <input type="text" class="form-control" name="{{ $campo }}" id="{{ $campo }}" value="{{ old($campo, $registro->$campo) }}">
                    @endif
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
        <a href="{{ route('produccion.abms.curva.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
    </form>
</div>
@endsection 
