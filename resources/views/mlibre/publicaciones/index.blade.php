@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h2 class="mb-4">📦 Publicaciones Mercado Libre</h2>

    <form method="GET" class="flex items-center gap-2 mb-4">
        <select name="campo" class="border rounded p-1">
            <option value="ml_id" {{ request('campo') == 'ml_id' ? 'selected' : '' }}>ID</option>
            <option value="ml_reference" {{ request('campo') == 'ml_reference' ? 'selected' : '' }}>Ref.</option>
            <option value="ml_name" {{ request('campo') == 'ml_name' ? 'selected' : '' }}>Título</option>
            <option value="status" {{ request('campo') == 'status' ? 'selected' : '' }}>Estado</option>
        </select>

        <input type="text" name="buscar" value="{{ request('buscar') }}" class="border rounded p-1" placeholder="Buscar...">
        
        <button type="submit" class="bg-blue-500 text-white rounded px-3 py-1">Buscar</button>
    </form>

    <table class="table table-sm table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Ref.</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($publicaciones as $p)
                <tr>
                    <td>{{ $p->ml_id }}</td>
                    <td>{{ $p->ml_name }}</td>
                    <td>{{ $p->ml_reference }}</td>
                    <td>{{ $p->mlibre_precio }}</td>
                    <td>{{ $p->mlibre_stock }}</td>
                    <td>{{ $p->status }}</td>
                    <td>
                        <a href="{{ route('mlibre.publicaciones.edit', $p->id) }}" class="btn btn-sm btn-secondary">✏️</a>
                        <a href="#" onclick="alert(JSON.stringify({{ json_encode($p->raw_json) }}, null, 2))" class="btn btn-sm btn-info">🧾 JSON</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $publicaciones->links() }}
</div>
@endsection
