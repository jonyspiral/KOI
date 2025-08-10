<?php

namespace App\Http\Controllers\Produccion;

use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\ColoresPorArticulo;
use App\Models\RangoTalle;
use App\Models\TipoProductoStock;
use App\Models\Almacen;
use App\Models\FamiliasProducto;
use App\Models\LineasProducto;
use App\Traits\PersisteFiltrosTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\FilterProvider;
use App\Services\Produccion\StockExportService;
use App\Services\StockService;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockExport;
use Illuminate\Support\Facades\Cache;

use App\Helpers\MemCacheHelper;



class AnalisisStockController extends Controller
{
    use PersisteFiltrosTrait;

  public function index(Request $request)
{
    if ($request->has('reset')) {
        session()->forget('filtros_analisis_stock');
        return redirect()->route('produccion.analisis-stock.index');
    }

    $request = $this->manejarFiltros($request, 'filtros_analisis_stock', [
        'cod_articulo', 'color', 'familia', 'linea', 'almacen', 'denom_articulo',
        'tipo_producto_stock', 'forma_comercializacion', 'vigente',
    ]);

    $almacenes = (array) ($request->almacen ?: ['01', '02', '03', '14', '15']);
    $almacenesRaw = collect($almacenes)->map(fn($a) => "'$a'")->implode(', ');

    $stockFiltrado = \App\Models\Sql\Stock::whereRaw(
        "CAST(cod_almacen AS VARCHAR) IN ($almacenesRaw)"
    )->get()->groupBy(fn($s) => "{$s->cod_articulo}-{$s->cod_color_articulo}");

    Cache::put('stock_filtrado_' . auth()->id(), $stockFiltrado, 300); // 5 minutos

    $almacenesList = Almacen::orderBy('cod_almacen')->pluck('denom_almacen', 'cod_almacen');
    $tiposProducto = TipoProductoStock::pluck('denom_tipo_producto', 'id_tipo_producto_stock');
    $familias = FamiliasProducto::pluck('nombre', 'id');
    $lineas = LineasProducto::pluck('denom_linea', 'cod_linea');
    $formasComercializacion = ColoresPorArticulo::select('comercializacion_libre')
        ->distinct()->orderBy('comercializacion_libre')
        ->pluck('comercializacion_libre', 'comercializacion_libre')->filter();

    if (!$request->filled('aplicar')) {
        return view('produccion.analisis-stock.index', [
            'registros' => collect(),
            'talles' => collect(),
            'almacenes' => $almacenesList,
            'tiposProductoStock' => $tiposProducto,
            'familias' => $familias,
            'lineas' => $lineas,
            'formasComercializacion' => $formasComercializacion,
            'totalesPorTalle' => [],
            'totalGeneral' => 0,
        ]);
    }

    $query = ColoresPorArticulo::with(['articulo.rango', 'articulo.familia', 'articulo.linea'])
        ->when($request->filled('cod_articulo'), fn($q) => $q->where('cod_articulo', 'like', "%{$request->cod_articulo}%"))
        ->when($request->filled('color'), fn($q) => $q->where('cod_color_articulo', 'like', "%{$request->color}%"))
        ->when($request->filled('familia'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->whereIn('cod_familia_producto', (array) $request->familia)))
        ->when($request->filled('linea'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->whereIn('cod_linea', (array) $request->linea)))
        ->when($request->filled('tipo_producto_stock'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->whereIn('id_tipo_producto_stock', (array) $request->tipo_producto_stock)))
        ->when($request->filled('forma_comercializacion'), fn($q) => $q->whereIn('comercializacion_libre', (array) $request->forma_comercializacion))
        ->when($request->filled('vigente'), fn($q) => $q->whereIn('vigente', (array) $request->vigente))
        ->when($request->filled('denom_articulo'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->where('denom_articulo', 'like', "%{$request->denom_articulo}%")));

    // ---------- NUEVO: precálculo global (sin paginar) ----------
   static $stockCache = [];

$registrosFiltrados = (clone $query)->get();

[$totalesPorTalle, $totalGeneral, $tallesOrdenados] = $this->calcularTotalesDelFiltrado(
    $registrosFiltrados, // 1) Collection de ColoresPorArticulo (sin paginar)
    $stockFiltrado,      // 2) Collection agrupada por "articulo-color"
    $almacenes,          // 3) array de almacenes (ids/ códigos)
    $stockCache          // 4) cache local (por referencia)
);

    // ---------- paginado + mapeo ----------
    $paginados = $query->paginate(400)->withQueryString();

    $registros = $paginados->getCollection()->map(function ($item) use (&$stockCache, $stockFiltrado, $almacenes) {
        $rango = $item->articulo?->rango;
        $cantidades = [];
        $total = 0;

        if ($rango) {
            for ($i = 1; $i <= 10; $i++) {
                $talle = $rango->{'posic_' . $i} ?? null;
                if (!$talle) continue;

                $cantidad = $this->obtenerCantidadConCache($stockCache, $item->cod_articulo, $item->cod_color_articulo, $i, $almacenes, $stockFiltrado);
                $cantidades[$talle] = $cantidad;
                $total += $cantidad;
            }
        }

        return (object)[
            'cod_articulo' => $item->cod_articulo,
            'cod_color_articulo' => $item->cod_color_articulo,
            'denom_articulo' => $item->articulo->denom_articulo ?? '—',
            'cantidades' => $cantidades,
            'total' => $total,
        ];
    });

    $sort = request('sort');
    $dir = strtolower(request('dir')) === 'desc' ? SORT_DESC : SORT_ASC;

    if (in_array($sort, ['cod_articulo', 'cod_color_articulo', 'denom_articulo', 'total'])) {
        $sortFlags = $sort === 'total' ? SORT_NUMERIC : SORT_NATURAL;
        $registros = $registros->sortBy([[$sort, $sortFlags | $dir]])->values();
    } else {
        $registros = $registros->sortBy([
            ['cod_articulo', SORT_NATURAL],
            ['cod_color_articulo', SORT_NATURAL],
            ['denom_articulo', SORT_NATURAL],
            ['total', SORT_NUMERIC],
        ])->values();
    }

    // ---------- NUEVO: talles sobre TODO el filtrado ----------
    $listaTalles = $tallesOrdenados;

    return view('produccion.analisis-stock.index', [
        'registros' => new \Illuminate\Pagination\LengthAwarePaginator(
            $registros,
            $paginados->total(),
            $paginados->perPage(),
            $paginados->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        ),
        'talles' => $listaTalles,
        'almacenes' => $almacenesList,
        'tiposProductoStock' => $tiposProducto,
        'familias' => $familias,
        'lineas' => $lineas,
        'formasComercializacion' => $formasComercializacion,
        'totalesPorTalle' => $totalesPorTalle,
        'totalGeneral' => $totalGeneral,
    ]);
}



private function prepararDatosAnaliticos(Request $request): array
{
    $almacenes = explode(',', $request->input('almacenes', '1,2'));
    $almacenesRaw = collect($almacenes)->map(fn($a) => "'$a'")->implode(', ');

    // Recuperar stock agrupado desde cache persistente
    $stockFiltrado = Cache::get('stock_filtrado_' . auth()->id());

    if (!$stockFiltrado) {
        $stock = \App\Models\Sql\Stock::whereRaw("CAST(cod_almacen AS VARCHAR) IN ($almacenesRaw)")->get();
        $stockFiltrado = $stock->groupBy(fn($r) => $r->cod_articulo . '-' . $r->cod_color_articulo);
        Cache::put('stock_filtrado_' . auth()->id(), $stockFiltrado, 300); // TTL 5 min
    }

    // Filtros aplicados al query
    $query = \App\Models\ColoresPorArticulo::with([
        'articulo.rango',
        'articulo.familia',
        'articulo.linea',
        'tipo_producto_stock'
    ])
        ->when($request->filled('cod_articulo'), fn($q) => $q->where('cod_articulo', 'like', "%{$request->cod_articulo}%"))
        ->when($request->filled('color'), fn($q) => $q->where('cod_color_articulo', 'like', "%{$request->color}%"))
        ->when($request->filled('familia'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->whereIn('cod_familia_producto', (array) $request->familia)))
        ->when($request->filled('linea'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->whereIn('cod_linea', (array) $request->linea)))
        ->when($request->filled('tipo_producto_stock'), fn($q) => $q->whereIn('id_tipo_producto_stock', (array) $request->tipo_producto_stock))
        ->when($request->filled('forma_comercializacion'), fn($q) => $q->whereIn('comercializacion_libre', (array) $request->forma_comercializacion))
        ->when($request->filled('vigente'), fn($q) => $q->whereIn('vigente', (array) $request->vigente))
        ->when($request->filled('denom_articulo'), fn($q) => $q->whereHas('articulo', fn($q2) => $q2->where('denom_articulo', 'like', "%{$request->denom_articulo}%")));

    $stockCache = [];

    $registros = $query->get()->map(function ($registro) use (&$stockCache, $stockFiltrado, $almacenes) {
        $articulo = $registro->articulo;
        $rango = $articulo?->rango;
        $talles = [];
        $total = 0;

        for ($i = 1; $i <= 10; $i++) {
            $talleReal = $rango?->{"posic_$i"};
            if (!$talleReal) continue;

            $key = "{$registro->cod_articulo}-{$registro->cod_color_articulo}-$i-" . implode('-', $almacenes);
            $cantidad = \App\Helpers\MemCacheHelper::getOrCompute($stockCache, $key, function () use ($stockFiltrado, $registro, $i) {
                $stockKey = "{$registro->cod_articulo}-{$registro->cod_color_articulo}";
                $stockGroup = $stockFiltrado[$stockKey] ?? collect();
                return $stockGroup->sum("cant_$i");
            });

            $talles["talle_{$talleReal}"] = $cantidad;
            $total += $cantidad;
        }

        return array_merge($registro->toArray(), $talles, [
            'denom_articulo' => $articulo->denom_articulo ?? '—',
            'familia' => $articulo->familia->nombre ?? '—',
            'linea' => $articulo->linea->denom_linea ?? '—',
            'tipo_producto_stock' => $registro->tipo_producto_stock->denom_tipo_producto ?? '—',
            'forma_comercializacion' => $registro->comercializacion_libre ?? '—',
            'total' => $total,
        ]);
    });

    $registros = collect($registros)->sortBy([
        ['cod_articulo', SORT_NATURAL],
        ['cod_color_articulo', SORT_NATURAL],
        ['denom_articulo', SORT_NATURAL],
        ['total', SORT_NUMERIC],
    ])->values();

    return [
        'registros' => $registros,
        'almacenes' => $almacenes,
    ];
}


public function exportarExcel(Request $request)
{
    $datos = $this->prepararDatosAnaliticos($request);

   return Excel::download(new StockExport($datos['registros'], $request), 'analisis_stock.xlsx');

}

private function obtenerCantidadConCache(array &$stockCache, string $codArticulo, string $codColor, int $pos, array $almacenes, $stockFiltrado): int
{
    $cacheKey = "{$codArticulo}-{$codColor}-{$pos}-" . implode('-', $almacenes);

    if (!isset($stockCache[$cacheKey])) {
        $stockKey = "{$codArticulo}-{$codColor}";
        $stockGroup = $stockFiltrado[$stockKey] ?? collect();
        $cantidad = $stockGroup->sum("cant_$pos");
        $stockCache[$cacheKey] = $cantidad;
    }

    return $stockCache[$cacheKey];
}
/**
 * Calcula totales reales del filtrado (sin paginación)
 *
 * @param Collection $todoFiltrado   Colección Eloquent completa (clone->get())
 * @param array      $almacenes      Lista de almacenes filtrados (['01','02',...])
 * @param \Illuminate\Support\Collection $stockFiltrado  GroupBy articulo-color del SQL Server
 * @param array      $stockCache     Cache local reutilizable (por referencia)
 * @return array [array $totalesPorTalle, int $totalGeneral]
 */
private function calcularTotalesDelFiltrado(
    Collection $todoFiltrado,
    Collection $stockFiltrado,
    array $almacenesIds,
    array &$stockCache = []
): array {
    $totalesPorTalle = [];
    $totalGeneral    = 0;
    $tallesSet       = collect();

    foreach ($todoFiltrado as $item) {
        $rango = $item->articulo?->rango;
        if (!$rango) continue;

        for ($i = 1; $i <= 10; $i++) {
            $talle = $rango->{'posic_' . $i} ?? null;
            if (!$talle) continue;

            $tallesSet->push($talle);

            $cantidad = $this->obtenerCantidadConCache(
                $stockCache,
                $item->cod_articulo,
                $item->cod_color_articulo,
                $i,
                $almacenesIds,
                $stockFiltrado
            );

            $totalesPorTalle[$talle] = ($totalesPorTalle[$talle] ?? 0) + (int) $cantidad;
            $totalGeneral += (int) $cantidad;
        }
    }

    $tallesOrdenados = $tallesSet->unique()->sort()->values();

    return [$totalesPorTalle, $totalGeneral, $tallesOrdenados];
}

}
