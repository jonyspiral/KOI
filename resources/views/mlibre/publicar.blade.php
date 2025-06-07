@extends('layouts.app')

@section('content')
<div class="container">
    <h3>📦 Publicar productos en Mercado Libre</h3>

    <form method="POST" action="{{ route('mlibre.publicar.enviar') }}">
        @csrf

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>✓</th>
                    <th>Imagen</th>
                    <th>SKU</th>
                    <th>Color</th>
                    <th>Agrupador</th>
                    <th>Stock</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productos as $item)
                    <tr>
                        <td>
                            <input type="checkbox" name="seleccionados[]" value="{{ $item->sku }}">
                        </td>
                        <td>
                            <img src="{{ $item->imagen_1_url }}" width="60">
                        </td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->nombre_color }}</td>
                        <td>{{ $item->agrupador }}</td>
                        <td>{{ $item->stock_total }}</td>
                        <td>${{ number_format($item->precio, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">📤 Publicar seleccionados</button>
    </form>
</div>
@endsection
