@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $formConfig['form_name'] ?? $modelo }}</h2>

    {{-- 🔎 Buscador del registro padre --}}
    <form action="{{ route(str_replace('/', '.', $formConfig['form_route']) . '.index') }}" method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
    {{-- 🔽 Campo para seleccionar columna --}}
    <div>
        <label class="form-label">Buscar por</label>
        <select name="buscar_campo" class="form-select">
            @foreach ($columnas as $col)
                <option value="{{ $col }}" {{ request('buscar_campo') === $col ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $col)) }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 🔎 Input de búsqueda --}}
    <div>
        <label class="form-label">Valor</label>
        <input type="text" name="buscar" class="form-control" placeholder="Ingrese texto..." value="{{ request('buscar') }}">
    </div>

    {{-- ▶️ Botones --}}
    <div>
        <button class="btn btn-primary mt-4" type="submit">🔍 Buscar</button>
        <a href="{{ route(str_replace('/', '.', $formConfig['form_route']) . '.index') }}" class="btn btn-secondary mt-4">🔄 Limpiar</a>
    </div>
</form>



    {{-- 📄 Registro principal --}}
    @isset($registro)
        <div class="row">
            @foreach ($campos as $campo => $meta)
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">{{ $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo)) }}</label>

                    @php $valor = $registro->$campo; @endphp

                    <div class="form-control bg-light">
                        @if (($meta['input_type'] ?? null) === 'checkbox')
                            {!! $valor === 'S' ? '✅' : '—' !!}
                        @elseif (($meta['input_type'] ?? null) === 'select' && !empty($meta['referenced_table']) && isset($selectCache[$meta['referenced_table']][$valor]))
                            {{ $selectCache[$meta['referenced_table']][$valor] }}
                        @elseif (($meta['input_type'] ?? null) === 'select_list' && isset($meta['select_list_data']))
                            @php
                                $mapa = collect(explode(',', $meta['select_list_data']))
                                    ->mapWithKeys(fn($item) => [explode('=', $item)[1] => explode('=', $item)[0]]);
                            @endphp
                            {{ $mapa->get($valor, $valor) }}
                        @else
                            {{ $valor }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 🧩 Subformularios inline --}}
        @foreach ($subformularios as $subform)
            @php
                $bladeParcial = $subform['partial'] ?? "components.partials.subform-" . \Illuminate\Support\Str::snake($subform['modelo']);
            @endphp

            <div class="card mt-4">
                <div class="card-header">
                    <h5>{{ $subform['titulo'] ?? $subform['modelo'] }}</h5>
                </div>
                <div class="card-body">
                    @includeIf($bladeParcial, [
                        'registroPadre' => $registro,
                        'config' => $subform
                    ])
                </div>
            </div>
        @endforeach
        {{-- 🔁 Navegación tipo ficha con filtros persistentes --}}
<div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
    @if ($registros->onFirstPage())
        <span class="btn btn-secondary disabled">⏮ Anterior</span>
    @else
        <a href="{{ $registros->appends(request()->except('page'))->previousPageUrl() }}" class="btn btn-outline-primary">⏮ Anterior</a>
    @endif

    <span>Página {{ $registros->currentPage() }} de {{ $registros->lastPage() }}</span>

    @if ($registros->hasMorePages())
        <a href="{{ $registros->appends(request()->except('page'))->nextPageUrl() }}" class="btn btn-outline-primary">Siguiente ⏭</a>
    @else
        <span class="btn btn-secondary disabled">Siguiente ⏭</span>
    @endif
</div>

       


        </div>
    @else
        <div class="alert alert-warning mt-4">
            No se encontró el registro principal.
        </div>
    @endisset
</div>
@endsection
