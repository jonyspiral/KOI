<?php

namespace App\Http\Controllers\Mlibre;
use App\Services\Mlibre\MlibreTokenService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MlVariante;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MlVariantesExport;
use Illuminate\Support\Facades\Http;
use App\Models\SkuVariante;

class MlibreVariantesController extends Controller
{
    public function index(Request $request)
{
    $sort = $request->get('sort', 'ml_id');
    $dir  = $request->get('dir', 'asc');

  $query = MlVariante::query()->with(['publicacion', 'skuVariante']);

    // Campos filtrables
    $campos = ['ml_id', 'variation_id', 'product_number', 'seller_custom_field', 'color', 'talle', 'modelo', 'titulo', 'seller_sku', 'sync_status', 'family_id'];

    foreach ($campos as $campo) {
        $valores = $request->input($campo);
        if (!empty($valores)) {
            $query->whereIn($campo, (array) $valores);
        }
    }

    // Filtro adicional desde ml_publicaciones.status
    if ($request->filled('status')) {
        $query->whereHas('publicacion', function ($q) use ($request) {
            $q->whereIn('status', (array) $request->input('status'));
        });
    }

    // Filtros numéricos
    if ($request->filled('stock_min')) {
        $query->where('stock', '>=', $request->stock_min);
    }

    if ($request->filled('stock_max')) {
        $query->where('stock', '<=', $request->stock_max);
    }

    if ($request->filled('precio_min')) {
        $query->where('precio', '>=', $request->precio_min);
    }

    if ($request->filled('precio_max')) {
        $query->where('precio', '<=', $request->precio_max);
    }

    // Ordenamiento
    $query->orderBy($sort, $dir);

    // Paginación
    $variantes = $query->paginate(50)->appends($request->all());

    // Generar filtros para selects de MlVariante
    $filtros = [];
    foreach ($campos as $campo) {
        $filtros[$campo] = MlVariante::select($campo)
            ->distinct()
            ->whereNotNull($campo)
            ->orderBy($campo)
            ->pluck($campo)
            ->filter()
            ->unique()
            ->values();
    }

    // Filtro para status desde MlPublicacion
    $filtros['status'] = \App\Models\MlPublicacion::select('status')
        ->distinct()
        ->whereNotNull('status')
        ->orderBy('status')
        ->pluck('status')
        ->filter()
        ->unique()
        ->values();

    return view('mlibre.variantes.index', compact('variantes', 'filtros', 'sort', 'dir'));
}


public function guardar(Request $request)
{
    foreach ($request->input('variantes', []) as $id => $datos) {
        $v = MlVariante::find($id);
        if (!$v) continue;

        $v->seller_custom_field = $datos['seller_custom_field'] ?? $v->seller_custom_field;
        $v->stock = $datos['stock'] ?? $v->stock;
        $v->precio = $datos['precio'] ?? $v->precio;
        $v->sync_status = 'U';
        $v->save();
    }

    return back()->with('success', '✅ Variantes actualizadas correctamente');
}

    public function exportar(Request $request)
    {
        return Excel::download(new MlVariantesExport($request), 'variantes-ml.xlsx');
    }


public function syncIndividual($id)
{
    $variante = MlVariante::with('skuVariante')->findOrFail($id);

    if ($variante->syncFromSku()) {
        $variante->save();
        $this->info("✅ Variante ID {$id} sincronizada con SKU.");
    }

    try {
        $variante->actualizarStockML();
        $variante->markAsSynced();
        return back()->with('success', "Stock actualizado para variante ID {$id}");
    } catch (\Exception $e) {
        $variante->markAsError($e->getMessage());
        return back()->with('error', "Error al sincronizar: " . $e->getMessage());
    }
}
public function syncPublicacion($ml_id)
{
    $variantes = MlVariante::where('ml_id', $ml_id)->get();

    if ($variantes->isEmpty()) {
        return back()->with('warning', "No se encontraron variantes para la publicación $ml_id");
    }

    $procesadas = 0;
    $errores = 0;

    foreach ($variantes as $variante) {
        $variante->syncFromSku();
        $variante->save();

        try {
            $variante->actualizarStockML();
            $variante->markAsSynced();
            $procesadas++;
        } catch (\Exception $e) {
            $variante->markAsError($e->getMessage());
            $errores++;
        }
    }

    return back()->with('info', "🔄 Sincronización completada: $procesadas OK, $errores errores.");
}
public function sincronizarSeleccionados(Request $request)
{
    $ids     = $request->input('ids', []);
    $scfs    = $request->input('scf', []);
    $stocks  = $request->input('stock', []);
    $precios = $request->input('precio', []);

    if (empty($ids)) {
        return back()->with('error', 'No seleccionaste ninguna variante.');
    }

    $token = app(MlibreTokenService::class)->getValidAccessToken();
    $ok = 0;
    $errors = 0;

    foreach ($ids as $id) {
        $v = MlVariante::find($id);
        if (!$v) {
            $errors++;
            continue;
        }

        // 🔧 GUARDAR CAMBIOS (SCF, stock, precio)
        $modificado = false;

        if (isset($scfs[$id]) && $scfs[$id] !== $v->seller_custom_field) {
            $v->seller_custom_field = $scfs[$id];
            $modificado = true;
        }

        if (isset($stocks[$id]) && $stocks[$id] != $v->stock) {
            $v->stock = $stocks[$id];
            $modificado = true;
        }

        if (isset($precios[$id]) && $precios[$id] != $v->precio) {
            $v->precio = $precios[$id];
            $modificado = true;
        }

        if ($modificado) {
            $v->sync_status = 'U';
            $v->save();
        }

        // 🔁 SYNC CON MERCADO LIBRE
        if (!$v->ml_id || !$v->variation_id || !$v->product_number) {
            $v->markAsError('Datos incompletos para sincronizar');
            $errors++;
            continue;
        }

        $itemRes = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$v->ml_id}?attributes=variations");

        if (!$itemRes->ok()) {
            $v->markAsError("Error al obtener item ML ({$itemRes->status()})");
            $errors++;
            continue;
        }

        $variacion = collect($itemRes->json()['variations'] ?? [])->firstWhere('id', $v->variation_id);

        if (!$variacion || !empty($variacion['inventory_id'])) {
            $v->markAsError('Variación FULL o no encontrada');
            $errors++;
            continue;
        }

        $put = Http::withToken($token)->put(
            "https://api.mercadolibre.com/items/{$v->ml_id}/variations/{$v->variation_id}",
            ['available_quantity' => (int) $v->stock]
        );

        if ($put->ok()) {
            $v->markAsSynced();
            $ok++;
        } else {
            $v->markAsError('Error PUT: ' . $put->status());
            $errors++;
        }
    }

    return back()->with('sync_result', [
        'ok' => $ok,
        'errors' => $errors,
        'total' => count($ids),
    ]);
}


}
