<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;

class MlibreActualizarStockSeguro extends Command
{
    protected $signature = 'mlibre:actualizar-stock-seguro {ml_id} {stock} {variation_id?}';
    protected $description = 'Actualiza el stock de una publicación o variación SOLO si no está en FULL.';

    public function handle()
    {
        $mlId = $this->argument('ml_id');
        $stock = (int) $this->argument('stock');
        $variationId = $this->argument('variation_id');

        $this->info("🔎 Consultando publicación {$mlId}...");

        $token = app(MlibreTokenService::class)->getValidAccessToken();

        $res = Http::withToken($token)
            ->get("https://api.mercadolibre.com/items/{$mlId}?include_attributes=all&include_variations=true");

        if (!$res->ok()) {
            $this->error("❌ Error al obtener publicación: " . $res->body());
            return 1;
        }

        $item = $res->json();
        $hasVariations = !empty($item['variations']);

        // 🔁 Si tiene variaciones, se requiere el variation_id
        if ($hasVariations) {
            if (!$variationId) {
                $this->error("⚠️ La publicación tiene variaciones. Debes pasar el variation_id como tercer argumento.");
                return 1;
            }

            $variation = collect($item['variations'])->firstWhere('id', (int) $variationId);

            if (!$variation) {
                $this->error("❌ La variación {$variationId} no fue encontrada.");
                return 1;
            }

            if (!empty($variation['inventory_id'])) {
                $this->warn("⚠️ La variación está en FULL (inventory_id: {$variation['inventory_id']}) — NO se puede actualizar vía API.");
                return 0;
            }

            $this->info("✅ Actualizando stock en variación {$variationId} a {$stock}...");

            $putRes = Http::withToken($token)->put(
                "https://api.mercadolibre.com/items/{$mlId}/variations/{$variationId}",
                ['available_quantity' => $stock]
            );

        } else {
            if (!empty($item['inventory_id'])) {
                $this->warn("⚠️ La publicación está en FULL (inventory_id: {$item['inventory_id']}) — NO se puede actualizar vía API.");
                return 0;
            }

            $this->info("✅ Actualizando stock en ítem {$mlId} a {$stock}...");

            $putRes = Http::withToken($token)->put(
                "https://api.mercadolibre.com/items/{$mlId}",
                ['available_quantity' => $stock]
            );
        }

        if ($putRes->ok()) {
            $this->info("🎉 Stock actualizado correctamente.");
        } else {
            $this->error("❌ Falló el update: " . $putRes->body());
        }

        return 0;
    }
}
