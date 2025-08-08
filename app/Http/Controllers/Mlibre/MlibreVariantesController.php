<?php

namespace App\Http\Controllers\Mlibre;

use App\Traits\PersisteFiltrosTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MlVariante;
use App\Models\SkuVariante;
use App\Services\Mlibre\MlibreTokenService;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MlVariantesExport;
use Illuminate\Support\Facades\Schema;
use App\Models\MlPublicacion;
use App\Services\StockService;
use App\Helpers\MemCacheHelper; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Sql\Stock;
use App\Models\RangoTalle;


class MlibreVariantesController extends Controller
{

   use PersisteFiltrosTrait;

   

public function index(Request $request)
{
    $camposFiltrables = [
        'ml_id', 'variation_id', 'product_number', 'seller_custom_field',
        'color', 'talle', 'modelo', 'titulo', 'seller_sku',
        'sync_status', 'status', 'family_id', 'logistic_type', 'has_campaign'
    ];

    // Aplicar filtros persistentes
    $requestFiltrado = $this->manejarFiltros($request, 'ml_variantes_filtros', $camposFiltrables);
    if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) {
        return $requestFiltrado;
    }
    $request = $requestFiltrado;

    // Cargar variantes con relaciones necesarias
    $query = MlVariante::with(['publicacion', 'skuVariante']);

foreach ($camposFiltrables as $campo) {
    if ($request->filled($campo)) {
        $valores = (array) $request->input($campo);

        if (in_array($campo, ['status', 'logistic_type', 'family_id', 'has_campaign'])) {
            $query->whereHas('publicacion', function ($q) use ($campo, $valores) {
                $q->whereIn($campo, $valores);
            });
        } else {
            $query->whereIn($campo, $valores);
        }
    }
}

$variantes = $query->get();


    // Si hay filtro de diferencia de stock activado
    if ($request->filled('diferencia_stock')) {
        $variantes = $variantes->filter(function ($v) {
            $sku = $v->skuVariante;
            $stockReal = $sku?->stock;
            $stockML = $v->stock;

            if (!is_numeric($stockReal) || !is_numeric($stockML)) return false;

            if ((int) $stockReal !== (int) $stockML) {
                \Log::info("🔍 Comparando stock", [
                    'id' => $v->id,
                    'scf' => $v->seller_custom_field,
                    'stock_real' => $stockReal,
                    'stock_ml' => $stockML,
                ]);
                return true;
            }

            return false;
        });

        \Log::info("📊 Total variantes con diferencia real de stock: " . $variantes->count());
    }

    // Aplicar orden si existe
    $sort = $request->get('sort');
    $dir  = $request->get('dir', 'asc');
    if ($sort && $variantes->isNotEmpty() && isset($variantes->first()[$sort])) {
        $variantes = $dir === 'desc'
            ? $variantes->sortByDesc($sort)
            : $variantes->sortBy($sort);
    }

    // Simular paginación
    $perPage = 100;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $total = $variantes->count();
    $items = $variantes->slice(($currentPage - 1) * $perPage, $perPage)->values();
    $paginado = new LengthAwarePaginator($items, $total, $perPage, $currentPage);
    $paginado->setPath(route('mlibre.variantes.index'));
    $paginado->appends($request->except('page'));

    // Armar filtros para select2
    $filtros = [];
  foreach ($camposFiltrables as $campo) {
    if (in_array($campo, ['status', 'logistic_type', 'family_id'])) {
        $filtros[$campo] = \App\Models\MlPublicacion::distinct()
            ->whereNotNull($campo)
            ->pluck($campo)
            ->filter()
            ->unique()
            ->values()
            ->all();
    } elseif (\Schema::hasColumn('ml_variantes', $campo)) {
        $filtros[$campo] = MlVariante::distinct()
            ->whereNotNull($campo)
            ->pluck($campo)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}


    return view('mlibre.variantes.index', [
        'variantes' => $paginado,
        'filtros' => $filtros,
    ]);
}




