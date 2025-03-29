@extends('layouts.app')

@section('content')
<div class="container">
  <h2>{{ isset($registro) ? 'Editar' : 'Nuevo' }} ForecastEncabezado</h2>

  <form method="POST" action="{{ isset($registro) ? route('forecast_encabezado.update', $registro->id) : route('forecast_encabezado.store') }}">
    @csrf
    @if(isset($registro)) @method('PUT') @endif

    <div class="mb-3">
  <label for="id" class="form-label">Id</label>
  <input type="text" name="id" value="{{ $registro->id ?? old('id') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="IdForecast" class="form-label">IdForecast</label>
  <input type="text" name="IdForecast" value="{{ $registro->IdForecast ?? old('IdForecast') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="Denom_Forecast" class="form-label">Denom Forecast</label>
  <input type="text" name="Denom_Forecast" value="{{ $registro->Denom_Forecast ?? old('Denom_Forecast') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="Autor" class="form-label">Autor</label>
  <input type="text" name="Autor" value="{{ $registro->Autor ?? old('Autor') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="Autoriza" class="form-label">Autoriza</label>
  <input type="text" name="Autoriza" value="{{ $registro->Autoriza ?? old('Autoriza') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="aprobado" class="form-label">Aprobado</label>
  <input type="text" name="aprobado" value="{{ $registro->aprobado ?? old('aprobado') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="anulado" class="form-label">Anulado</label>
  <input type="text" name="anulado" value="{{ $registro->anulado ?? old('anulado') }}" class="form-control">
</div>
<div class="mb-3">
  <label for="Observaciones" class="form-label">Observaciones</label>
  <input type="text" name="Observaciones" value="{{ $registro->Observaciones ?? old('Observaciones') }}" class="form-control">
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
    <a href="{{ route('forecast_encabezado.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection