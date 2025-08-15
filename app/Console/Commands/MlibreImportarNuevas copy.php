<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;
use App\Models\MlPublicacion;
use App\Models\MlVariante;
use App\Models\SkuVariante;
use Illuminate\Support\Facades\DB;

class MlibreImportarNuevas extends Command
{
    protected $signature = 'mlibre:importar-nuevas';
    protected $description = 'Importar publicaciones y variantes desde Mercado Libre y sincronizar en KOI2';

    public function handle()
    {
        $userId = env('MLIBRE_USER_ID');
        $tokenService = new MlibreTokenService();
        $token = $tokenService->getValidAccessToken($userId);

        $estados = ['active', 'paused', 'closed'];

        foreach ($estados as $estado) {
            $this->info("🔍 Procesando publicaciones con estado: $estado");

            $offset = 0;
            $limit = 50;
            $totalProcesadas = 0;

            do {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer $token",
                    'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])->get("https://api.mercadolibre.com/users/$userId/items/search", [
                    'status' => $estado,
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                if ($response->failed()) {
                    $this->error("❌ Error al consultar publicaciones ($estado): " . $response->body());
                    break;
                }

                $data = $response->json();
                $itemIds = $data['results'] ?? [];
                $total = count($itemIds);

                foreach ($itemIds as $itemId) {
                    $itemResp = Http::withHeaders([
                        'Authorization' => "Bearer $token",
                        'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                        'Accept'        => 'application/json',
                        'Content-Type'  => 'application/json',
                    ])->get("https://api.mercadolibre.com/items/$itemId?include_attributes=all");

                    if ($itemResp->failed()) {
                        $this->error("❌ Error al obtener item $itemId");
                        continue;
                    }

                    $item = $itemResp->json();

                    // Crear o actualizar publicación
                    MlPublicacion::updateOrCreate(
                        ['ml_id' => $itemId],
                        [
                            'status'         => $item['status'] ?? null,
                            'logistic_type'  => $item['shipping']['logistic_type'] ?? null,
                            'ml_name'        => $item['title'] ?? null,
                            'mlibre_precio'  => $item['price'] ?? null,
                            'mlibre_stock'   => $item['available_quantity'] ?? null,
                            'category_id'    => $item['category_id'] ?? null,
                            'family_id'      => $item['family_id'] ?? null,
                            'family_name'    => $item['family_name'] ?? null,
                            'raw_json' => json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),

                        ]
                    );

                    // Procesar variantes
                    foreach ($item['variations'] ?? [] as $var) {
                        $color  = self::extractAttribute($var, 'COLOR');
                        $talle  = self::extractAttribute($var, 'SIZE');
                        $modelo = self::extractAttribute($var, 'MODEL');

                        // Prioridad SCF > seller_sku > null
                        $scf = $var['seller_custom_field'] ?? $var['seller_sku'] ?? null;

                        $mlVariante = MlVariante::updateOrCreate(
                            ['variation_id' => $var['id']],
                            [
                                'ml_id'               => $item['id'],
                                'color'               => $color,
                                'talle'               => $talle,
                                'modelo'              => $modelo,
                                'precio'              => $var['price'] ?? null,
                                'stock'               => $var['available_quantity'] ?? null,
                                'seller_custom_field' => $scf,
                                'ml_name'             => $item['title'] ?? null,
                            ]
                        );

                        // Verificar si existe en sku_variantes
                        $existeSku = SkuVariante::where('var_sku', $scf)->exists();
                        $mlVariante->sync_status = $existeSku ? 'S' : 'N';
                        $mlVariante->save();
                    }

                    $totalProcesadas++;
                }

                $offset += $limit;

            } while ($total === $limit);

            $this->info("✅ Total procesadas ($estado): $totalProcesadas");
        }

        $this->info("✅ Importación completada.");
    }

    // Utilidad para extraer atributos
    private static function extractAttribute(array $variation, string $name): ?string
    {
        foreach ($variation['attributes'] ?? [] as $attr) {
            if (strtoupper($attr['name'] ?? '') === strtoupper($name)) {
                return $attr['value_name'] ?? null;
            }
        }
        return null;
    }
}
