<?php

namespace App\Http\Controllers\Mlibre;

use App\Traits\PersisteFiltrosTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MlVariante;
use App\Services\Mlibre\MlibreTokenService;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MlVariantesExport;

class MlibreVariantesController extends Controller
{

   use PersisteFiltrosTrait;

    public function index(Request $request)
    {
        // 🎯 Campos de filtro válidos
        $camposFiltrables = [
            'ml_id', 'variation_id', 'product_number', 'seller_custom_field',
            'color', 'talle', 'modelo', 'titulo', 'seller_sku',
            'sync_status', 'status', 'family_id',
            'sort', 'dir', 'page'
        ];

      $requestFiltrado = $this->manejarFiltros($request, 'ml_variantes_filtros', $camposFiltrables);

// Si manejarFiltros devolvió un redirect, lo devolvemos directamente:
if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) {
    return $requestFiltrado;
}

// Sino, usamos el Request filtrado
$request = $requestFiltrado;

        // 🧱 Armar query base
       
$query = MlVariante::query();

foreach ($camposFiltrables as $campo) {
    if (!in_array($campo, ['sort', 'dir', 'page']) && $request->filled($campo)) {
        $valores = collect($request->$campo);

        if ($valores->isNotEmpty()) {
            if (is_array($request->$campo)) {
                // ✅ Soporta valores normales + "__NULL__"
                $normales = $valores->filter(fn($v) => $v !== '__NULL__');
                $incluyeNull = $valores->contains('__NULL__');

                $query->where(function ($q) use ($campo, $normales, $incluyeNull) {
                    if ($normales->isNotEmpty()) {
                        $q->whereIn($campo, $normales);
                    }
                    if ($incluyeNull) {
                        $q->orWhereNull($campo);
                    }
                });
            } else {
                // Para inputs de texto
                $query->where($campo, 'like', '%' . $request->$campo . '%');
            }
        }
    }
}

// 📊 Ordenamiento
$sort = $request->get('sort', 'ml_id');
$dir  = $request->get('dir', 'asc');
if (\Schema::hasColumn('ml_variantes', $sort)) {
    $query->orderBy($sort, $dir);
}

// 📄 Paginación
$variantes = $query->paginate(50)->appends($request->query());

// 🔄 Filtros únicos para Select2s (sin __NULL__ aquí)
$filtros = [];
foreach ($camposFiltrables as $campo) {
    if (!in_array($campo, ['sort', 'dir', 'page']) && \Schema::hasColumn('ml_variantes', $campo)) {
        $filtros[$campo] = MlVariante::select($campo)
            ->distinct()
            ->whereNotNull($campo)
            ->pluck($campo)
            ->sort()
            ->values();
    }
}

return view('mlibre.variantes.index', compact('variantes', 'filtros', 'sort', 'dir'));
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
            $v = MlVariante::find($id);
            if (!$v) {
                $errors++;
                continue;
            }

            $modificado = false;

            if (isset($scfs[$id]) && $scfs[$id] !== $v->seller_custom_field) {
                $v->seller_custom_field = $scfs[$id];
                $modificado = true;
            }

           if (!$v->manual_stock && $v->skuVariante) {
            $stockNuevo = $v->skuVariante->stock;
            } else {
                $stockNuevo = $stocks[$id] ?? $v->stock;
            }

            if ($stockNuevo != $v->stock) {
                $v->stock = $stockNuevo;
                $modificado = true;
            }

            

            if (isset($precios[$id]) && $precios[$id] != $v->precio) {
                $v->precio = $precios[$id];
                $modificado = true;
            }

            $v->manual_stock = isset($overridesStock[$id]) ? 1 : 0;
            $v->manual_price = isset($overridesPrecio[$id]) ? 1 : 0;

            if ($modificado) {
                $v->sync_status = 'U';
                $v->save();
            }

            if (!$v->ml_id) {
                $v->markAsError('Falta ml_id');
                $errors++;
                continue;
            }

            $itemRes = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$v->ml_id}?attributes=variations");

            if (!$itemRes->ok()) {
                $v->markAsError("Error al obtener item ML ({$itemRes->status()})");
                $errors++;
                continue;
            }

            $item = $itemRes->json();
            $hasVariants = !empty($item['variations']);

            try {
                if ($hasVariants) {
                    if (!$v->variation_id) {
                        $v->markAsError('Falta variation_id');
                        $errors++;
                        continue;
                    }

                    $variacion = collect($item['variations'])->firstWhere('id', $v->variation_id);

                    if (!$variacion) {
                        $v->markAsError('Variación no encontrada en ML');
                        $errors++;
                        continue;
                    }

                    if (!empty($variacion['inventory_id'])) {
                        $v->markAsError('Stock FULL (no editable)');
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
                    $v->markAsSynced();
                    $ok++;
                } else {
                    $v->markAsError("Error PUT: " . $put->status() . " - " . $put->body());
                    $errors++;
                }
            } catch (\Exception $e) {
                $v->markAsError("Excepción: " . $e->getMessage());
                $errors++;
            }
        }

        return back()->with('sync_result', [
            'ok' => $ok,
            'errors' => $errors,
            'total' => count($ids),
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
