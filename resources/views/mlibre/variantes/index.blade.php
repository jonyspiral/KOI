@extends('layouts.app')

@section('title', 'Variantes ML')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 d-flex justify-content-between align-items-center">
        📦 Mercado Libre --- PANEL DE VARIANTES---
        <small class="text-muted">{{ $variantes->total() }} resultados</small>
    </h2>

    {{-- FILTROS --}}
    
    <form method="GET" action="{{ route('mlibre.variantes.index') }}" class="row g-2 mb-3">
       @foreach(['ml_id', 'variation_id', 'product_number', 'seller_custom_field', 'color', 'talle', 'modelo', 'titulo', 'seller_sku', 'sync_status', 'status', 'family_id'] as $campo)
    <div class="col-md-3">
        <label class="form-label fw-light text-muted small mb-1">
            {{ ucfirst(str_replace('_', ' ', $campo)) }}
        </label>
        <select name="{{ $campo }}[]" multiple class="form-select select2" onchange="this.form.submit()">
    <option value="__NULL__" {{ collect(request($campo, []))->contains('__NULL__') ? 'selected' : '' }}>
        (Sin valor)
    </option>
    @foreach($filtros[$campo] ?? [] as $valor)
        <option value="{{ $valor }}" {{ collect(request($campo, []))->contains($valor) ? 'selected' : '' }}>
            {{ $valor }}
        </option>
    @endforeach
</select>

    </div>
@endforeach
    
        <div class="col-md-2">
        <a href="{{ route('mlibre.variantes.index', ['reset' => 1]) }}" class="btn btn-danger w-100">❌ Borrar filtros guardados</a>
    </div>
    </form>

    {{-- BOTÓN EXPORTAR --}}
    <form method="GET" action="{{ route('mlibre.variantes.exportar') }}" class="mb-3">
        @foreach(request()->all() as $k => $v)
            @if(is_array($v))
                @foreach($v as $vv)
                    <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endif
        @endforeach
        <button type="submit" class="btn btn-outline-primary">📤 Exportar Excel (filtro actual)</button>
    </form>

    <form method="POST" action="{{ route('mlibre.variantes.sync-seleccionados') }}">
        @csrf

        @if(session('sync_result'))
        <div class="alert alert-info">
            <strong>🔁 Resultados de sincronización:</strong><br>
            Total seleccionados: {{ session('sync_result.total') }}<br>
            ✅ Sincronizados: {{ session('sync_result.ok') }}<br>
            ❌ Errores: {{ session('sync_result.errors') }}
        </div>
        @endif

        {{-- TABLA --}}
        <table class="table table-striped table-bordered table-sm align-middle">
    <thead>
    <tr>
        <th><input type="checkbox" id="select-all"></th>

        @php
            $sortableCols = [
                'ml_id' => 'ML ID',
                'variation_id' => 'Var ID',
                'seller_custom_field' => 'SCF',
                'color' => 'Color',
                'talle' => 'Talle',
                'modelo' => 'Modelo',
                'titulo' => 'Título',
                'precio' => 'Precio',
                'manual_price' => 'Override Precio',
                'stock' => 'Stock KOI',
                'manual_stock' => 'Override Stock',
                'stock_fisico' => 'Stock Físico',
                'status' => 'Status',
                'family_id' => 'Family',
                'sync_status' => 'Sync'
            ];
        @endphp

        @foreach($sortableCols as $col => $label)
            <th>
                <a href="{{ route('mlibre.variantes.index', array_merge(request()->all(), ['sort' => $col, 'dir' => $sort == $col && $dir == 'asc' ? 'desc' : 'asc'])) }}">
                    {{ $label }} {!! $sort == $col ? ($dir == 'asc' ? ' 🔼' : ' 🔽') : '' !!}
                </a>
            </th>
        @endforeach
    </tr>
</thead>

<tbody>
    @forelse($variantes as $v)
    <tr>
        <td><input type="checkbox" name="ids[]" value="{{ $v->id }}"></td>
        <td><a href="https://articulo.mercadolibre.com.ar/MLA-{{ Str::after($v->ml_id, 'MLA') }}" target="_blank">{{ $v->ml_id }}</a></td>
        <td>{{ $v->variation_id }}</td>
        <td>
            <input type="text" name="scf[{{ $v->id }}]" value="{{ $v->seller_custom_field }}" class="form-control form-control-sm" maxlength="15">
        </td>
        <td>{{ $v->color }}</td>
        <td>{{ $v->talle }}</td>
        <td>{{ $v->modelo }}</td>
        <td>{{ $v->titulo }}</td>
        <td>
            <input type="number" step="0.01" name="precio[{{ $v->id }}]" value="{{ $v->precio }}" class="form-control form-control-sm" style="width: 80px;">
        </td>
        <td class="text-center">
            <input type="checkbox" name="manual_price[{{ $v->id }}]" value="1" {{ $v->manual_price ? 'checked' : '' }}>
        </td>
        <td>
            <input type="number" name="stock[{{ $v->id }}]" value="{{ $v->stock }}" class="form-control form-control-sm" style="width: 60px;">
        </td>
        <td class="text-center">
            <input type="checkbox" name="manual_stock[{{ $v->id }}]" value="1" {{ $v->manual_stock ? 'checked' : '' }}>
        </td>
        <td @if(isset($v->skuVariante) && $v->skuVariante->stock != $v->stock) class="table-warning" @endif>
            {{ $v->skuVariante->stock ?? '-' }}
        </td>
        <td>{{ $v->publicacion->status ?? '-' }}</td>
        <td>{{ $v->publicacion->family_id ?? '-' }}</td>
        <td title="{{ $v->sync_log }}">
            @if ($v->sync_status == 'S') ✅
            @elseif ($v->sync_status == 'N') 🕓
            @elseif ($v->sync_status == 'U') 🟡
            @else ❌
            @endif
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="17" class="text-center">No hay resultados</td>
    </tr>
    @endforelse
</tbody>

        </table>

        {{-- BOTÓN DE ACCIÓN --}}
        <div class="mb-4">
            <button type="submit" class="btn btn-warning">🔄 Sincronizar seleccionados</button>
        </div>
    </form>

    {{-- PAGINACIÓN --}}
    {{ $variantes->appends(request()->all())->links() }}
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('#select-all').on('change', function() {
        $('input[name="ids[]"]').prop('checked', this.checked);
    });

    $(document).ready(function() {
        $('.select2').select2({
            allowClear: true,
            width: '100%',
            placeholder: 'Seleccionar'
        });
    });
</script>
@endpush
