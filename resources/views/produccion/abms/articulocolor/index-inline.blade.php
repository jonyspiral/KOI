{{-- 📄 index-inline.blade.php (AdminLTE funcional con filtros, sort y subform) --}}
@extends('adminlte::page')

@section('title', 'Artículos con Colores')

@section('content_header')
    <h1>🧱 Artículos</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevoArticulo">➕ Nuevo Artículo</button>
    </div>
    <div class="card-body table-responsive p-0" x-data="{}">
        <form method="GET" action="{{ route('articulocolor.index') }}">
        <table class="table table-hover table-bordered table-sm text-nowrap align-middle">
 
        <thead class="table-dark align-middle">
                       
                <tr>
                    <th>@sortableth('cod_articulo', 'Código')</th>
                    <th>@sortableth('denom_articulo', 'Nombre')</th>
                    <th>@sortableth('unidad', 'Unidad')</th>
                    <th>@sortableth('linea', 'Línea')</th>
                    <th>@sortableth('vigente', 'Vigente')</th>
                    <th>@sortableth('descripcion', 'Descripción')</th>
                    <th>@sortableth('familia', 'Familia')</th>
                    <th>@sortableth('ruta', 'Ruta')</th>
                    <th>@sortableth('rango', 'Rango')</th>
                    <th>@sortableth('horma', 'Horma')</th>
                    <th>@sortableth('marca', 'Marca')</th>
                    <th>@sortableth('forma_comercializacion', 'Forma Com.')</th>
                    <th>🎯</th>
                </tr>
                <tr>
                    <th>@filterInput('cod_articulo')</th>
                    <th>@filterInputLike('denom_articulo')</th>

                    <th>@filterInput('unidad')</th>
                   <x-filtros.select-multiple campo="linea" :opciones="$lineas->pluck('denom_linea', 'denom_linea')" />
                    <th>@filterSelect('vigente', ['S' => 'Sí', 'N' => 'No'])</th>

                    <th>@filterInputLike('descripcion')</th>
                    <x-filtros.select-multiple campo="familia" :opciones="$familias->pluck('nombre', 'nombre')" />
                    <x-filtros.select-multiple campo="ruta" :opciones="$rutas->pluck('denom_ruta', 'denom_ruta')" />
                    <th>@filterInput('rango')</th>
                    <th>@filterInput('horma')</th>
                 
                    <x-filtros.select-multiple campo="marca" :opciones="$marcas->pluck('denom_marca', 'denom_marca')" />

                    <x-filtros.select-multiple campo="forma_comercializacion" :opciones="$formasComercializacion" />

                    <th>
                        <button type="submit" class="btn btn-sm btn-primary">🔍</button>
                        <a href="{{ route('articulocolor.index', ['reset' => 1]) }}" class="btn btn-sm btn-danger">❌</a>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articulos as $articulo)
                    <tr>
                        <td>{{ $articulo->cod_articulo }}</td>
                        <td>{{ $articulo->denom_articulo }}</td>
                        <td>{{ $articulo->unidad }}</td>
                        <td>{{ $articulo->linea }}</td>
                        <td>{{ $articulo->vigente === 'S' ? '✅' : '❌' }}</td>
                        <td><textarea class="form-control form-control-sm" rows="2" readonly>{{ $articulo->descripcion }}</textarea></td>
                        <td>{{ $articulo->familia }}</td>
                        <td>{{ $articulo->ruta }}</td>
                        <td>{{ $articulo->rango }}</td>
                        <td>{{ $articulo->horma }}</td>
                        <td>{{ $articulo->marca }}</td>
                        <td>{{ $articulo->forma_comercializacion }}</td>
                        <td>
                            <a href="{{ route('articulocolor.edit', $articulo->id) }}" class="btn btn-sm btn-warning">✏️</a>
                            <form action="{{ route('articulocolor.destroy', $articulo->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar artículo?')">🗑️</button>
                            </form>
                          
                            <button type="button" @click="$refs['colores{{ $articulo->id }}'].classList.toggle('d-none')" class="btn btn-sm btn-secondary">🎨</button>
                        </td>
                    </tr>
                    <tr x-ref="colores{{ $articulo->id }}" class="d-none">
                        <td colspan="13" class="bg-light p-3">
                            @include('produccion.abms.articulocolor.partials.subform-colores', ['articulo' => $articulo])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </form>

        <div class="mt-3 px-3">
            {{ $articulos->links() }}
        </div>
    </div>
</div>

@include('produccion.abms.articulocolor.partials.create-modal')
@push('js')
    <script src="//unpkg.com/alpinejs" defer></script>
@endpush
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2-filtro').select2({
            placeholder: 'Todas',
            allowClear: true,
            width: 'resolve'
        });
    });
</script>
@endpush
@stop

