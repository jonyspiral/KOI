@extends('layouts.app')

@section('content')
<h1 class="mb-3">📦 Análisis de Stock</h1>

{{-- 🔍 Filtros --}}
<form method="GET" class="mb-2">
    <input type="hidden" name="aplicar" value="1">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm mb-0">
            <thead>
                <tr>
                    <th>Cód. Artículo</th>
                    <th>Descripción</th>
                    <th>Color</th>
                    <th>Familia</th>
                    <th>Línea</th>
                    <th>Almacén</th>
                    <th>Tipo Producto</th>
                    <th>F.Com</th>
                    <th>Vig</th>
                    <th class="text-center">Acciones</th>
                </tr>
                <tr>
                    <th>@filterInput('cod_articulo')</th>
                    <th>@filterInputLike('denom_articulo')</th>
                    <th>@filterInput('color')</th>

                    {{-- Múltiples --}}
                    <x-filtros.select-multiple campo="familia" :opciones="$familias" />
                    <x-filtros.select-multiple campo="linea" :opciones="$lineas" />
                    <x-filtros.select-multiple campo="almacen" :opciones="$almacenes" />

                    {{-- Tipo producto stock --}}
                    <x-filtros.select-multiple campo="tipo_producto_stock" :opciones="$tiposProductoStock" />

                    {{-- Forma de comercialización --}}
                    <x-filtros.select-multiple campo="forma_comercializacion" :opciones="$formasComercializacion" />

                    {{-- Vigente --}}
                    <th>@filterSelect('vigente', ['S' => 'Sí', 'N' => 'No'])</th>

                    {{-- Acciones --}}
                    <th class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <button type="submit" class="btn btn-sm btn-primary" title="Aplicar filtros">🔍</button>
                            <a href="{{ route('produccion.analisis-stock.index', ['reset' => 1]) }}" class="btn btn-sm btn-danger" title="Limpiar filtros">❌</a>
                            @if(($registros instanceof \Illuminate\Pagination\LengthAwarePaginator ? $registros->total() : $registros->count()) > 0)
                                <a href="{{ route('produccion.analisis-stock.exportar', request()->query()) }}" class="btn btn-sm btn-success" title="Exportar a Excel">📥</a>
                            @endif
                        </div>
                    </th>
                </tr>
            </thead>
        </table>
    </div>
</form>

{{-- 🎛️ Filtros activos (legibles) --}}
@if(request()->has('aplicar'))
    @php($activeFilters = \App\Helpers\FilterProvider::getActiveLabels(request()->except('page')))
    @if(!empty($activeFilters))
        <div class="mb-3">
            <strong>🧮 Filtros activos:</strong>
            @foreach($activeFilters as $label)
                <span class="badge bg-primary me-1" style="font-weight: normal;">{{ $label }}</span>
            @endforeach
        </div>
    @endif
@endif

{{-- 📋 Tabla de resultados --}}
<div class="table-responsive">
    <table class="table table-bordered table-sm table-hover">
        <thead class="table-light text-center">

            {{-- 🧮 Fila de Totales por Talle (del filtrado completo) --}}
            <tr class="table-warning fw-bold">
                <td>@sortableth('cod_articulo', 'Artículo') @ordenIcon('cod_articulo')</td>
                <td>@sortableth('cod_color_articulo', 'Color') @ordenIcon('cod_color_articulo')</td>
                <td>@sortableth('denom_articulo', 'Denominación') @ordenIcon('denom_articulo')</td>

                @foreach ($talles as $talle)
                    <td class="text-end">{{ number_format($totalesPorTalle[$talle] ?? 0, 0, ',', '.') }}</td>
                @endforeach

                <td class="fw-bold text-end">{{ number_format($totalGeneral ?? 0, 0, ',', '.') }}</td>
            </tr>

            {{-- 🧾 Encabezados de columnas --}}
            <tr>
                <th>@sortableth('cod_articulo', 'Artículo') @ordenIcon('cod_articulo')</th>
                <th>@sortableth('cod_color_articulo', 'Color') @ordenIcon('cod_color_articulo')</th>
                <th>@sortableth('denom_articulo', 'Denominación') @ordenIcon('denom_articulo')</th>

                @foreach ($talles as $talle)
                    <th class="text-center">{{ $talle }}</th>
                @endforeach

                <th>@sortableth('total', 'Total') @ordenIcon('total')</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($registros as $r)
                <tr>
                    <td class="text-monospace">{{ $r->cod_articulo }}</td>
                    <td class="text-monospace">{{ $r->cod_color_articulo }}</td>
                    <td>{{ $r->denom_articulo }}</td>

                    @foreach ($talles as $talle)
                        <td class="text-end">
                            {{ number_format($r->cantidades[$talle] ?? 0, 0, ',', '.') }}
                        </td>
                    @endforeach

                    <td class="fw-bold text-end">{{ number_format($r->total ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + count($talles) }}" class="text-center text-muted">No hay registros.</td>
                </tr>
            @endforelse
        </tbody>

        {{-- 🧮 Repetir totales al pie (opcional) --}}
        @if(($registros instanceof \Illuminate\Pagination\LengthAwarePaginator ? $registros->total() : $registros->count()) > 0)
            <tfoot>
                <tr class="table-warning fw-bold">
                    <td colspan="3" class="text-end">Totales del filtrado</td>
                    @foreach ($talles as $talle)
                        <td class="text-end">{{ number_format($totalesPorTalle[$talle] ?? 0, 0, ',', '.') }}</td>
                    @endforeach
                    <td class="text-end">{{ number_format($totalGeneral ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

{{-- 📄 Paginación --}}
<div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="small text-muted">
        @if($registros instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $registros->total() }} resultados • página {{ $registros->currentPage() }} / {{ $registros->lastPage() }}
        @endif
    </div>
    <div>
        @if($registros instanceof \Illuminate\Pagination\Paginator || $registros instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $registros->links() }}
        @endif
    </div>
</div>
@endsection

