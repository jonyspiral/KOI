{{-- 📄 subform-colores.blade.php --}}

        <div class="border p-3 bg-light rounded shadow-sm">
            {{-- Tabla de colores --}}
            <table class="table table-sm table-striped mb-0">
                <thead class="table-secondary text-center">
                    <tr>
                        <th>📷</th>
                        <th>Cod. Color</th>
                        <th>Nombre</th>
                        <th>Vigente</th>
                        <th>Precio Base</th>
                        <th>Precio Eshop</th>
                        <th>Precio ML</th>
                        <th>Tipo Producto</th>
                        <th>Comercialización</th>
                        <th>Existe Eshop</th>
                        <th>Stock Eshop</th>
                        <th>Stock 1°</th>
                        <th>Stock 2°</th>
                        <th>Stock Prod</th>
                        <th>Stock Ped</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articulo->coloresPorArticulo as $sub)
                        <tr>
                            <td>
                                @if ($sub->fotografia)
                                    <img src="{{ asset('storage/fotos/' . $sub->fotografia) }}" width="40">
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $sub->cod_color_articulo }}</td>
                            <td>{{ $sub->denom_color }}</td>
                            <td>{{ $sub->vigente === 'S' ? '✅' : '—' }}</td>
                            <td>${{ $sub->precio_mayorista_usd }}</td>
                            <td>${{ $sub->ecommerce_price1 }}</td>
                            <td>${{ $sub->mlibre_precio }}</td>
                            <td>{{ optional($sub->tipo_producto_stock)->denom_tipo_producto ?? '—' }}</td>
                            <td>{{ $sub->comercializacion_libre }}</td>
                            <td>{{ $sub->ecommerce_existe === 'S' ? '✅' : '—' }}</td>


                            
                            <td>{{ $sub->stock_eshop ?? '—' }}</td>
                            <td>{{ $sub->stock_1 ?? '—' }}</td>
                            <td>{{ $sub->stock_2 ?? '—' }}</td>
                            <td>{{ $sub->stock_produccion ?? '—' }}</td>
                            <td>{{ $sub->stock_pedidos ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="text-center text-muted">No hay colores cargados para este artículo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
