<?php

namespace App\Http\Controllers\Produccion;


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

    static $stockCache = [];

    $registrosFiltrados = (clone $query)->get();
    $totalesPorTalle = [];
    $totalGeneral = 0;

    foreach ($registrosFiltrados as $item) {
        $rango = $item->articulo?->rango;
        if (!$rango) continue;

        for ($i = 1; $i <= 10; $i++) {
            $talle = $rango->{'posic_' . $i} ?? null;
            if (!$talle) continue;

            $cantidad = $this->obtenerCantidadConCache($stockCache, $item->cod_articulo, $item->cod_color_articulo, $i, $almacenes, $stockFiltrado);
            $totalesPorTalle[$talle] = ($totalesPorTalle[$talle] ?? 0) + $cantidad;
            $totalGeneral += $cantidad;
        }
    }

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

    $listaTalles = $paginados->getCollection()
        ->flatMap(function ($item) {
            $rango = $item->articulo?->rango;
            return collect(range(1, 10))
                ->map(fn($i) => $rango->{'posic_' . $i} ?? null)
                ->filter();
        })
        ->unique()
        ->sort()
        ->values();

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



public function exportarExcel(Request $request)
{
    $service = new StockExportService();
    return $service->export($request);
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


}
