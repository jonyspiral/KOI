<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;
use App\Models\MlPublicacion;
use App\Models\MlVariante;

class MlibreActualizarSku extends Command
{
    protected $signature = 'mlibre:actualizar-sku 
                            {ml_id?} 
                            {variation_id?} 
                            {sku?} 
                            {--todos : Actualiza todos los SKUs de una publicación} 
                            {--sync : Sincroniza variantes con nuevo_seller_custom_field pendientes}';

    protected $description = 'Actualiza el SKU (seller_custom_field) de una o todas las variantes de una publicación ML, o sincroniza las variantes pendientes con --sync';

    public function handle()
    {
        $userId = env('MLIBRE_USER_ID');

if (!$userId) {
    $this->error('⚠️ No se definió MLIBRE_USER_ID en el archivo .env');
    return;
}

$token = app(\App\Services\Mlibre\MlibreTokenService::class)->getValidAccessToken($userId);

        if ($this->option('sync')) {
            $this->info("🔄 Sincronizando variantes pendientes con nuevo SCF...");

            $variantes = MlVariante::whereNotNull('nuevo_seller_custom_field')
                ->where('sincronizado', 0)
                ->get();

            if ($variantes->isEmpty()) {
                $this->info("✅ No hay variantes pendientes de sincronizar.");
                return;
            }

            foreach ($variantes as $var) {
                $body = [
                    "variations" => [
                        [
                            "id" => $var->variation_id,
                            "seller_custom_field" => $var->nuevo_seller_custom_field
                        ]
                    ]
                ];

                $res = Http::withToken($token)
                    ->put("https://api.mercadolibre.com/items/{$var->ml_id}", $body);

                if ($res->successful()) {
                    $var->seller_custom_field_actual = $var->nuevo_seller_custom_field;
                    $var->sincronizado = 1;
                    $var->save();

                    $this->info("✅ SKU actualizado para {$var->ml_id} / {$var->variation_id}");
                } else {
                    $this->error("❌ Error en {$var->ml_id} / {$var->variation_id}: " . $res->body());
                }
            }

            return;
        }

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
            return;
        }

        $variationId = $this->argument('variation_id');
        $sku = $this->argument('sku');

        if (!$mlId || !$variationId || !$sku) {
            $this->error("❌ Debes indicar ml_id, variation_id y sku si no usás --todos ni --sync");
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
