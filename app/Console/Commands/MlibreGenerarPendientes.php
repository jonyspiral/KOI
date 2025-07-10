<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Sync\ComparadorTimestampsService;
use App\Models\SkuVariante;
use App\Models\MlVariante;

class MlibreGenerarPendientes extends Command
{
    protected $signature = 'mlibre:generar-pendientes';
    protected $description = 'Detecta registros de MlVariante desactualizados respecto a SkuVariante y marca sync_status = U';

    public function handle()
    {
        $this->info('🔍 Comparando timestamps y campos entre SkuVariante y MlVariante...');

        $comparador = app(ComparadorTimestampsService::class);

        $modificados = $comparador->compararTimestamps(
            SkuVariante::class,
            MlVariante::class,
            'var_sku',
            ['stock', 'precio']
        );

        $this->info("✅ Total de variantes desactualizadas marcadas con 'U': {$modificados->count()}");
    }
}
