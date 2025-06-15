<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MlVariante;

class SyncMlVariantes extends Command
{
    protected $signature = 'mlibre:sync-variantes';
    protected $description = 'Sincroniza stock y precio de las variantes de ML desde SKU (view_sku_variantes)';

    public function handle(): void
{
    $this->info("🔄 Iniciando sincronización de variantes ML...");

    $variantes = MlVariante::with('skuVariante')->get();
    $actualizadas = 0;
    $sinCambios = 0;
    $errores = 0;

    foreach ($variantes as $v) {
        if (!$v->seller_custom_field || strlen($v->seller_custom_field) < 7) {
            $v->sync_status = 'E';
            $v->sync_log = 'seller_custom_field nulo o inválido';
            $v->save();
            $errores++;
            continue;
        }

        if ($v->syncFromSku()) {
            $v->save();
            $actualizadas++;
        } else {
            $v->save();
            $sinCambios++;
        }
    }

    $this->info("✅ Sincronización finalizada.");
    $this->info("🔢 Actualizadas: $actualizadas | Sin cambios: $sinCambios | Errores: $errores");
}

}
