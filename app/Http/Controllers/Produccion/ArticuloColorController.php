<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\ColoresPorArticulo;
use App\Models\FamiliasProducto;
use App\Models\TipoProductoStock;
use App\Services\StockService;
use App\Traits\PersisteFiltrosTrait;

use App\Models\RangoTalle;
use App\Models\Horma;


class ArticuloColorController extends Controller
{

    use PersisteFiltrosTrait;



public function index(Request $request)
{
    $campos = [
        'cod_articulo', 'denom_articulo', 'unidad', 'linea', 'vigente',
        'descripcion', 'familia', 'ruta', 'rango', 'horma', 'marca',
        'sort', 'dir', 'page'
    ];

    // Persistencia de filtros
    $requestFiltrado = $this->manejarFiltros($request, 'articulocolor_filtros', $campos);
    if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) return $requestFiltrado;
    $request = $requestFiltrado;

    // Alias para joins y filtros (solo del PADRE)
    $aliasMap = [
        'linea'   => 'lp.denom_linea',
        'familia' => 'fp.nombre',
        'ruta'    => 'rp.denom_ruta',
        'rango'   => 'rt.denom_rango',
        'horma'   => 'h.denom_horma',
        'marca'   => 'm.denom_marca',
    ];

    // Query del PADRE (sin campos del hijo)
    $query = Articulo::select('articulos.*')
        ->leftJoin('lineas_productos as lp', 'articulos.cod_linea', '=', 'lp.cod_linea')
        ->leftJoin('familias_producto as fp', 'articulos.cod_familia_producto', '=', 'fp.id')
        ->leftJoin('Rutas_produccion as rp', 'articulos.cod_ruta', '=', 'rp.cod_ruta')
        ->leftJoin('rango_talles as rt', 'articulos.cod_rango', '=', 'rt.cod_rango')
        ->leftJoin('hormas as h', 'articulos.cod_horma', '=', 'h.cod_horma')
        ->leftJoin('Marcas as m', 'articulos.cod_marca', '=', 'm.cod_marca')
        ->addSelect([
            'lp.denom_linea as linea',
            'fp.nombre as familia',
            'rp.denom_ruta as ruta',
            'rt.denom_rango as rango',
            'h.denom_horma as horma',
            'm.denom_marca as marca',
        ])
        ->where('articulos.cod_articulo', '!=', '');

    // Filtros (solo campos del PADRE)
    foreach ($campos as $campo) {
        if (!in_array($campo, ['sort', 'dir', 'page']) && $request->filled($campo)) {
            $columna = $aliasMap[$campo] ?? "articulos.$campo";
            if (is_array($request->$campo)) {
                $query->whereIn($columna, $request->$campo);
            } else {
                $query->where($columna, 'like', '%' . $request->$campo . '%');
            }
        }
    }

    // Ordenamiento (sin tiposProducto ni forma_comercializacion)
    $sortMap = [
        'cod_articulo'   => 'articulos.cod_articulo',
        'denom_articulo' => 'articulos.denom_articulo',
        'unidad'         => 'articulos.unidad',
        'linea'          => 'lp.denom_linea',
        'vigente'        => 'articulos.vigente',
        'descripcion'    => 'articulos.descripcion',
        'familia'        => 'fp.nombre',
        'ruta'           => 'rp.denom_ruta',
        'rango'          => 'rt.denom_rango',
        'horma'          => 'h.denom_horma',
        'marca'          => 'm.denom_marca',
    ];

    $sort = $request->get('sort', 'cod_articulo');
    $dir = $request->get('dir', 'asc');
    $sortColumn = $sortMap[$sort] ?? 'articulos.cod_articulo';
    $query->orderBy($sortColumn, $dir);

    // Eager loading para el SUBFORM (acá sí vienen los campos de hijo)
    $query->with('coloresPorArticulo.tipo_producto_stock');

    $articulos = $query->paginate(20)->appends($request->all());

    // Cálculo de stock por color (hijo)
    foreach ($articulos as $articulo) {
        foreach ($articulo->coloresPorArticulo as $color) {
            $color->stock_eshop = \App\Services\StockService::stockTotalPorArticuloColor(
                $articulo->cod_articulo,
                $color->cod_color_articulo,
                ['07', '14', '20']
            );
            $color->stock_1 = \App\Services\StockService::stockTotalPorArticuloColor(
                $articulo->cod_articulo,
                $color->cod_color_articulo,
                ['01']
            );
            $color->stock_2 = \App\Services\StockService::stockTotalPorArticuloColor(
                $articulo->cod_articulo,
                $color->cod_color_articulo,
                ['02']
            );
        }
    }

    // Catálogos del PADRE (para filtros del thead)
    $familias = FamiliasProducto::orderBy('nombre')
    ->get(['id', 'nombre']);

$rutas    = \App\Models\RutasProduccion::orderBy('denom_ruta')->get();
$rangos   = \App\Models\RangoTalle::orderBy('denom_rango')->get();

$hormas   = Horma::orderBy('denom_horma')->get(['cod_horma', 'denom_horma']);
$marcas   = \App\Models\Marca::orderBy('denom_marca')->get();

$lineas   = \App\Models\LineasProducto::orderBy('denom_linea')->get();

    return view('produccion.abms.articulocolor.index-inline', compact(
        'articulos', 'familias', 'rutas', 'rangos', 'hormas', 'marcas',
    'lineas'
    ));
}





public function create()
{
    $familias = \App\Models\FamiliasProducto::orderBy('nombre')->get();
    $rutas = \App\Models\RutaProduccion::orderBy('denom_ruta')->get();
    $rangos = \App\Models\RangoTalle::orderBy('denom_rango')->get();
    $hormas = \App\Models\Horma::orderBy('denom_horma')->get();
    $marcas = \App\Models\Marca::orderBy('denom_marca')->get();
    $rubros = \App\Models\RubroIva::orderBy('nombre')->get();
    $lineas = \App\Models\LineasProducto::orderBy('denom_linea')->get();

    return view('produccion.abms.articulocolor.partials.create-modal', compact(
        'familias', 'rutas', 'rangos', 'hormas', 'marcas', 'rubros', 'lineas'
    ));
}

    public function store(Request $request)
    {
        $articulo = Articulo::create($request->all());

        return redirect()->route('articulocolor.edit', $articulo->id)
                         ->with('success', 'Artículo creado correctamente.');
    }

public function edit($id)
{
    $articulo = Articulo::with('coloresPorArticulo')->findOrFail($id);

    $familias = \App\Models\FamiliasProducto::all();
    $rutas    = \App\Models\RutasProduccion::all();
    $rangos   = \App\Models\RangoTalle::all();
    $hormas   = \App\Models\Horma::all();
    $marcas   = \App\Models\Marca::all();
    $rubros_iva = \App\Models\RubrosIva::all();
    return view('produccion.abms.articulocolor.edit', compact(
        'articulo',
        'familias',
        'rutas',
        'rangos',
        'hormas',
        'rubros_iva',
        'marcas'
    ));
}


    public function update(Request $request, $id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->update($request->all());

        return redirect()->route('articulocolor.edit', $id)
                         ->with('success', 'Artículo actualizado.');
    }

    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->delete();

        return back()->with('success', 'Artículo eliminado.');
    }
}
