@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Editar registro de {{ $modelo }}</h2>

    <form action="{{ route('__NOMBRE_RUTA__.update', $registro->id) }}" method="POST">
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
                    $isSelect = !empty($meta['referenced_table']) && !empty($meta['referenced_label']);
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
                        @elseif ($isSelect)
                            {{-- 🎯 SELECT FK --}}
                            <div class="mb-3">
                                <label for="{{ $inputId }}" class="form-label">{{ $campo }}</label>
                                <select class="form-select select2" name="{{ $campo }}" id="{{ $inputId }}">
                                    <option value="">Seleccione una opción</option>
                                    @foreach (${$campo . '_opciones'} as $op)
                                        <option value="{{ $op->id }}" {{ old($campo, $registro->$campo ?? '') == $op->id ? 'selected' : '' }}>
                                            {{ $op->{$meta['referenced_label']} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    @else
                        <input type="text" class="form-control" name="{{ $campo }}" id="{{ $campo }}" value="{{ old($campo, $registro->$campo) }}">
                    @endif
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Actualizar</button>
        <a href="{{ route('__NOMBRE_RUTA__.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Cancelar</a>
    </form>
</div>
@endsection 
