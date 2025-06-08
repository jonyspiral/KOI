<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;

class MlibreTestVariations extends Command
{
    protected $signature = 'mlibre:test-variations {item_id}';
    protected $description = 'Consulta una publicación y muestra las variations, incluyendo attributes completos';

    public function handle()
    {
        $itemId = $this->argument('item_id');
        $tokenService = new MlibreTokenService();
        $token = $tokenService->getValidAccessToken();

        $url = "https://api.mercadolibre.com/items/{$itemId}?include_attributes=all";

        $response = Http::withToken($token)->get($url);

        if (!$response->ok()) {
            $this->error("❌ Error en la solicitud: {$response->status()}");
            $this->line($response->body());
            return;
        }

        $data = $response->json();

        if (empty($data['variations'])) {
            $this->warn("⚠️ No se encontraron variations para {$itemId}");
            return;
        }

        foreach ($data['variations'] as $i => $v) {
            $sku = $v['seller_custom_field'] ?? '❌ sin SKU';
            $talle = collect($v['attribute_combinations'] ?? [])->firstWhere('name', 'Talle')['value_name'] ?? '-';
            $color = collect($v['attribute_combinations'] ?? [])->firstWhere('name', 'Color')['value_name'] ?? '-';
            $this->line("🔹 Var $i → SKU: $sku | Talle: $talle | Color: $color | Stock: " . ($v['available_quantity'] ?? 0));
        }
    }
}
