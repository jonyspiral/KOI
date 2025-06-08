<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;

class VerSkusMlibreCommand extends Command
{
    protected $signature = 'mlibre:ver-skus {ml_id}';
    protected $description = 'Ver SKUs de una publicación (vía /variations)';

    public function handle()
    {
        $mlId = $this->argument('ml_id');
        $token = app(MlibreTokenService::class)->getValidAccessToken();

        $response = Http::withToken($token)
            ->get("https://api.mercadolibre.com/items/{$mlId}/variations");

        if (!$response->ok()) {
            $this->error("❌ Error al obtener variaciones: " . $response->status());
            return;
        }

        $variations = $response->json();

        if (empty($variations)) {
            $this->warn("❌ No se encontraron variaciones para {$mlId}");
            return;
        }

        foreach ($variations as $i => $var) {
            $color = collect($var['attribute_combinations'] ?? [])->firstWhere('id', 'COLOR')['value_name'] ?? 'Sin color';
            $talle = collect($var['attribute_combinations'] ?? [])->firstWhere('id', 'SIZE')['value_name'] ?? 'Sin talle';
            $stock = $var['available_quantity'] ?? '¿?';

            $sku = $var['seller_sku'] ?? '❌ sin seller_sku';
            $custom = $var['seller_custom_field'] ?? '❌ sin seller_custom_field';

            $this->line("🔹 Var {$i} → SKU: {$sku} | Custom: {$custom} | Talle: {$talle} | Color: {$color} | Stock: {$stock}");
        }
    }
}