    public function guardar(Request $request)
    {
        foreach ($request->input('variantes', []) as $id => $datos) {
            $v = MlVariante::find($id);
            if (!$v) continue;

            $v->seller_custom_field = $datos['seller_custom_field'] ?? $v->seller_custom_field;
            $v->precio = $datos['precio'] ?? $v->precio;
            $v->stock = $datos['stock'] ?? $v->stock;

            $v->manual_price = isset($datos['manual_price']) ? 1 : 0;
            $v->manual_stock = isset($datos['manual_stock']) ? 1 : 0;

            $v->sync_status = 'U';
            $v->save();
        }

        return back()->with('success', '✅ Variantes actualizadas correctamente');
    }

    public function exportar(Request $request)
    {
        return Excel::download(new MlVariantesExport($request), 'variantes-ml.xlsx');
    }

    public function sincronizarSeleccionados(Request $request)
{
    $ids     = $request->input('ids', []);
    $scfs    = $request->input('scf', []);
    $stocks  = $request->input('stock', []);
    $precios = $request->input('precio', []);
    $overridesStock = $request->input('manual_stock', []);
    $overridesPrecio = $request->input('manual_price', []);

    if (empty($ids)) {
        return back()->with('error', 'No seleccionaste ninguna variante.');
    }

    $token = app(MlibreTokenService::class)->getValidAccessToken();
    $ok = 0;
    $errors = 0;

    foreach ($ids as $id) {
        $v = MlVariante::with('skuVariante')->find($id);
        if (!$v) {
            $errors++;
            continue;
        }

        $modificado = false;

        // ✅ SCF
        $scfFinal = $scfs[$id] ?? $v->seller_custom_field;
        if ($scfFinal !== $v->seller_custom_field && !empty($scfFinal)) {
            $v->seller_custom_field = $scfFinal;
            $modificado = true;
        }

        // ✅ Overrides manuales
        $v->manual_stock  = isset($overridesStock[$id]) ? 1 : 0;
        $v->manual_price  = isset($overridesPrecio[$id]) ? 1 : 0;

        // ✅ STOCK
        $stockNuevo = $v->manual_stock ? ($stocks[$id] ?? $v->stock) : optional($v->skuVariante)->stock;
        if ($stockNuevo != $v->stock) {
            $v->stock = $stockNuevo;
            $modificado = true;
        }

        // ✅ PRECIO
        $precioNuevo = $precios[$id] ?? null;
        if (isset($precioNuevo) && $precioNuevo != $v->precio) {
            $v->precio = $precioNuevo;
            $modificado = true;
        }

        if ($modificado) {
            $v->sync_status = 'U';
            $v->sync_log = '🟡 Modificado local (pendiente sync)';
        }

        $v->save();

        // Validaciones previas al PUT
        if (empty($v->seller_custom_field)) {
            $v->sync_status = 'E';
            $v->sync_log = '❌ SCF vacío o nulo';
            $v->save();
            $errors++;
            continue;
        }

        if (!$v->skuVariante) {
            $v->sync_status = 'E';
            $v->sync_log = '❌ SKU Variante no encontrado';
            $v->save();
            $errors++;
            continue;
        }

        if (!$v->ml_id) {
            $v->sync_status = 'E';
            $v->sync_log = '❌ Falta ml_id';
            $v->save();
            $errors++;
            continue;
        }

        $itemRes = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$v->ml_id}?attributes=variations");

        if (!$itemRes->ok()) {
            $v->sync_status = 'E';
            $v->sync_log = "❌ Error al obtener item ML ({$itemRes->status()})";
            $v->save();
            $errors++;
            continue;
        }

        $item = $itemRes->json();
        $hasVariants = !empty($item['variations']);

        try {
            if ($hasVariants) {
                if (!$v->variation_id) {
                    $v->sync_status = 'E';
                    $v->sync_log = '❌ Falta variation_id';
                    $v->save();
                    $errors++;
                    continue;
                }

                $variacion = collect($item['variations'])->firstWhere('id', $v->variation_id);
                if (!$variacion) {
                    $v->sync_status = 'E';
                    $v->sync_log = '❌ Variación no encontrada en ML';
                    $v->save();
                    $errors++;
                    continue;
                }

                if (!empty($variacion['inventory_id'])) {
                    $v->sync_status = 'E';
                    $v->sync_log = '❌ Stock FULL (no editable)';
                    $v->save();
                    $errors++;
                    continue;
                }

                $put = Http::withToken($token)->put(
                    "https://api.mercadolibre.com/items/{$v->ml_id}/variations/{$v->variation_id}",
                    ['available_quantity' => (int) $v->stock]
                );
            } else {
                $put = Http::withToken($token)->put(
                    "https://api.mercadolibre.com/items/{$v->ml_id}",
                    ['available_quantity' => (int) $v->stock]
                );
            }

            if ($put->ok()) {
                $v->sync_status = 'S';
                $v->sync_log = '✅ Sincronizado OK';
                $v->save();
                $ok++;
            } else {
                $v->sync_status = 'E';
                $v->sync_log = "❌ Error PUT: " . $put->status() . " - " . $put->body();
                $v->save();
                $errors++;
            }
        } catch (\Exception $e) {
            $v->sync_status = 'E';
            $v->sync_log = "❌ Excepción: " . $e->getMessage();
            $v->save();
            $errors++;
        }
    }

