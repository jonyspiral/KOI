@extends('layouts.app')

@section('content')
<div class="container">
  <h2>{{ isset($registro) ? 'Editar' : 'Nuevo' }} RutasProduccion</h2>

  <form method="POST" action="{{ isset($registro) ? route('rutas_produccion.update', $registro->id) : route('rutas_produccion.store') }}">
    @csrf
    @if(isset($registro)) @method('PUT') @endif

    <div class="mb-3">
  <label for="id" class="form-label">Id</label>
  <input type="text" name="id" value="{{ $registro->id ?? old('id') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_ruta" class="form-label">Cod ruta</label>
  <input type="text" name="cod_ruta" value="{{ $registro->cod_ruta ?? old('cod_ruta') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="denom_ruta" class="form-label">Denom ruta</label>
  <input type="text" name="denom_ruta" value="{{ $registro->denom_ruta ?? old('denom_ruta') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="anulado" class="form-label">Anulado</label>
  <input type="text" name="anulado" value="{{ $registro->anulado ?? old('anulado') }}" class="form-control">
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
    <a href="{{ route('rutas_produccion.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection