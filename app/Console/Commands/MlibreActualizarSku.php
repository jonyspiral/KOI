<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;
use App\Models\MlPublicacion;

class MlibreActualizarSku extends Command
{
    protected $signature = 'mlibre:actualizar-sku {ml_id} {variation_id?} {sku?} {--todos}';
    protected $description = 'Actualiza el SKU (seller_custom_field) de una o todas las variantes de una publicación ML';

    public function handle()
    {
        $token = app(MlibreTokenService::class)->getValidAccessToken();
        $mlId = $this->argument('ml_id');
        $updateAll = $this->option('todos');

        if ($updateAll) {
            $this->info("Actualizando todos los SKUs para $mlId...");

            $response = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$mlId}/variations");
            if ($response->failed()) {
                $this->error("❌ Error al obtener variaciones: " . $response->body());
                return;
            }

            $variaciones = $response->json();

            // Buscar cod_articulo en KOI por ml_id o ml_reference
            $codArticulo = MlPublicacion::where('ml_id', $mlId)->value('ml_reference') ?? 'XXXX';
            $codColor = 'GN'; // En el futuro podría mapearse desde ML

            $variationsBody = [];
            foreach ($variaciones as $var) {
                $talle = collect($var['attribute_combinations'])
                    ->firstWhere('id', 'SIZE')['value_name'] ?? null;

                if ($talle) {
                    $sku = $codArticulo . $codColor . $talle;
                    $variationsBody[] = [
                        'id' => $var['id'],
                        'seller_custom_field' => $sku
                    ];
                }
            }

            $putResponse = Http::withToken($token)->put(
                "https://api.mercadolibre.com/items/{$mlId}",
                ["variations" => $variationsBody]
            );

            $this->info("🔄 Status: " . $putResponse->status());
            $this->line(json_encode($putResponse->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        } else {
            $variationId = $this->argument('variation_id');
            $sku = $this->argument('sku');

            if (!$variationId || !$sku) {
                $this->error("❌ Debes indicar variation_id y sku si no usás --todos");
                return;
            }

            $body = [
                "variations" => [
                    [
                        "id" => $variationId,
                        "seller_custom_field" => $sku
                    ]
                ]
            ];

            $response = Http::withToken($token)
                ->put("https://api.mercadolibre.com/items/{$mlId}", $body);

            $this->info("🔄 Status: " . $response->status());
            $this->line(json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}
