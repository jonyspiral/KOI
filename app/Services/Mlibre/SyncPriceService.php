<?php
namespace App\Services\Mlibre;

use Illuminate\Support\Facades\Http;
use App\Models\MlVariante;

class SyncPriceService
{
    public function sincronizar(MlVariante $variante): array
    {
        $sku = $variante->skuVariante;

        if (!$sku || $sku->ml_price === null) {
            $variante->sync_status = 'E';
            $variante->sync_log = '❌ Precio ML no disponible en SKU';
            $variante->save();
            return ['success' => false, 'log' => $variante->sync_log];
        }

        if (!$variante->ml_id || !$variante->variation_id) {
            $variante->sync_status = 'E';
            $variante->sync_log = '❌ Faltan datos para sincronizar (ml_id o variation_id)';
            $variante->save();
            return ['success' => false, 'log' => $variante->sync_log];
        }

        $token = app(MlibreTokenService::class)->getValidAccessToken();

        $payload = [
            'price' => (float) $sku->ml_price,
        ];

        $url = "https://api.mercadolibre.com/items/{$variante->ml_id}/variations/{$variante->variation_id}";

        try {
            $res = Http::withToken($token)->put($url, $payload);

            if ($res->ok()) {
                $variante->precio = $sku->ml_price;
                $variante->sync_status = 'S';
                $variante->sync_log = '✅ Precio sincronizado';
                $variante->save();
                return ['success' => true];
            } else {
                $variante->sync_status = 'E';
                $variante->sync_log = '❌ Error ML: ' . $res->status();
                $variante->save();
                return ['success' => false];
            }
        } catch (\Exception $e) {
            $variante->sync_status = 'E';
            $variante->sync_log = '❌ Excepción: ' . $e->getMessage();
            $variante->save();
            return ['success' => false];
        }
    }
}
