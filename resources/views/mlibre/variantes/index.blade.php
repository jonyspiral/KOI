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
    @php
        $syncLabels = [
            'S' => '✅ Sincronizado',
            'U' => '🟡 Actualizado',
            'N' => '🕓 Nuevo',
            'E' => '❌ Error',
        ];
    @endphp

    <div class="col-md-3">
        <label class="form-label fw-light text-muted small mb-1">Sync Status</label>
        <select name="sync_status[]" multiple class="form-select select2" onchange="this.form.submit()">
            @foreach($syncLabels as $valor => $texto)
                <option value="{{ $valor }}" {{ collect(request('sync_status', []))->contains($valor) ? 'selected' : '' }}>{{ $texto }}</option>
            @endforeach
        </select>
    </div>

    @foreach(['ml_id', 'variation_id', 'seller_custom_field', 'color', 'talle', 'modelo', 'titulo', 'status', 'family_id', 'logistic_type'] as $campo)
        <div class="col-md-3">
            <label class="form-label fw-light text-muted small mb-1">{{ ucfirst(str_replace('_', ' ', $campo)) }}</label>
            <select name="{{ $campo }}[]" multiple class="form-select select2" onchange="this.form.submit()">
                <option value="__NULL__" {{ collect(request($campo, []))->contains('__NULL__') ? 'selected' : '' }}>(Sin valor)</option>
                @foreach($filtros[$campo] ?? [] as $valor)
                    <option value="{{ $valor }}" {{ collect(request($campo, []))->contains($valor) ? 'selected' : '' }}>{{ $valor }}</option>
                @endforeach
            </select>
        </div>
    @endforeach
<div class="form-check">
  <input class="form-check-input" type="checkbox" name="has_campaign" id="has_campaign"
         value="1" {{ request('has_campaign') ? 'checked' : '' }}>
  <label class="form-check-label" for="has_campaign">
    Solo en campaña
  </label>
</div>

    {{-- Campos ocultos para sort y dir --}}
    <input type="hidden" name="sort" value="{{ request('sort') }}">
    <input type="hidden" name="dir" value="{{ request('dir') }}">

    <div class="col-md-2">
        <a href="{{ route('mlibre.variantes.index', ['reset' => 1]) }}" class="btn btn-danger w-100">❌ Borrar filtros guardados</a>
    </div>
</form>

{{-- EXPORTAR --}}
<form method="GET" action="{{ route('mlibre.variantes.exportar') }}" class="mb-3">
    @foreach(request()->except('page') as $k => $v)
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

{{-- FORMULARIO PRINCIPAL --}}
<form method="POST" action="{{ route('mlibre.sync.seleccionados') }}" id="sync-form">
    @csrf

    @if(session('sync_result'))
        <div class="alert alert-info">
            <strong>🔁 Resultados de sincronización:</strong><br>
            Total seleccionados: {{ session('sync_result.total') }}<br>
            ✅ Sincronizados: {{ session('sync_result.ok') }}<br>
            ❌ Errores: {{ session('sync_result.errors') }}
        </div>
    @endif

    <table class="table table-striped table-bordered table-sm align-middle">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th><a href="{{ route('mlibre.variantes.index', array_merge(request()->except('page'), ['sort' => 'ml_id', 'dir' => request('dir') === 'asc' ? 'desc' : 'asc'])) }}">ML ID</a></th>
                <th>Var ID</th>
                <th>SCF</th>
                <th>Color</th>
                <th>Talle</th>
                <th>Modelo</th>
                <th>Título</th>
                <th>Precio</th>
                <th>Override</th>
                <th>Precio SKU (ML)</th>
                <th>Stock</th>
                <th>Override</th>
                <th>Stock SKU</th>
                <th>Status</th>
                <th>Logística</th>
                <th>Sync Stock</th>
                <th>Sync Precio</th>
            </tr>
        </thead>
        <tbody>
            @forelse($variantes as $v)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $v->id }}"></td>
                    <td><a href="https://articulo.mercadolibre.com.ar/MLA-{{ Str::after($v->ml_id, 'MLA') }}" target="_blank">{{ $v->ml_id }}</a></td>
                    <td>{{ $v->variation_id }}</td>
                    <td><input type="text" name="scf[{{ $v->id }}]" value="{{ $v->seller_custom_field }}" class="form-control form-control-sm" maxlength="15"></td>
                    <td>{{ $v->color }}</td>
                    <td>{{ $v->talle }}</td>
                    <td>{{ $v->modelo }}</td>
                    <td>{{ $v->titulo }}</td>
                    <td><input type="number" step="0.01" name="precio[{{ $v->id }}]" value="{{ $v->precio }}" class="form-control form-control-sm" style="width: 80px;"></td>
                    <td class="text-center"><input type="checkbox" name="manual_price[{{ $v->id }}]" value="1" {{ $v->manual_price ? 'checked' : '' }}></td>
                    <td>{{ $v->skuVariante->ml_price ?? '-' }}</td>
                    <td><input type="number" name="stock[{{ $v->id }}]" value="{{ $v->stock }}" class="form-control form-control-sm" style="width: 60px;"></td>
                    <td class="text-center"><input type="checkbox" name="manual_stock[{{ $v->id }}]" value="1" {{ $v->manual_stock ? 'checked' : '' }}></td>
                    <td>{{ $v->skuVariante->stock ?? '-' }}</td>
                    <td>{{ $v->publicacion->status ?? '-' }}</td>
                    <td>{{ $v->publicacion->logistic_type ?? '-' }}</td>
                    <td title="{{ $v->sync_log_stock }}">
                        @if(str_starts_with($v->sync_log_stock, '✅')) ✅
                        @elseif(str_starts_with($v->sync_log_stock, '❌')) ❌
                        @elseif(str_starts_with($v->sync_log_stock, '🟡')) 🟡
                        @else 🕓
                        @endif
                    </td>
                    <td title="{{ $v->sync_log_precio }}">
                        @if(str_starts_with($v->sync_log_precio, '✅')) ✅
                        @elseif(str_starts_with($v->sync_log_precio, '❌')) ❌
                        @elseif(str_starts_with($v->sync_log_precio, '🟡')) 🟡
                        @else 🕓
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="text-center">No hay resultados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $variantes->appends(request()->except('page'))->links() }}

    <button type="submit" class="btn btn-warning">🔄 Sincronizar seleccionados</button>
</form>

{{-- SINCRONIZAR FILTRADOS --}}
<form method="POST" action="{{ route('mlibre.sync.filtrados') }}" class="d-inline mt-2">
    @csrf
    @foreach(request()->except('page') as $key => $value)
        @if(is_array($value))
            @foreach($value as $val)
                <input type="hidden" name="{{ $key }}[]" value="{{ $val }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <button type="submit" class="btn btn-outline-success">💰 Sincronizar precios filtrados</button>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $('#select-all').on('change', function () {
        $('input[name="ids[]"]').prop('checked', this.checked);
    });

    $(document).ready(function () {
        $('.select2').select2({
            allowClear: true,
            width: '100%',
            placeholder: 'Seleccionar'
        });
    });
</script>
@endpush
