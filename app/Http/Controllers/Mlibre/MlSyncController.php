<?php
namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Mlibre\MlSyncService;
use App\Services\Mlibre\SyncPriceService;
use App\Models\MlVariante;
use Illuminate\Support\Facades\DB;

class MlSyncController extends Controller
{

    use App\Services\Mlibre\SyncPriceService;

public function sincronizarSeleccionados(Request $request)
{
    $ids = $request->input('ids', []);
    if (empty($ids)) {
        return back()->with('error', 'No seleccionaste ninguna variante.');
    }

    $ok = 0;
    $errors = 0;

    foreach ($ids as $id) {
        $variante = MlVariante::with('skuVariante')->find($id);
        if (!$variante || !$variante->skuVariante) {
            $errors++;
            continue;
        }

        $servicio = new SyncPriceService();
        $resultado = $servicio->sincronizar($variante);

        if ($resultado['success']) {
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
    $query = MlVariante::with('skuVariante');

    $campos = ['ml_id', 'variation_id', 'product_number', 'seller_custom_field',
               'color', 'talle', 'modelo', 'titulo', 'seller_sku',
               'sync_status', 'status', 'family_id', 'logistic_type'];

    foreach ($campos as $campo) {
        if ($request->filled($campo)) {
            $valores = (array) $request->input($campo);
            if ($campo === 'status') {
                $query->whereHas('publicacion', function ($q) use ($valores) {
                    $q->whereIn('status', $valores);
                });
            } elseif ($campo === 'logistic_type') {
                $query->whereHas('publicacion', function ($q) use ($valores) {
                    $q->whereIn('logistic_type', $valores);
                });
            } else {
                $query->whereIn($campo, $valores);
            }
        }
    }

    $variantes = $query->get();

    $ok = 0;
    $errors = 0;

    foreach ($variantes as $variante) {
        $servicio = new SyncPriceService();
        $resultado = $servicio->sincronizar($variante);

        if ($resultado['success']) {
            $ok++;
        } else {
            $errors++;
        }
    }

    return back()->with('sync_result', [
        'ok' => $ok,
        'errors' => $errors,
        'total' => $variantes->count(),
    ]);
}

    public function syncFiltrados(Request $request)
    {
        $campos = $request->input('campos', ['stock']); // ['stock'], ['precio'] o ['stock', 'precio']
        $query = \App\Models\MlVariante::query()->with('skuVariante');

        // Reaplicar filtros del request
        foreach ($request->except(['_token', 'campos']) as $campo => $valores) {
            $valores = (array) $valores;

            // Relaciones con ml_publicaciones
            if (in_array($campo, ['status', 'logistic_type'])) {
                $query->whereHas('publicacion', function ($q) use ($campo, $valores) {
                    $q->whereIn($campo, $valores);
                });
            } else {
                $query->whereIn($campo, $valores);
            }
        }

        $variantesFiltradas = $query->pluck('id')->toArray();
        $resultados = [];

        foreach ($campos as $campo) {
            $resultados[$campo] = app(MlSyncService::class)
                ->setModo('seleccionados')
                ->setCampo($campo)
                ->setIds($variantesFiltradas)
                ->sync();
        }

        return back()->with('sync_result', $resultados);
    }

    public function syncSeleccionados(Request $request)
    {
        $campos = $request->input('campos', ['stock']);
        $ids = $request->input('ids', []);
        $resultados = [];

        foreach ($campos as $campo) {
            $resultados[$campo] = app(MlSyncService::class)
                ->setModo('seleccionados')
                ->setCampo($campo)
                ->setIds($ids)
                ->sync();
        }

        return back()->with('sync_result', $resultados);
    }

    public function syncPendientes(Request $request)
    {
        $campos = $request->input('campos', ['stock']);
        $resultados = [];

        foreach ($campos as $campo) {
            $resultados[$campo] = app(MlSyncService::class)
                ->setModo('pendientes')
                ->setCampo($campo)
                ->sync();
        }

        return back()->with('sync_result', $resultados);
    }
}