    return back()->with('sync_result', [
        'ok' => $ok,
        'errors' => $errors,
        'total' => count($ids),
    ]);
}



public function sincronizarFiltrados(Request $request)
    {
        $camposFiltrables = [
            'ml_id', 'variation_id', 'product_number', 'seller_custom_field',
            'color', 'talle', 'modelo', 'titulo', 'seller_sku',
            'sync_status', 'status', 'family_id', 'logistic_type'
        ];

        $query = MlVariante::query()->with('skuVariante');

        foreach ($camposFiltrables as $campo) {
            if ($request->filled($campo)) {
                $valores = (array) $request->input($campo);

                if ($campo === 'status') {
                    $query->whereHas('publicacion', function ($q) use ($valores) {
                        $q->whereIn('status', $valores);
                    });
                } else {
                    $query->whereIn($campo, $valores);
                }
            }
        }

        $variantes = $query->get();
        $token = app(MlibreTokenService::class)->getValidAccessToken();

        $ok = 0;
        $errors = 0;

        foreach ($variantes as $v) {
            try {
                $v->syncFromSku();
                $result = $v->sincronizarVariante($token);
                $result ? $ok++ : $errors++;
            } catch (\Exception $e) {
                $v->sync_status = 'E';
                $v->sync_log = "❌ Excepción: " . $e->getMessage();
                $v->save();
                $errors++;
            }
        }

        return back()->with('sync_result', [
            'ok' => $ok,
            'errors' => $errors,
            'total' => $variantes->count(),
        ]);
    }


public function syncFromSku(): bool
{
    if (!$this->skuVariante) {
        $this->sync_status = 'E';
        $this->sync_log = 'SKU no encontrado en view_sku_variantes';
        return false;
    }

    $updated = false;
    $log = [];

    // PRECIO
    if ($this->manual_price) {
        $log[] = 'Precio override manual';
    } elseif ($this->precio != $this->skuVariante->precio) {
        $this->precio = $this->skuVariante->precio;
        $updated = true;
        $log[] = 'Precio actualizado desde SKU';
    }

    // STOCK
    if ($this->manual_stock) {
        $log[] = 'Stock override manual';
    } elseif ($this->stock != $this->skuVariante->stock) {
        $this->stock = $this->skuVariante->stock;
        $updated = true;
        $log[] = 'Stock actualizado desde SKU';
    }

    if ($updated) {
        $this->sync_status = 'U';
    } else {
        $this->sync_status = 'S';
    }

    $this->sync_log = implode('; ', $log) ?: 'Sin cambios desde SKU';
    return $updated;
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


}
