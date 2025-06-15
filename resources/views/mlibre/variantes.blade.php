@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 d-flex justify-content-between align-items-center">
        🛠️ Edición de Variantes ML
        <small class="text-muted">{{ $variantes->total() }} variantes</small>
    </h2>

    <div class="mb-3 d-flex gap-2">
        <form method="POST" action="{{ route('mlibre.sync-scfs') }}">
            @csrf
            <button type="submit" class="btn btn-success">
                🔄 Sincronizar SKUs con Mercado Libre
            </button>
        </form>

        <form method="GET" action="{{ route('mlibre.variantes.exportar') }}">
            <button type="submit" class="btn btn-outline-primary">
                📤 Exportar a Excel
            </button>
        </form>
    </div>

    <form method="GET" class="mb-3">
        <div class="row g-2">
            @foreach (['ml_id', 'color', 'talle', 'modelo', 'titulo', 'seller_sku', 'variation_id', 'product_number', 'seller_custom_field'] as $field)
                <div class="col">
                    <input type="text" name="{{ $field }}" value="{{ request($field) }}" class="form-control" placeholder="{{ ucfirst(str_replace('_', ' ', $field)) }}">
                </div>
            @endforeach
            <div class="col-auto"><button class="btn btn-secondary">Filtrar</button></div>
        </div>
    </form>

    @php
        function sort_link($label, $field) {
            $currentSort = request('sort');
            $currentDir = request('dir', 'asc');
            $newDir = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
            $arrow = $currentSort === $field ? ($currentDir === 'asc' ? '🔼' : '🔽') : '';
            $query = array_merge(request()->all(), ['sort' => $field, 'dir' => $newDir]);
            return '<a href="'.url()->current().'?'.http_build_query($query).'">'.$label.' '.$arrow.'</a>';
        }
    @endphp

    <form method="POST" action="{{ route('mlibre.variantes.guardar') }}">
        @csrf

        <table class="table table-bordered table-sm table-hover align-middle">
            <thead>
                <tr>
                    <th>{!! sort_link('ML ID', 'ml_variantes.ml_id') !!}</th>
                    <th>{!! sort_link('Variation ID', 'ml_variantes.variation_id') !!}</th>
                    <th>{!! sort_link('Título', 'ml_publicaciones.ml_name') !!}</th>
                    <th>{!! sort_link('Modelo', 'ml_variantes.modelo') !!}</th>
                    <th>{!! sort_link('SSKU', 'ml_variantes.seller_sku') !!}</th>
                    <th>{!! sort_link('SCF Actual', 'ml_variantes.seller_custom_field') !!}</th>
                    <th>{!! sort_link('Product #', 'ml_variantes.product_number') !!}</th>
                    <th>{!! sort_link('Color', 'ml_variantes.color') !!}</th>
                    <th>{!! sort_link('Talle', 'ml_variantes.talle') !!}</th>
                    <th>{!! sort_link('Precio', 'ml_variantes.precio') !!}</th>
                    <th>{!! sort_link('Stock', 'ml_variantes.stock') !!}</th>
                    <th class="d-none">{!! sort_link('Stock Flex', 'ml_variantes.stock_flex') !!}</th>
                    <th class="d-none">{!! sort_link('Stock Full', 'ml_variantes.stock_full') !!}</th>
                    <th>{!! sort_link('Status', 'ml_publicaciones.status') !!}</th>
                    <th>{!! sort_link('Logística', 'ml_publicaciones.logistic_type') !!}</th>
                    <th>SCF</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($variantes as $v)
                <tr>
                    <td>{{ $v->ml_id }}</td>
                    <td>{{ $v->variation_id }}</td>
                    <td>{{ $v->publicacion->ml_name ?? '-' }}</td>
                    <td>{{ $v->modelo ?? '-' }}</td>
                    <td>{{ $v->seller_sku ?? '-' }}</td>
                    <td>{{ $v->seller_custom_field ?? '-' }}</td>
                    <td>{{ $v->product_number ?? '-' }}</td>
                    <td>{{ $v->color ?? '-' }}</td>
                    <td>{{ $v->talle ?? '-' }}</td>
                    <td>{{ $v->precio ?? '-' }}</td>
                    <td>{{ $v->stock ?? '-' }}</td>
                    <td>{{ $v->stock_flex ?? '-' }}</td>
                    <td>{{ $v->stock_full ?? '-' }}</td>
                    <td>{{ $v->publicacion->status ?? '-' }}</td>
                    <td>{{ $v->publicacion->logistic_type ?? '-' }}</td>
                    <td>
                        <input type="text" name="variantes[{{ $v->id }}][seller_custom_field]"
                               value="{{ $v->seller_custom_field }}"
                               class="form-control form-control-sm w-auto"size="10">
                        <input type="hidden" name="variantes[{{ $v->id }}][id]" value="{{ $v->id }}">
                    </td>
                    <td class="text-center">
                        @php
                            $pendiente = $v->seller_custom_field && $v->seller_custom_field !== $v->seller_custom_field_actual;
                        @endphp

                        @if ($v->sincronizado == 1 && !$pendiente)
                            ✅
                        @elseif ($pendiente)
                            🟡
                        @else
                            ⏳
                        @endif

                        <form method="GET" action="{{ route('mlibre.variantes.verificar_scf', $v->id) }}" class="d-inline m-0 p-0">
                            <button class="btn btn-sm btn-info" title="Verificar SCF">🔍</button>
                        </form>
                        <!-- Botón Guardar Individual -->
                                <form method="POST" action="{{ route('mlibre.variantes.guardar_individual', $v->id) }}" class="d-inline m-0 p-0 guardar-individual-form">
                                @csrf
                                <input type="hidden" name="seller_custom_field" value="{{ old('seller_custom_field', $v->seller_custom_field) }}">
                                <button type="submit" class="btn btn-sm btn-primary" title="Guardar Variante">💾</button>
                            </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end mb-3">
            <button type="submit" class="btn btn-primary">💾 Guardar y Publicar</button>
        </div>

        <div class="d-flex justify-content-center">
            {{ $variantes->appends(request()->query())->links() }}
        </div>
    </form>
</div>
@endsection
