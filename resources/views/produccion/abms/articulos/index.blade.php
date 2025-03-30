@extends('layouts.app')

@section('content')
<div class="container">
  <h2></h2>
  <a href="{{ route('produccion.abms.articulos.create') }}" class="btn btn-success mb-2">Nuevo</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>id</th>
<th>cod_articulo</th>
<th>cod_ruta</th>
<th>cod_linea</th>
<th>cod_marca</th>
<th>cod_rango</th>
<th>denom_articulo</th>
<th>vigente</th>
<th>cod_horma</th>
<th>naturaleza</th>
<th>cod_familia_producto</th>
<th>denom_articulo_largo</th>
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
<td>{{ $r->cod_articulo }}</td>
<td>{{ $r->cod_ruta }}</td>
<td>{{ $r->cod_linea }}</td>
<td>{{ $r->cod_marca }}</td>
<td>{{ $r->cod_rango }}</td>
<td>{{ $r->denom_articulo }}</td>
<td>{{ $r->vigente }}</td>
<td>{{ $r->cod_horma }}</td>
<td>{{ $r->naturaleza }}</td>
<td>{{ $r->cod_familia_producto }}</td>
<td>{{ $r->denom_articulo_largo }}</td>
<td>{{ $r->created_at }}</td>
<td>{{ $r->updated_at }}</td>
<td>{{ $r->sync_status }}</td>

          <td>
            <a href="{{ route('produccion.abms.articulos.edit', $r->id) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('produccion.abms.articulos.destroy', $r->id) }}" style="display:inline;">
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