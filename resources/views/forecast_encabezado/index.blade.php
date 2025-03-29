@extends('layouts.app')

@section('content')
<div class="container">
  <h2>ForecastEncabezado</h2>
  <a href="{{ route('forecast_encabezado.create') }}" class="btn btn-success mb-2">Nuevo</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>id</th>
<th>IdForecast</th>
<th>Denom_Forecast</th>
<th>Autor</th>
<th>Autoriza</th>
<th>aprobado</th>
<th>anulado</th>
<th>Observaciones</th>
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
<td>{{ $r->IdForecast }}</td>
<td>{{ $r->Denom_Forecast }}</td>
<td>{{ $r->Autor }}</td>
<td>{{ $r->Autoriza }}</td>
<td>{{ $r->aprobado }}</td>
<td>{{ $r->anulado }}</td>
<td>{{ $r->Observaciones }}</td>
<td>{{ $r->created_at }}</td>
<td>{{ $r->updated_at }}</td>
<td>{{ $r->sync_status }}</td>

          <td>
            <a href="{{ route('forecast_encabezado.edit', $r->id) }}" class="btn btn-sm btn-primary">Editar</a>
            <form method="POST" action="{{ route('forecast_encabezado.destroy', $r->id) }}" style="display:inline;">
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