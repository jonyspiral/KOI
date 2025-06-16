<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>VAR SKU</th>
            <th>ML Name</th>
            <th>Cod Artículo</th>
            <th>Color</th>
            <th>Talle</th>
            <th>Tipo Producto</th>
            <th>Denom Línea</th>
            <th>Precio</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registros as $r)
        <tr>
            <td>{{ $r->sku }}</td>
            <td>{{ $r->var_sku }}</td>
            <td>{{ $r->ml_name }}</td>
            <td>{{ $r->cod_articulo }}</td>
            <td>{{ $r->cod_color_articulo }}</td>
            <td>{{ $r->talle }}</td>
            <td>{{ $r->tipo_producto }}</td>
            <td>{{ $r->denom_linea }}</td>
            <td>{{ $r->precio }}</td>
            <td>{{ $r->stock }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
