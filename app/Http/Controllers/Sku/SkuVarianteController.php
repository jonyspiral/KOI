<?php

namespace App\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SkuVariante;
use App\Models\TipoProductoStock;
use App\Models\LineasProducto;
use Illuminate\Support\Facades\Schema;

class SkuVarianteController extends Controller
{
    public function index(Request $request)
    {
        $query = SkuVariante::with(['tipoProductoStock', 'lineaProducto']);

        $camposFiltrables = [
            'sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo',
            'familia', 'color', 'talle', 'precio',
            'id_tipo_producto_stock', 'cod_linea',
        ];

        foreach ($camposFiltrables as $campo) {
            if ($request->filled($campo)) {
                if (is_array($request->$campo)) {
                    $query->whereIn($campo, $request->$campo);
                } else {
                    $query->where($campo, 'like', '%' . $request->$campo . '%');
                }
            }
        }

        $sort = $request->get('sort', 'sku');
        $dir  = $request->get('dir', 'asc');

        if (Schema::hasColumn('sku_variantes', $sort)) {
            $query->orderBy($sort, $dir);
        }
        $totales = (clone $query)->selectRaw("
            SUM(stock) as stock_total,
            SUM(stock_ecommerce) as stock_ecommerce_total,
            SUM(stock_2da) as stock_2da_total,
            SUM(stock_fulfillment) as stock_fulfillment_total
        ")->first();

        $registros = $query->paginate(30)->appends($request->query());

        // Para filtros desplegables (si usás select[multiple])
        $tiposProducto = TipoProductoStock::pluck('denom_tipo_producto', 'id_tipo_producto_stock');
        $lineasProducto = LineasProducto::pluck('denom_linea', 'cod_linea');

        return view('sku.sku_variantes.index', compact('registros', 'tiposProducto', 'lineasProducto','totales'));
    }

    public function show($id)
    {
        $registro = SkuVariante::with(['tipoProductoStock', 'lineaProducto'])->findOrFail($id);
        return view('sku.sku_variantes.show', compact('registro'));
    }
}
