@extends('layouts.app')

@section('content')
<div class="container">
  <h2>{{ isset($registro) ? 'Editar' : 'Nuevo' }} </h2>

  <form method="POST" action="{{ isset($registro) ? route('produccion.abms.hormas.update', $registro->id) : route('produccion.abms.hormas.store') }}">
    @csrf
    @if(isset($registro)) @method('PUT') @endif

    <div class="mb-3">
  <label for="id" class="form-label">Id</label>
  <input type="text" name="id" value="{{ $registro->id ?? old('id') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="cod_horma" class="form-label">Cod horma</label>
  <input type="text" name="cod_horma" value="{{ $registro->cod_horma ?? old('cod_horma') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="denom_horma" class="form-label">Denom horma</label>
  <input type="text" name="denom_horma" value="{{ $registro->denom_horma ?? old('denom_horma') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="talles_desde" class="form-label">Talles desde</label>
  <input type="text" name="talles_desde" value="{{ $registro->talles_desde ?? old('talles_desde') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="talles_hasta" class="form-label">Talles hasta</label>
  <input type="text" name="talles_hasta" value="{{ $registro->talles_hasta ?? old('talles_hasta') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="punto" class="form-label">Punto</label>
  <input type="text" name="punto" value="{{ $registro->punto ?? old('punto') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="observaciones" class="form-label">Observaciones</label>
  <textarea name="observaciones" class="form-control">{{ $registro->observaciones ?? old('observaciones') }}</textarea>
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
    <a href="{{ route('produccion.abms.hormas.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection