<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;

class MlibreActualizarPublicacion extends Command
{
    protected $signature = 'mlibre:actualizar-publicacion
        {ml_id : ID de la publicación (MLA...)}
        {--variation= : ID de la variación (si aplica)}
        {--stock= : Stock disponible a actualizar}
        {--sku= : Código SKU a asignar (seller_custom_field)}';

    protected $description = 'Actualiza una publicación (stock o SKU) en ML. Maneja variaciones y unitarios.';

    public function handle()
    {
        $mlId = $this->argument('ml_id');
        $variationId = $this->option('variation');
        $stock = $this->option('stock');
        $sku = $this->option('sku');

        if (is_null($stock) && is_null($sku)) {
            $this->error('❌ Debe especificar al menos --stock o --sku para actualizar.');
            return 1;
        }

        $token = app(MlibreTokenService::class)->getValidAccessToken();

        $res = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$mlId}?include_attributes=all&include_variations=true");

        if (!$res->ok()) {
            $this->error("❌ Error al obtener la publicación: " . $res->body());
            return 1;
        }

        $item = $res->json();
        $payload = [];

        // Publicación con variaciones
        if (!empty($item['variations'])) {
            if (!$variationId) {
                $this->error('❌ Esta publicación tiene variaciones. Debe pasar --variation para continuar.');
                return 1;
            }

            $variation = collect($item['variations'])->firstWhere('id', (int) $variationId);

            if (!$variation) {
                $this->error("❌ La variación $variationId no fue encontrada.");
                return 1;
            }

            if (!empty($variation['inventory_id'])) {
                $this->warn("⚠️ La variación está en FULL (inventory_id: {$variation['inventory_id']}) — No se puede modificar stock.");
                return 0;
            }

            if ($stock !== null) {
                $payload['available_quantity'] = (int) $stock;
            }

            if ($sku !== null) {
                $payload['seller_custom_field'] = $sku;
            }

            if (empty($payload)) {
                $this->warn("⚠️ No hay cambios que aplicar.");
                return 0;
            }

            $put = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$mlId}/variations/{$variationId}", $payload);
        }

       // Publicación sin variaciones
else {
    if ($stock !== null) {
        $payload['available_quantity'] = (int) $stock;
    }

    if ($sku !== null) {
        $payload['seller_custom_field'] = $sku;

        // Revisar si ya existe el atributo SELLER_SKU
        $attributes = $item['attributes'] ?? [];

        $hasSkuAttr = false;
        foreach ($attributes as &$attr) {
            if ($attr['id'] === 'SELLER_SKU') {
                $attr['value_name'] = $sku;
                $attr['values'] = [['id' => null, 'name' => $sku, 'struct' => null]];
                $hasSkuAttr = true;
                break;
            }
        }

        if (!$hasSkuAttr) {
            $attributes[] = [
                'id' => 'SELLER_SKU',
                'value_name' => $sku,
                'values' => [['id' => null, 'name' => $sku, 'struct' => null]],
            ];
        }

        $payload['attributes'] = $attributes;
    }

    if (empty($payload)) {
        $this->warn("⚠️ No hay cambios que aplicar.");
        return 0;
    }

    $put = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$mlId}", $payload);
}


        if ($put->ok()) {
            $this->info("🎉 Publicación actualizada correctamente.");
        } else {
            $this->error("❌ Falló el update: " . $put->body());
        }

        return 0;
    }
}
