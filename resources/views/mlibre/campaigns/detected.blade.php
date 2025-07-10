{{-- 📄 resources/views/mlibre/campaigns/detected.blade.php --}}
@extends('adminlte::page')

@section('title', 'Ítems promocionados sin campaña')

@section('content_header')
    <h1>🧩 Ítems promocionados sin campaña formal</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if ($items->isEmpty())
                <p>No se detectaron ítems en promoción sin campaña asignada.</p>
            @else
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Item ID</th>
                            <th>SKU</th>
                            <th>Modelo</th>
                            <th>Color</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->item_id }}</td>
                                <td>{{ $item->variante->sku ?? '-' }}</td>
                                <td>{{ $item->variante->modelo->denominacion ?? '-' }}</td>
                                <td>{{ $item->variante->color->nombre ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@stop
