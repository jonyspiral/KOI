@extends('adminlte::page')

@section('title', 'Análisis de Stock')

@section('content_header')
    <h1 class="mb-0">📦 Análisis de Stock</h1>
@endsection

@section('content')

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
                    <x-filtros.select-multiple campo="familia" :opciones="$familias" />
                    <x-filtros.select-multiple campo="linea" :opciones="$lineas" />
                    <x-filtros.select-multiple campo="almacen" :opciones="$almacenes" />
                    {{-- Filtro tipo producto stock --}}
                    <x-filtros.select-multiple campo="tipo_producto_stock" :opciones="$tiposProductoStock"/>

                    {{-- Filtro forma de comercialización --}}
                    <x-filtros.select-multiple  campo="forma_comercializacion" :opciones="$formasComercializacion" />
                    {{-- Filtro vigente en colores_por_articulo --}}
                 <th>@filterSelect('vigente', ['S' => 'Sí', 'N' => 'No'])</th>
                    <th class="text-center">

                        <button type="submit" class="btn btn-sm btn-primary">🔍</button>
                        <a href="{{ route('produccion.analisis-stock.index', ['reset' => 1]) }}" class="btn btn-sm btn-danger">❌</a>
                        @if($registros->count())
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('produccion.analisis-stock.exportar', request()->query()) }}" class="btn btn-success">
                                    📥
                                </a>
                            </div>
                        @endif

                    </th>
                </tr>
            </thead>
        </table>
    </div>
</form>
@if(request()->except('page'))
    <div class="mb-3">
       {{-- 📋 Active Filters --}}
       @if(request()->has('aplicar'))
           @php
               $activeFilters = \App\Helpers\FilterProvider::getActiveLabels(request()->except('page'));
           @endphp

           @if(count($activeFilters))
               <div class="mb-3">
                   <strong>🧮 Active filters:</strong>
                   @foreach($activeFilters as $label)
                       <span class="badge bg-primary me-1" style="font-weight: normal;">{{ $label }}</span>
                   @endforeach
               </div>
           @endif
       @endif
    </div>
@endif




{{-- 📋 Tabla de resultados --}}
<div class="table-responsive">
    <table class="table table-bordered table-sm table-hover">
       <thead class="table-light text-center">
    {{-- 🧮 Fila de Totales por Talle --}}
    <tr class="table-warning fw-bold">
        <td>@sortableth('cod_articulo', 'Artículo')</td>
        <td>@sortableth('cod_color_articulo', 'Color')</td>
        <td>@sortableth('denom_articulo', 'Denominación')</td>

        @foreach ($talles as $talle)
            <td class="text-end">{{ $totalesPorTalle[$talle] ?? 0 }}</td>
        @endforeach

        <td class="fw-bold text-end">{{ $totalGeneral }}</td>
    </tr>

    {{-- 🧾 Encabezados --}}
    <tr>
        <th>@sortableth('cod_articulo', 'Artículo')</th>
        <th>@sortableth('cod_color_articulo', 'Color')</th>
        <th>@sortableth('denom_articulo', 'Denominación')</th>

        @foreach ($talles as $talle)
            <th class="text-center">{{ $talle }}</th>
        @endforeach

        <th>Total</th>
    </tr>
</thead>



        <tbody>
            @forelse ($registros as $r)
                <tr>
                    <td class="text-monospace">{{ $r->cod_articulo }}</td>
                    <td class="text-monospace">{{ $r->cod_color_articulo }}</td>
                    <td>{{ $r->denom_articulo }}</td>
                    @foreach ($talles as $talle)
                        <td class="text-end">{{ $r->cantidades[$talle] ?? '—' }}</td>
                    @endforeach
                    <td class="fw-bold text-end">{{ $r->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + count($talles) }}" class="text-center text-muted">No hay registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 📄 Paginación --}}
<div class="mt-3">
    @if($registros instanceof \Illuminate\Pagination\Paginator || $registros instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $registros->links() }}
@endif

</div>
@endsection
