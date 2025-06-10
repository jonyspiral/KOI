<?php

namespace App\Console\Commands;

use App\Models\MlPublicacion;
use App\Models\MlVariante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportarPublicacionesML extends Command
{
    protected $signature = 'mlibre:importar-json';
    protected $description = 'Importa publicaciones ML desde archivos JSON almacenados localmente';

   public function handle()
{
    $this->info('Importando publicaciones desde JSON...');

    $folder = storage_path('app/private/mlibre/items');
    $archivos = glob($folder . '/*.json');

    foreach ($archivos as $path) {
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data || empty($data['id'])) {
            $this->error("Archivo inválido: " . basename($path));
            continue;
        }

        $itemId     = $data['id'];
        $variations = $data['variations'] ?? [];
        $precio     = $this->extraerPrecio($data);
        $stock      = $this->extraerStock($data);

        if (empty($variations)) {
            $attrs      = collect($data['attributes'] ?? []);
            $modelo     = $attrs->firstWhere('id', 'MODEL')['value_name'] ?? null;
            $sellerSku  = $attrs->firstWhere('id', 'SELLER_SKU')['value_name'] ?? null;

            MlVariante::updateOrCreate(
                ['ml_id' => $itemId, 'variation_id' => null],
                [
                    'sku_'       => null,
                    'talle'      => null,
                    'color'      => null,
                    'modelo'     => $modelo,
                    'seller_sku' => $sellerSku,
                    'precio'     => $precio,
                    'stock'      => $stock,
                ]
            );

            $this->info("Publicación simple importada: $itemId");
            continue;
        }

        foreach ($variations as $variation) {
            $variationId = $variation['id'] ?? null;
            $sku         = $variation['seller_custom_field'] ?? null;
            $talle       = $this->extraerTalle($variation);
            $color       = $this->extraerColor($variation);
            $attrs       = collect($variation['attributes'] ?? []);
            $modelo      = $attrs->firstWhere('id', 'MODEL')['value_name'] ?? null;
            $sellerSku   = $attrs->firstWhere('id', 'SELLER_SKU')['value_name'] ?? null;
            $stockVar    = $variation['available_quantity'] ?? null;
            $precioVar   = $variation['price'] ?? $precio;

            MlVariante::updateOrCreate(
                ['ml_id' => $itemId] + (is_null($variationId) ? ['variation_id' => null] : ['variation_id' => $variationId]),
                [
                    'sku_'       => $sku,
                    'talle'      => $talle,
                    'color'      => $color,
                    'modelo'     => $modelo,
                    'seller_sku' => $sellerSku,
                    'precio'     => $precioVar,
                    'stock'      => $stockVar,
                ]
            );

            $this->info("Variación importada: $itemId / $variationId");
        }
    }

    $this->info('Importación finalizada.');
}


    private function extraerPrecio(array $data): ?float
    {
        return $data['variations'][0]['price'] ?? $data['price'] ?? null;
    }

    private function extraerStock(array $data): ?int
    {
        if (!empty($data['variations'])) {
            return collect($data['variations'])->sum(fn($v) => $v['available_quantity'] ?? 0);
        }
        return $data['available_quantity'] ?? null;
    }

    private function extraerTalle(array $variation): ?string
    {
        foreach ($variation['attribute_combinations'] ?? [] as $attr) {
            if (Str::contains(strtolower($attr['name']), 'talle')) {
                return $attr['value_name'] ?? null;
            }
        }
        return null;
    }

    private function extraerColor(array $variation): ?string
    {
        foreach ($variation['attribute_combinations'] ?? [] as $attr) {
            if (Str::contains(strtolower($attr['name']), 'color')) {
                return $attr['value_name'] ?? null;
            }
        }
        return null;
    }

    private function inferirMlReference(array $variations): ?string
    {
        foreach ($variations as $v) {
            if (!empty($v['seller_custom_field'])) {
                $sku = $v['seller_custom_field'];
                return substr($sku, 0, strrpos($sku, '_')) ?: $sku;
            }
        }
        return null;
    }
}
