@extends('adminlte::page')

@section('title', 'Variantes ML')

@section('content_header')
    <h1>📦 Variantes Publicadas en Mercado Libre</h1>
@stop

@section('content')
    <div class="mb-3">
        <form method="GET" action="">
            <div class="form-group">
                <label for="en_campania">🎯 Mostrar solo en campaña</label>
                <select name="en_campania" id="en_campania" class="form-control" onchange="this.form.submit()">
                    <option value="">— Todas —</option>
                    <option value="1" {{ request('en_campania') == '1' ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
        </form>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Item ID ML</th>
                <th>SKU</th>
                <th>Modelo</th>
                <th>Color</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>🎯</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($variantes as $var)
                <tr>
                    <td>{{ $var->id }}</td>
                    <td>{{ $var->ml_id }}</td>
                    <td>{{ $var->sku }}</td>
                    <td>{{ $var->modelo->denominacion ?? '-' }}</td>
                    <td>{{ $var->color->nombre ?? '-' }}</td>
                    <td>{{ $var->stock ?? 0 }}</td>
                    <td>{{ $var->ml_price ? '$' . number_format($var->ml_price, 0, ',', '.') : '-' }}</td>
                    <td>
                        @if ($var->campaignItems()->exists())

                            <span title="Está en campaña">🎯</span>
                        @else
                            <span title="No está en campaña">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $variantes->appends(request()->query())->links() }}
    </div>
@stop
