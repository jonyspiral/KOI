{{-- 📄 Generado por Sofía
     Función: Plantilla de exportación a Excel para Stock
     Fecha: 2025-07-11 --}}

<table>
    <thead>
        <tr>
            <th>Código Artículo</th>
            <th>Código Color</th>
            <th>Denominación</th>

            @foreach ($talles as $talle)
                <th>{{ $talle }}</th>
            @endforeach

            <th>Total</th>

            {{-- Filtros aplicados como columnas extra --}}
            @if (!empty($filas[0]))
                @foreach (array_keys(array_filter($filas[0], fn($key) => str_starts_with($key, 'filtro_'), ARRAY_FILTER_USE_KEY)) as $columnaFiltro)
                    <th>{{ ucfirst(str_replace('filtro_', '', $columnaFiltro)) }}</th>
                @endforeach
            @endif
        </tr>
    </thead>

    <tbody>
        @foreach ($filas as $fila)
            <tr>
                <td>{{ $fila['cod_articulo'] }}</td>
                <td>{{ $fila['cod_color_articulo'] }}</td>
                <td>{{ $fila['denom_articulo'] }}</td>

                @foreach ($talles as $talle)
                    <td>{{ $fila["talle_$talle"] ?? 0 }}</td>
                @endforeach

                <td>{{ $fila['total'] }}</td>

                {{-- Mostrar valores de filtros aplicados --}}
                @foreach ($fila as $key => $valor)
                    @if (str_starts_with($key, 'filtro_'))
                        <td>{{ $valor }}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
