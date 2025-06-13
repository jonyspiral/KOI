<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MlVariante;
use App\Services\Mlibre\MlibreTokenService;

class MlibreActualizarStock extends Command
{
    protected $signature = 'mlibre:actualizar-stock';
    protected $description = 'Actualiza el stock de Mercado Libre en ml_variantes (FULL y FLEX)';

    public function handle()
    {
        
        $this->info('📦 Iniciando actualización de stock desde Mercado Libre...');
        $userId = env('MLIBRE_USER_ID'); // 🔑 desde .env
        $token = (new MlibreTokenService())->getValidAccessToken($userId);  
       

        // Asumimos que todas las publicaciones están en ml_variantes
        $publicaciones = MlVariante::select('ml_id')->distinct()->pluck('ml_id');

        foreach ($publicaciones as $itemId) {
            $url = "https://api.mercadolibre.com/items/{$itemId}?include_attributes=all";

$response = Http::withToken($token)->get($url);

if (!$response->ok()) {
    $this->error("❌ Error al obtener item $itemId: " . $response->status());
    continue;
}

$item = $response->json();

foreach ($item['variations'] ?? [] as $variation) {
    $userProductId = $variation['user_product_id'] ?? null;

    if (!$userProductId) {
        $this->warn("⚠️ Variación sin user_product_id");
        continue;
    }

    $stockFull = 0;
    $stockFlex = 0;

    foreach ($variation['available_quantity_by_warehouse'] ?? [] as $warehouse) {
        if ($warehouse['logistic_type'] === 'fulfillment') {
            $stockFull += $warehouse['quantity'];
        } elseif ($warehouse['logistic_type'] === 'cross_docking') {
            $stockFlex += $warehouse['quantity'];
        }
    }

    $variante = \App\Models\MlVariante::where('product_number', $userProductId)->first();

    if (!$variante) {
        $this->warn("❌ No se encontró en ml_variantes: product_number = $userProductId");
        continue;
    }

    $variante->stock_full = $stockFull;
    $variante->stock_flex = $stockFlex;
    $variante->stock = $stockFull + $stockFlex;
    $variante->save();

    $this->info("✅ Actualizado: {$userProductId} FULL=$stockFull FLEX=$stockFlex");
}
        }

        $this->info('🎯 Sincronización de stock finalizada.');
    }
}


