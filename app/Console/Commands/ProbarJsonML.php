<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ProbarJsonML extends Command
{
    protected $signature = 'mlibre:probar-json {archivo}';
    protected $description = 'Muestra modelo y SELLER_SKU del JSON de Mercado Libre';

    public function handle()
    {
        $archivo = $this->argument('archivo');

        if (!Storage::exists("mlibre/items/{$archivo}")) {
            $this->error("Archivo mlibre/items/{$archivo} no encontrado.");
            return;
        }

        $json = Storage::get("mlibre/items/{$archivo}");
        $data = json_decode($json, true);
        $variations = $data['variations'] ?? [];

        if (empty($variations)) {
            $this->warn("El JSON no contiene variations.");
            return;
        }

        foreach ($variations as $i => $v) {
            $attrs = collect($v['attributes'] ?? []);
            $modelo = $attrs->firstWhere('id', 'MODEL')['value_name'] ?? '-';
            $seller_sku = $attrs->firstWhere('id', 'SELLER_SKU')['value_name'] ?? '-';

            $this->line("🔸 Variante #" . ($i + 1));
            $this->info("  Modelo: $modelo");
            $this->info("  Seller SKU: $seller_sku\n");
        }
    }
}
