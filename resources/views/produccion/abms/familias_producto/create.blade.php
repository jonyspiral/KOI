@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">➕ Nuevo registro en {{ $modelo }}</h2>

    <form action="{{ route('produccion.abms.familias_productos.store') }}" method="POST">
        @csrf

        @foreach ($campos as $campo => $config)
            @php
                $type = $config['input_type'] ?? 'text';
                $label = $labels[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
                $default = old($campo, $defaults[$campo] ?? '');
                $isBoolean = !empty($config['is_boolean']);
                $isMaxPlusOne = !empty($config['max_mas_uno']) || !empty($config['auto_increment_plus']);
                $isSelect = !empty($config['referenced_table']) && !empty($config['referenced_label']);
                $isTextarea = $type === 'textarea';
                $value = $isMaxPlusOne && isset($siguiente[$campo]) ? $siguiente[$campo] : $default;
                $inputId = 'input_' . $campo;
            @endphp

            <div class="mb-3">
                <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>

                @if ($isBoolean)
                    {{-- ✅ Campo booleano --}}
                    <input type="hidden" name="{{ $campo }}" value="N">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="{{ $campo }}" id="{{ $inputId }}" value="S"
                            {{ $value === 'S' ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $inputId }}">Sí</label>
                    </div>

                @elseif ($isSelect)
                    {{-- 🔽 Campo SELECT --}}
                    <select class="form-select" name="{{ $campo }}" id="{{ $inputId }}">
                        <option value="">Seleccione una opción</option>
                        @foreach (${$campo . '_opciones'} as $op)
                            <option value="{{ $op->id }}" {{ $value == $op->id ? 'selected' : '' }}>
                                {{ $op->{$config['referenced_label']} }}
                            </option>
                        @endforeach
                    </select>

                @elseif ($isMaxPlusOne)
                    {{-- 🔒 Campo autoincrementado --}}
                    <input type="text" class="form-control" name="{{ $campo }}" id="{{ $inputId }}"
                        value="{{ $value }}" readonly>

                @elseif ($isTextarea)
                    {{-- 📝 Textarea --}}
                    <textarea class="form-control" name="{{ $campo }}" id="{{ $inputId }}" rows="3">{{ $value }}</textarea>

                @else
                    {{-- ✍️ Input estándar --}}
                    <input type="{{ $type }}" class="form-control" name="{{ $campo }}" id="{{ $inputId }}"
                        value="{{ $value }}">
                @endif
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">💾 Guardar</button>
        <a href="{{ route('produccion.abms.familias_productos.index') }}" class="btn btn-secondary">⬅️ Cancelar</a>
    </form>
</div>
@endsection