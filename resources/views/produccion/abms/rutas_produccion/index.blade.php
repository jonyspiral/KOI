@extends('layouts.app')

@section('content')
<div class="container">
  <h2></h2>
  <a href="{{ route('produccion.abms.rutas_produccion.create') }}" class="btn btn-success mb-2">Nuevo</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>id</th>
<th>cod_ruta</th>
<th>denom_ruta</th>
<th>anulado</th>
<th>created_at</th>
<th>updated_at</th>
<th>sync_status</th>

        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach($registros as $r)
        <tr>
          <td>{{ $r->id }}</td>
<td>{{ $r->cod_ruta }}</td>
<td>{{ $r->denom_ruta }}</td>
<td>{{ $r->anulado }}</td>
<td>{{ $r->created_at }}</td>
<td>{{ $r->updated_at }}</td>
<td>{{ $r->sync_status }}</td>

          <td>
            <a href="{{ route('produccion.abms.rutas_produccion.edit', $r->id) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('produccion.abms.rutas_produccion.destroy', $r->id) }}" style="display:inline;">
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