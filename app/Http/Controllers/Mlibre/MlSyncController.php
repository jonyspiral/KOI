<?php
namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Mlibre\MlSyncService;
use App\Models\MlVariante;
use App\Models\MlSyncLog;

class MlSyncController extends Controller
{
    public function syncSeleccionados(Request $request)
    {
        $ids = $request->input('ids', []);

        // 1. Guardar SCFs
        foreach ($request->input('scf', []) as $id => $valor) {
            if (!is_null($valor)) {
                MlVariante::where('id', $id)->update(['seller_custom_field' => $valor]);
            }
        }

        // 2. Validación post-guardado: verificar SKU vinculados
        $variantes = MlVariante::with('skuVariante')->whereIn('id', $ids)->get();

        foreach ($variantes as $v) {
            if (!$v->skuVariante) {
                MlSyncLog::create([
                    'ml_variante_id' => $v->id,
                    'campo'          => 'scf',
                    'exito'          => 0,
                    'mensaje'        => '❌ No se encuentra sku_variante para SCF: ' . ($v->seller_custom_field ?? 'NULL'),
                ]);

                $v->sync_status = 'E';
                $v->sync_log = '❌ Sin SKU vinculado por SCF';
                $v->save();
            }
        }

        // 3. Ejecutar sincronización real con el Service
        $resultado = app(MlSyncService::class)
            ->setModo('seleccionados')
            ->setCampo('global')
            ->setIds($ids)
            ->sync();

        return back()->with('sync_result', $resultado);
    }

    public function syncFiltrados(Request $request)
    {
        try {
            $resultado = app(MlSyncService::class)
                ->setModo('filtrados')
                ->setCampo('global')
                ->sync();

            return back()->with('sync_result', $resultado);
        } catch (\Throwable $e) {
            \Log::error("Error en syncFiltrados: " . $e->getMessage());

            return back()->with('sync_result', [
                'total' => 0,
                'ok' => 0,
                'errors' => 0,
                'mensaje' => 'Error al sincronizar variantes filtradas.',
            ]);
        }
    }

    public function syncPendientes(Request $request)
    {
        try {
            $resultado = app(MlSyncService::class)
                ->setModo('pendientes')
                ->setCampo('global')
                ->sync();

            return back()->with('sync_result', $resultado);
        } catch (\Throwable $e) {
            \Log::error("Error en syncPendientes: " . $e->getMessage());

            return back()->with('sync_result', [
                'total' => 0,
                'ok' => 0,
                'errors' => 0,
                'mensaje' => 'Error al sincronizar variantes pendientes.',
            ]);
        }
    }
}
