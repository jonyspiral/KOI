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
use Illuminate\Support\Facades\Schema;
use App\Models\MlPublicacion;

class MlibreVariantesController extends Controller
{

   use PersisteFiltrosTrait;

    public function index(Request $request)
{
    $camposFiltrables = [
        'ml_id', 'variation_id', 'product_number', 'seller_custom_field',
        'color', 'talle', 'modelo', 'titulo', 'seller_sku',
        'sync_status', 'status', 'family_id', 'logistic_type',
        'sort', 'dir', 'page'
    ];

    $requestFiltrado = $this->manejarFiltros($request, 'ml_variantes_filtros', $camposFiltrables);

    if ($requestFiltrado instanceof \Illuminate\Http\RedirectResponse) {
        return $requestFiltrado;
    }

    $request = $requestFiltrado;

    $query = MlVariante::query()->with('publicacion', 'skuVariante');

    foreach ($camposFiltrables as $campo) {
        if (!in_array($campo, ['sort', 'dir', 'page']) && $request->filled($campo)) {
            $valores = collect($request->$campo);

            if ($valores->isNotEmpty()) {
                if ($campo === 'status' || in_array($campo, ['family_id', 'logistic_type'])) {
                    $query->whereHas('publicacion', function ($q) use ($campo, $valores) {
                        $normales = $valores->filter(fn($v) => $v !== '__NULL__');
                        $incluyeNull = $valores->contains('__NULL__');

                        $q->where(function ($q2) use ($campo, $normales, $incluyeNull) {
                            if ($normales->isNotEmpty()) {
                                $q2->whereIn($campo, $normales);
                            }
                            if ($incluyeNull) {
                                $q2->orWhereNull($campo);
                            }
                        });
                    });
                } elseif (is_array($request->$campo)) {
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
                    $query->where($campo, 'like', '%' . $request->$campo . '%');
                }
            }
        }
    }

    $sort = $request->get('sort', 'ml_id');
    $dir  = $request->get('dir', 'asc');

    if (Schema::hasColumn('ml_variantes', $sort)) {
        $query->orderBy($sort, $dir);
    }

    $variantes = $query->paginate(50)->appends($request->query());

    $filtros = [];

    foreach ($camposFiltrables as $campo) {
        if (!in_array($campo, ['sort', 'dir', 'page'])) {
            if ($campo === 'status' || in_array($campo, ['family_id', 'logistic_type'])) {
                $filtros[$campo] = MlPublicacion::select($campo)
                    ->distinct()
                    ->whereNotNull($campo)
                    ->pluck($campo)
                    ->filter()
                    ->map(fn($v) => (string) $v)
                    ->unique()
                    ->sort()
                    ->values();
            } elseif (Schema::hasColumn('ml_variantes', $campo)) {
                $filtros[$campo] = MlVariante::select($campo)
                    ->distinct()
                    ->whereNotNull($campo)
                    ->pluck($campo)
                    ->sort()
                    ->values();
            }
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
    $ids = $request->input('ids', []);

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

        // 🔁 Tomar datos actualizados desde SKU
        $v->syncFromSku();

        // 🔄 Sincronizar con ML
        $result = $v->sincronizarVariante($token);

        if ($result) {
            $ok++;
        } else {
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
        $v->syncFromSku();
        $result = $v->sincronizarVariante($token);
        $result ? $ok++ : $errors++;
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
