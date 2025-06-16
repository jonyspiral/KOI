<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MlVariante;

class MlibreActualizarStockLote extends Command
{
    protected $signature = 'mlibre:actualizar-stock-lote';
    protected $description = 'Sincroniza en lote el stock de Mercado Libre desde ml_variantes (con y sin variantes)';

   public function handle(): int

    {
        $this->info("🔄 Iniciando sincronización masiva de stock hacia Mercado Libre...");

        $variantes = MlVariante::whereIn('sync_status', ['N', 'U'])
            ->whereNotNull('ml_id')
            ->whereNotNull('product_number')
            ->get();

        if ($variantes->isEmpty()) {
            $this->warn("⚠️ No hay variantes pendientes para sincronizar.");
            return 0;
        }

        $total = $variantes->count();
        $ok = 0;
        $errores = 0;
        $omitidos = 0;

        foreach ($variantes as $v) {
            $resultado = $v->actualizarStockML();

            if ($v->sync_status === 'S') {
                $ok++;
            } elseif ($v->sync_status === 'E') {
                $errores++;
            } else {
                $omitidos++;
            }
        }

        $this->info("🎯 Resultado final:");
        $this->info("✅ Éxito: $ok | ❌ Errores: $errores | 🚫 Omitidos/FULL: $omitidos | 🔢 Total: $total");

        return 0;
    }
}
