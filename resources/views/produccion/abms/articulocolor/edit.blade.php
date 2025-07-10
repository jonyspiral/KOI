@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Artículo: {{ $articulo->cod_articulo }}</h1>

    <form action="{{ route('articulocolor.update', $articulo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <ul class="nav nav-tabs mb-3" id="tabArticulo" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general" type="button">General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#produccion" type="button">Producción</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#marketing" type="button">Marketing</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#comercial" type="button">Comercial</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#avanzado" type="button">Avanzado</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="general">
                @include('produccion.abms.articulocolor.tabs.general')
            </div>
            <div class="tab-pane fade" id="produccion">
                @include('produccion.abms.articulocolor.tabs.produccion')
            </div>
            <div class="tab-pane fade" id="marketing">
                @include('produccion.abms.articulocolor.tabs.marketing')
            </div>
            <div class="tab-pane fade" id="comercial">
                @include('produccion.abms.articulocolor.tabs.comercial')
            </div>
            <div class="tab-pane fade" id="avanzado">
                @include('produccion.abms.articulocolor.tabs.avanzado')
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
            <a href="{{ route('articulocolor.index') }}" class="btn btn-secondary">Volver</a>
        </div>
    </form>
</div>
@endsection
