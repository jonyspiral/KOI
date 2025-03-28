@extends('layouts.app')

@section('content')
<div class="container">
  <h2>{{ isset($registro) ? 'Editar' : 'Nuevo' }} </h2>

  <form method="POST" action="{{ isset($registro) ? route('produccion.articulos_new.update', $registro->id) : route('produccion.articulos_new.store') }}">
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
  <label for="denom_articulo" class="form-label">Denom articulo</label>
  <input type="text" name="denom_articulo" value="{{ $registro->denom_articulo ?? old('denom_articulo') }}" class="form-control">
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
    <a href="{{ route('produccion.articulos_new.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection