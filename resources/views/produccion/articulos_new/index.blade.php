@extends('layouts.app')

@section('content')
<div class="container">
  <h2></h2>
  <a href="{{ route('produccion.articulos_new.create') }}" class="btn btn-success mb-2">Nuevo</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>id</th>
<th>cod_articulo</th>
<th>denom_articulo</th>
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
<td>{{ $r->denom_articulo }}</td>
<td>{{ $r->created_at }}</td>
<td>{{ $r->updated_at }}</td>
<td>{{ $r->sync_status }}</td>

          <td>
            <a href="{{ route('produccion.articulos_new.edit', $r->id) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('produccion.articulos_new.destroy', $r->id) }}" style="display:inline;">
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