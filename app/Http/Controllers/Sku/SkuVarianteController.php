<?php

namespace App\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SkuVariante;
use App\Models\TipoProductoStock;
use App\Models\LineasProducto;
use App\Traits\PersisteFiltrosTrait;

class SkuVarianteController extends Controller
{
    use PersisteFiltrosTrait;

    public function index(Request $request)
{
    $camposFiltrables = [
        'sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo',
        'familia', 'color', 'talle','precio', 
        'ml_price', 'eshop_price', 'segunda_price',
        'id_tipo_producto_stock', 'cod_linea',
        'sort', 'dir', 'page',
    ];

    $requestFiltrado = $this->manejarFiltros($request, 'sku_variantes_filtros', $camposFiltrables);

    if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) {
        return $requestFiltrado;
    }

    $request = $requestFiltrado;

    $query = SkuVariante::with(['tipoProductoStock', 'lineaProducto']);

    foreach ($camposFiltrables as $campo) {
        if (!in_array($campo, ['sort', 'dir', 'page']) && $request->filled($campo)) {
            if (is_array($request->$campo)) {
                $query->whereIn($campo, $request->$campo);
            } else {
                $query->where($campo, 'like', '%' . $request->$campo . '%');
            }
        }
    }

    $sort = $request->get('sort', 'sku');
    $dir  = $request->get('dir', 'asc');
    if (\Schema::hasColumn('sku_variantes', $sort)) {
        $query->orderBy($sort, $dir);
    }

    // 📄 Paginación
$registros = $query->paginate(30)->appends($request->query());

// 📦 Totales virtuales calculados sobre TODO el dataset filtrado
$totales = [
    'total'            => 0,
    'ecommerce_total'  => 0,
    'segunda_total'    => 0,
    'fulfillment_total'=> 0,
];

// Usamos chunk para evitar cargar todo en memoria
(clone $query)->chunk(200, function ($chunk) use (&$totales) {
    foreach ($chunk as $sku) {
        $totales['total']             += $sku->stock;
        $totales['ecommerce_total']   += $sku->stock_ecommerce;
        $totales['segunda_total']     += $sku->stock_2da;
        $totales['fulfillment_total'] += $sku->stock_fulfillment;
    }
});
    $tiposProducto = TipoProductoStock::pluck('denom_tipo_producto', 'id_tipo_producto_stock');
    $lineasProducto = LineasProducto::pluck('denom_linea', 'cod_linea');

    return view('sku.sku_variantes.index', compact(
        'registros', 'tiposProducto', 'lineasProducto', 'totales'
    ));
}


    public function show($id)
    {
        $registro = SkuVariante::with(['tipoProductoStock', 'lineaProducto'])->findOrFail($id);
        return view('sku.sku_variantes.show', compact('registro'));
    }
}
