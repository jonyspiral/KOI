@extends('layouts.app')

@section('content')
<div class="container">
<h2>{{ isset($registro) ? 'Editar' : 'Nuevo' }} {{ $modelo }}</h2>

<form method="POST" action="{{ isset($registro) ? route('produccion.secciones_produccion.update', $registro->id) : route('produccion.secciones_produccion.store') }}">
@csrf
@if(isset($registro)) @method('PUT') @endif

<div class="mb-3">
<label for="cod_seccion" class="form-label">Cod seccion</label>
<input type="text" name="cod_seccion" value="{{ $registro->cod_seccion ?? old('cod_seccion') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="ejecucion" class="form-label">Ejecucion</label>
<input type="text" name="ejecucion" value="{{ $registro->ejecucion ?? old('ejecucion') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="denom_seccion" class="form-label">Denom seccion</label>
<input type="text" name="denom_seccion" value="{{ $registro->denom_seccion ?? old('denom_seccion') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="denom_corta" class="form-label">Denom corta</label>
<input type="text" name="denom_corta" value="{{ $registro->denom_corta ?? old('denom_corta') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="unid_med_cap_prod" class="form-label">Unid med cap prod</label>
<input type="text" name="unid_med_cap_prod" value="{{ $registro->unid_med_cap_prod ?? old('unid_med_cap_prod') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="interrumpible" class="form-label">Interrumpible</label>
<input type="text" name="interrumpible" value="{{ $registro->interrumpible ?? old('interrumpible') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="anulado" class="form-label">Anulado</label>
<input type="text" name="anulado" value="{{ $registro->anulado ?? old('anulado') }}" class="form-control" >
</div>
<div class="mb-3">
<label for="sync_status" class="form-label">Sync status</label>
<input type="text" name="sync_status" value="{{ $registro->sync_status ?? old('sync_status') }}" class="form-control" >
</div>


<button class="btn btn-primary">Guardar</button>
<a href="{{ route('produccion.secciones_produccion.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
</div>
@endsection