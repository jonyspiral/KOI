@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">➕ Nuevo registro en {{ $modelo }}</h2>

    {{-- 🧾 Formulario de creación --}}
    <form action="{{ route('produccion.abms.rutas_produccion.store') }}" method="POST">
        @csrf

        {{-- 🌐 Inputs dinámicos desde configuración --}}
        @foreach ($campos as $campo => $config)
            @php
                $type = $config['input_type'] ?? 'text';
                $default = $config['default'] ?? '';
                $isBoolean = !empty($config['is_boolean']);
                $isMaxPlusOne = !empty($config['max_mas_uno']) || !empty($config['auto_increment_plus']);
                $inputId = 'input_' . $campo;
                $value = $isMaxPlusOne && isset($siguiente[$campo]) ? $siguiente[$campo] : $default;
            @endphp

            <div class="mb-3">
                <label for="{{ $inputId }}" class="form-label">{{ $campo }}</label>

                {{-- ✅ Campo booleano como checkbox --}}
                @if ($isBoolean)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="{{ $campo }}" id="{{ $inputId }}" value="S"
                            {{ $default === 'S' ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $inputId }}">Sí</label>
                    </div>

                {{-- 🔒 Campo con Max+1 (autoincrementado y readonly) --}}
                @elseif ($isMaxPlusOne)
                    <input type="{{ $type }}" class="form-control" name="{{ $campo }}" id="{{ $inputId }}"
                        value="{{ $value }}" readonly>

                {{-- ✍️ Input normal --}}
                @else
                    <input type="{{ $type }}" class="form-control" name="{{ $campo }}" id="{{ $inputId }}"
                        value="{{ $value }}">
                @endif
            </div>
        @endforeach

        {{-- 💾 Botones --}}
        <button type="submit" class="btn btn-success">
            💾 Guardar
        </button>
        <a href="{{ route('produccion.abms.rutas_produccion.index') }}" class="btn btn-secondary">
            ⬅️ Cancelar
        </a>
    </form>
</div>
@endsection
