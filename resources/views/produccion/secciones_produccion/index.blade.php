@extends('layouts.app')

@section('content')
<div class="container">
<h2>{{ $modelo }}</h2>
<a href="{{ route('produccion.secciones_produccion.create') }}" class="btn btn-success mb-2">Nuevo</a>
<table class="table table-bordered">
<thead>
  <tr>
    <th>cod_seccion</th>
<th>ejecucion</th>
<th>denom_seccion</th>
<th>denom_corta</th>
<th>unid_med_cap_prod</th>
<th>interrumpible</th>
<th>anulado</th>
<th>sync_status</th>

    <th>Acciones</th>
  </tr>
</thead>
<tbody>
  @foreach($registros as $r)
    <tr>
      <td>{{ $r->cod_seccion }}</td>
<td>{{ $r->ejecucion }}</td>
<td>{{ $r->denom_seccion }}</td>
<td>{{ $r->denom_corta }}</td>
<td>{{ $r->unid_med_cap_prod }}</td>
<td>{{ $r->interrumpible }}</td>
<td>{{ $r->anulado }}</td>
<td>{{ $r->sync_status }}</td>

      <td>
        <a href="{{ route('produccion.secciones_produccion.edit', $r->id) }}" class="btn btn-sm btn-primary">Editar</a>
        <form method="POST" action="{{ route('produccion.secciones_produccion.destroy', $r->id) }}" style="display:inline;">
          @csrf
          @method('DELETE')
          <button class="btn btn-sm btn-danger">Eliminar</button>
        </form>
      </td>
    </tr>
  @endforeach
</tbody>
</table>
</div>
@endsection