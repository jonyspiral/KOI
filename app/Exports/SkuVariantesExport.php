<?php

namespace App\Exports;

use App\Models\SkuVariante;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class SkuVariantesExport implements FromCollection, WithHeadings
{
    protected $filtros;

    public function __construct(Request $request)
    {
        $this->filtros = $request->all();
    }

    public function collection()
    {
        $query = SkuVariante::with(['tipoProductoStock', 'lineaProducto']);

        // Campos simples
        foreach (['sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo', 'familia', 'color', 'talle'] as $campo) {
            if (!empty($this->filtros[$campo])) {
                $query->where($campo, 'like', '%' . $this->filtros[$campo] . '%');
            }
        }

        // Filtro múltiple por tipo de producto
        if (!empty($this->filtros['id_tipo_producto_stock'])) {
            $query->whereIn('id_tipo_producto_stock', $this->filtros['id_tipo_producto_stock']);
        }

        // Filtro múltiple por línea
        if (!empty($this->filtros['cod_linea'])) {
            $query->whereIn('cod_linea', $this->filtros['cod_linea']);
        }

        return $query->get()->map(function ($r) {
            return [
                $r->sku,
                $r->var_sku,
                $r->ml_name,
                $r->cod_articulo,
                $r->cod_color_articulo,
                $r->familia,
                $r->color,
                $r->talle,
                $r->precio,
                $r->stock,
                $r->stock_ecommerce,
                $r->stock_2da,
                $r->stock_fulfillment,
                $r->id_tipo_producto_stock,
                optional($r->tipoProductoStock)->denom_tipo_producto,
                $r->cod_linea,
                optional($r->lineaProducto)->denom_linea,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Var SKU',
            'ML Name',
            'Cod Artículo',
            'Cod Color Artículo',
            'Familia',
            'Color',
            'Talle',
            'Precio',
            'Stock',
            'Stock Ecommerce',
            'Stock 2da',
            'Stock Full',
            'ID Tipo Producto',
            'Tipo Producto',
            'Cod Línea',
            'Denom Línea',
        ];
    }
}
