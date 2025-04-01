@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($registro) ? '✏️ Editar' : '➕ Crear' }} {{ $titulo }}</h2>

    <form method="POST" action="{{ isset($registro) ? route('{{ ruta }}.update', $registro->id) : route('{{ ruta }}.store') }}">
        @csrf
        @if(isset($registro))
            @method('PUT')
        @endif

        @foreach ($campos as $campo => $config)
            @if (!empty($config['visible']))
                <div class="mb-3">
                    <label class="form-label">{{ ucfirst(str_replace('_', ' ', $campo)) }}</label>

                    @switch($config['input_type'])
                        @case('text')
                        <input type="text" name="{{ $campo }}" class="form-control"
                               value="{{ old($campo, $registro->$campo ?? $config['default'] ?? '') }}">
                        @break

                        @case('number')
                        <input type="number" name="{{ $campo }}" class="form-control"
                               value="{{ old($campo, $registro->$campo ?? $config['default'] ?? '') }}">
                        @break

                        @case('checkbox')
                        <div class="form-check">
                            <input type="checkbox" name="{{ $campo }}" class="form-check-input" value="1"
                                   {{ old($campo, $registro->$campo ?? $config['default'] ?? false) ? 'checked' : '' }}>
                        </div>
                        @break

                        @case('date')
                        <input type="date" name="{{ $campo }}" class="form-control"
                               value="{{ old($campo, $registro->$campo ?? $config['default'] ?? '') }}">
                        @break

                        @case('select')
                        <select name="{{ $campo }}" class="form-select">
                            {{-- Las opciones se deben cargar manualmente si es clave foránea --}}
                        </select>
                        @break

                        @default
                        <input type="text" name="{{ $campo }}" class="form-control"
                               value="{{ old($campo, $registro->$campo ?? $config['default'] ?? '') }}">
                    @endswitch
                </div>
            @endif
        @endforeach

        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('{{ ruta }}.index') }}" class="btn btn-secondary">↩️ Volver</a>
    </form>
</div>
@endsection