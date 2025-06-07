@extends('layouts.app')

@section('content')
<div class="container">
    <h3>👟 Publicar variantes por talle</h3>

    @if(count($variantes) > 0)
        <div class="mb-4">
            <img src="{{ $variantes[0]->imagen1 ?? 'https://via.placeholder.com/150' }}" width="150">
            <p><strong>SKU:</strong> {{ $variantes[0]->sku ?? '-' }}</p>
        </div>

        <form method="POST" action="{{ route('mlibre.publicar.variantes.enviar', ['sku' => $variantes[0]->sku]) }}">

            @csrf

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>✓</th>
                        <th>Talle</th>
                        <th>Stock</th>
                        <th>Precio</th>
                        <th>Color</th>
                        <th>SKU Variante</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($variantes as $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="seleccionados[]" value="{{ $item->sku_variante }}">
                            </td>
                            <td>{{ $item->variante1 ?? '-' }}</td>
                            <td>{{ $item->cantidad }}</td>
                            <td>${{ number_format($item->precio_valor1, 0, ',', '.') }}</td>
                            <td>{{ $item->variante2 }}</td>
                            <td>{{ $item->sku_variante }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">📤 Publicar variantes seleccionadas</button>
        </form>
    @else
        <p>No hay variantes disponibles con stock.</p>
    @endif
</div>
@endsection
