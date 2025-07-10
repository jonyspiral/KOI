<?php

namespace App\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SkuVariante;
use App\Models\TipoProductoStock;
use App\Models\LineasProducto;
use Illuminate\Support\Facades\Schema;
use App\Traits\PersisteFiltrosTrait;
class SkuVarianteController extends Controller
{
    use PersisteFiltrosTrait;

    public function index(Request $request)
{
    // 🎯 Campos a guardar en sesión
    $camposFiltrables = [
        'sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo',
        'familia', 'color', 'talle','precio', 
        'ml_price', 'eshop_price', 'segunda_price',
        'id_tipo_producto_stock', 'cod_linea',
        'sort', 'dir', 'page',
    ];

    // 💾 Aplicar lógica del Trait
    $requestFiltrado = $this->manejarFiltros($request, 'sku_variantes_filtros', $camposFiltrables);

    if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) {
        return $requestFiltrado;
    }

    $request = $requestFiltrado;

    // 🧱 Query base
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

    // 📊 Ordenamiento
    $sort = $request->get('sort', 'sku');
    $dir  = $request->get('dir', 'asc');
    if (\Schema::hasColumn('sku_variantes', $sort)) {
        $query->orderBy($sort, $dir);
    }

    // 📦 Totales
    $totales = (clone $query)->selectRaw("
        SUM(stock) as total,
        SUM(ecommerce) as ecommerce_total,
        SUM(2da) as 2da_total,
        SUM(fulfillment) as fulfillment_total
    ")->first();

    // 📄 Paginación con filtros aplicados
    $registros = $query->paginate(30)->appends($request->query());

    // 🎛️ Filtros desplegables
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
