@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🛠️ Edición de Variantes ML</h2>

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col"><input type="text" name="ml_id" value="{{ request('ml_id') }}" class="form-control" placeholder="Filtrar ML ID"></div>
            <div class="col"><input type="text" name="color" value="{{ request('color') }}" class="form-control" placeholder="Color"></div>
            <div class="col"><input type="text" name="talle" value="{{ request('talle') }}" class="form-control" placeholder="Talle"></div>
            <div class="col"><input type="text" name="modelo" value="{{ request('modelo') }}" class="form-control" placeholder="Modelo"></div>
            <div class="col"><input type="text" name="titulo" value="{{ request('titulo') }}" class="form-control" placeholder="Título"></div>
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
                    <th>Variation ID</th>
                    <th>{!! sort_link('Título', 'ml_publicaciones.ml_name') !!}</th>
                    <th>{!! sort_link('Modelo', 'ml_variantes.modelo') !!}</th>
                    <th>{!! sort_link('SSKU', 'ml_variantes.seller_sku') !!}</th>
                    <th>{!! sort_link('Color', 'ml_variantes.color') !!}</th>
                    <th>{!! sort_link('Talle', 'ml_variantes.talle') !!}</th>
                    <th>{!! sort_link('Precio', 'ml_variantes.precio') !!}</th>
                    <th>{!! sort_link('Stock', 'ml_variantes.stock') !!}</th>
                    <th>Nuevo SCF</th>
                    <th>Publicar</th>
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

                    <td>{{ $v->color }}</td>
                    <td>{{ $v->talle }}</td>
                    <td>{{ $v->precio }}</td>
                    <td>{{ $v->stock }}</td>
                    <td>
                        <input type="text" name="variantes[{{ $v->id }}][nuevo_seller_custom_field]"
                               value="{{ $v->nuevo_seller_custom_field }}"
                               class="form-control form-control-sm">
                        <input type="hidden" name="variantes[{{ $v->id }}][id]" value="{{ $v->id }}">
                    </td>
                    <td class="text-center">
                        @if ($v->sincronizado == 1)
                            ✅
                        @else
                            ⏳
                        @endif

                        <form method="POST" action="{{ route('mlibre.variantes.publicar_scf', $v->id) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm" title="Publicar SCF">
                                🔁
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">💾 Guardar cambios</button>
        </div>
    </form>
</div>
@endsection
