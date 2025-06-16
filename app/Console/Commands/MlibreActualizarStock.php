<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MlVariante;
use App\Services\Mlibre\MlibreTokenService;

class MlibreActualizarStock extends Command
{
    protected $signature = 'mlibre:actualizar-stock {--ml_id=}';
    protected $description = 'Envía el stock de ml_variantes a Mercado Libre (excepto publicaciones FULL)';

    public function handle()
    {
        $ml_id = $this->option('ml_id');
        $query = MlVariante::query()->whereNotNull('product_number');

        if ($ml_id) {
            $query->where('ml_id', $ml_id);
            $this->info("📦 Filtrando variantes por ml_id: $ml_id");
        }

        $variantes = $query->get();

        if ($variantes->isEmpty()) {
            $this->warn('⚠️ No se encontraron variantes para procesar.');
            return 0;
        }

        $this->info("🔄 Procesando {$variantes->count()} variantes...");

        foreach ($variantes as $v) {
            $resultado = $v->actualizarStockML();

            if ($resultado) {
                $this->info("✅ ML {$v->ml_id} / V{$v->variation_id} actualizado.");
            } else {
                $this->error("❌ ML {$v->ml_id} / V{$v->variation_id} falló: {$v->sync_log}");
            }
        }

        $this->info('🎯 Sincronización puntual de stock finalizada.');
        return 0;
    }
}
