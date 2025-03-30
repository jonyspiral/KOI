@extends('layouts.app')

@section('content')
<div class="container">
  <h2>{{ isset($registro) ? 'Editar' : 'Nuevo' }} </h2>

  <form method="POST" action="{{ isset($registro) ? route('produccion.abms.articulos.update', $registro->id) : route('produccion.abms.articulos.store') }}">
    @csrf
    @if(isset($registro)) @method('PUT') @endif

    <div class="mb-3">
  <label for="id" class="form-label">Id</label>
  <input type="text" name="id" value="{{ $registro->id ?? old('id') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_articulo" class="form-label">Cod articulo</label>
  <input type="text" name="cod_articulo" value="{{ $registro->cod_articulo ?? old('cod_articulo') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_ruta" class="form-label">Cod ruta</label>
  <input type="text" name="cod_ruta" value="{{ $registro->cod_ruta ?? old('cod_ruta') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_linea" class="form-label">Cod linea</label>
  <input type="text" name="cod_linea" value="{{ $registro->cod_linea ?? old('cod_linea') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_marca" class="form-label">Cod marca</label>
  <input type="text" name="cod_marca" value="{{ $registro->cod_marca ?? old('cod_marca') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_rango" class="form-label">Cod rango</label>
  <input type="text" name="cod_rango" value="{{ $registro->cod_rango ?? old('cod_rango') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="denom_articulo" class="form-label">Denom articulo</label>
  <input type="text" name="denom_articulo" value="{{ $registro->denom_articulo ?? old('denom_articulo') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="vigente" class="form-label">Vigente</label>
  <input type="text" name="vigente" value="{{ $registro->vigente ?? old('vigente') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_horma" class="form-label">Cod horma</label>
  <input type="text" name="cod_horma" value="{{ $registro->cod_horma ?? old('cod_horma') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="naturaleza" class="form-label">Naturaleza</label>
  <input type="text" name="naturaleza" value="{{ $registro->naturaleza ?? old('naturaleza') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_familia_producto" class="form-label">Cod familia producto</label>
  <input type="text" name="cod_familia_producto" value="{{ $registro->cod_familia_producto ?? old('cod_familia_producto') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="denom_articulo_largo" class="form-label">Denom articulo largo</label>
  <input type="text" name="denom_articulo_largo" value="{{ $registro->denom_articulo_largo ?? old('denom_articulo_largo') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="created_at" class="form-label">Created at</label>
  <input type="text" name="created_at" value="{{ $registro->created_at ?? old('created_at') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="updated_at" class="form-label">Updated at</label>
  <input type="text" name="updated_at" value="{{ $registro->updated_at ?? old('updated_at') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="sync_status" class="form-label">Sync status</label>
  <input type="text" name="sync_status" value="{{ $registro->sync_status ?? old('sync_status') }}" class="form-control">
</div>


    <button class="btn btn-primary">Guardar</button>
    <a href="{{ route('produccion.abms.articulos.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection