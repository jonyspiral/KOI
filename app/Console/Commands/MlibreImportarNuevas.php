<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;

class MlibreImportarNuevas extends Command
{
    protected $signature = 'mlibre:importar-nuevas';
    protected $description = 'Importa publicaciones nuevas desde Mercado Libre a ml_publicaciones y ml_variantes';

    public function handle()
    {
        $token = app(MlibreTokenService::class)->getValidAccessToken();
        $userId = env('MLIBRE_USER_ID');

        if (!$userId) {
            $this->error("❌ MLIBRE_USER_ID no está definido en .env");
            return;
        }

        $estados = ['active', 'paused'];
        $limitePorPagina = 50;
        $limitePorLote = 5;

        foreach ($estados as $estado) {
            $this->info("🔍 Procesando publicaciones con estado: $estado");

            $offset = 0;
            while (true) {
                $url = "https://api.mercadolibre.com/users/{$userId}/items/search?status={$estado}&limit={$limitePorPagina}&offset={$offset}";
                $res = Http::ml($token)->get($url)->json();
                $ids = $res['results'] ?? [];
                if (empty($ids)) break;

                foreach (array_chunk($ids, $limitePorLote) as $lote) {
                    foreach ($lote as $itemId) {
                        $item = Http::ml($token)->get("https://api.mercadolibre.com/items/{$itemId}")->json();
                        if (!isset($item['id'])) continue;

                        // 🔹 ml_publicaciones
                        DB::table('ml_publicaciones')->updateOrInsert(
                            ['ml_id' => $itemId],
                            [
                                'status'        => $item['status'] ?? null,
                                'logistic_type' => $item['shipping']['logistic_type'] ?? null,
                                'family_id'     => $item['family_id'] ?? null,
                                'family_name'   => $item['family_name'] ?? null,
                                'ml_name'       => $item['title'] ?? null,
                                'mlibre_precio' => $item['price'] ?? null,
                                'mlibre_stock'  => $item['available_quantity'] ?? null,
                                'raw_json'      => json_encode($item),
                                'updated_at'    => now(),
                            ]
                        );

                        // 🔹 ml_variantes
                        $variaciones = $item['variations'] ?? [];

                        if (!empty($variaciones)) {
                            foreach ($variaciones as $v) {
                                $scf = $v['seller_custom_field'] ?? null;
                                $sku = collect($v['attributes'] ?? [])->firstWhere('id', 'SELLER_SKU')['value_name'] ?? null;
                                $color = collect($v['attribute_combinations'] ?? [])->firstWhere('id', 'COLOR')['value_name'] ?? null;
                                $talle = collect($v['attribute_combinations'] ?? [])->firstWhere('id', 'SIZE')['value_name'] ?? null;
                                $modelo = collect($v['attributes'] ?? [])->firstWhere('id', 'MODEL')['value_name'] ?? null;

                                $yaExiste = $scf
                                    ? DB::table('ml_variantes')->where('seller_custom_field', $scf)->exists()
                                    : DB::table('ml_variantes')->where('ml_id', $itemId)->where('variation_id', $v['id'])->exists();

                                if (!$yaExiste) {
                                    DB::table('ml_variantes')->insert([
                                        'ml_id' => $itemId,
                                        'variation_id' => $v['id'],
                                        'seller_custom_field' => $scf ?: $sku,
                                        'titulo' => $item['title'],
                                        'talle' => $talle,
                                        'color' => $color,
                                        'modelo' => $modelo,
                                        'seller_sku' => $sku,
                                        'precio' => $item['price'],
                                        'stock' => $v['available_quantity'] ?? null,
                                        'sync_status' => 'N',
                                        'vigente' => 1,
                                        'manual_price' => 0,
                                        'manual_stock' => 0,
                                        'stock_flex' => null,
                                        'stock_full' => null,
                                        'seller_custom_field_actual' => $scf ?: $sku,
                                        'sincronizado' => 0,
                                        'raw_json' => json_encode($v),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                    $this->info("➕ Variante nueva importada: {$itemId} (SCF: {$scf})");
                                }
                            }
                        } else {
                            $sku = collect($item['attributes'])->firstWhere('id', 'SELLER_SKU')['value_name'] ?? null;
                            $yaExiste = DB::table('ml_variantes')->where('ml_id', $itemId)->exists();

                            if (!$yaExiste) {
                                DB::table('ml_variantes')->insert([
                                    'ml_id' => $itemId,
                                    'variation_id' => null,
                                    'seller_custom_field' => $sku,
                                    'titulo' => $item['title'],
                                    'talle' => null,
                                    'color' => null,
                                    'modelo' => null,
                                    'seller_sku' => $sku,
                                    'precio' => $item['price'],
                                    'stock' => $item['available_quantity'],
                                    'sync_status' => 'N',
                                    'vigente' => 1,
                                    'manual_price' => 0,
                                    'manual_stock' => 0,
                                    'stock_flex' => null,
                                    'stock_full' => null,
                                    'seller_custom_field_actual' => $sku,
                                    'sincronizado' => 0,
                                    'raw_json' => json_encode($item),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $this->info("➕ Producto sin variaciones importado: {$itemId}");
                            }
                        }
                    }
                }

                $offset += $limitePorPagina;
            }
        }

        $this->info("✅ Finalizado");
    }
}
