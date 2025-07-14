{{-- 📄 Generado por Sofía
     Función: Plantilla de exportación a Excel para Stock (datos planos procesados)
     Fecha: 2025-07-14 --}}

<table>
   <thead>
    <tr>
        <th>Código Artículo</th>
        <th>Código Color</th>
        <th>Denominación</th>
        <th>Familia</th>
        <th>Línea</th>
        <th>Tipo Producto</th>
        <th>Comercialización</th>

        @foreach ($talles as $talle)
            <th>{{ $talle }}</th>
        @endforeach

        <th>Total</th>
    </tr>
</thead>

    <tbody>
    @foreach ($filas as $fila)
        <tr>
            <td>{{ $fila['cod_articulo'] }}</td>
            <td>{{ $fila['cod_color_articulo'] }}</td>
            <td>{{ $fila['denom_articulo'] }}</td>
            <td>{{ $fila['familia'] ?? '' }}</td>
            <td>{{ $fila['linea'] ?? '' }}</td>
            <td>{{ $fila['tipo_producto_stock'] ?? '' }}</td>
            <td>{{ $fila['forma_comercializacion'] ?? '' }}</td>

            @foreach ($talles as $talle)
                <td>{{ $fila["talle_{$talle}"] ?? 0 }}</td>
            @endforeach

            <td>{{ $fila['total'] }}</td>
        </tr>
    @endforeach
</tbody>

</table>
