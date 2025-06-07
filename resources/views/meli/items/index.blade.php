@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">🍒 Publicaciones en Mercado Libre</h2>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Link</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['id'] }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>${{ $item['price'] }}</td>
                    <td>{{ $item['status'] }}</td>
                    <td><a href="{{ $item['permalink'] }}" target="_blank">Ver</a></td>
                    <td>
                        {{-- Formulario de actualización --}}
                        <form method="POST" action="/meli/items/{{ $item['id'] }}/actualizar" style="display:inline-block;">
                            @csrf
                            <input type="text" name="price" value="{{ $item['price'] }}" style="width: 80px;" />
                            <input type="text" name="title" value="{{ $item['title'] }}" style="width: 120px;" />
                            <button class="btn btn-sm btn-primary" title="Guardar cambios">📂</button>
                        </form>

                        {{-- Botones de estado --}}
                        @if($item['status'] === 'paused')
                            <form method="POST" action="/meli/items/{{ $item['id'] }}/activar" style="display:inline-block;">
                                @method('PUT') @csrf
                                <button class="btn btn-sm btn-success" title="Activar">▶️</button>
                            </form>
                        @elseif($item['status'] === 'active')
                            <form method="POST" action="/meli/items/{{ $item['id'] }}/pausar" style="display:inline-block;">
                                @method('PUT') @csrf
                                <button class="btn btn-sm btn-warning" title="Pausar">⏸️</button>
                            </form>
                        @endif

                        {{-- Botón de eliminar (pausa permanente) --}}
                        <form method="POST" action="/meli/items/{{ $item['id'] }}/pausar" style="display:inline-block;">
                            @method('PUT') @csrf
                            <button class="btn btn-sm btn-danger" title="Eliminar">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
