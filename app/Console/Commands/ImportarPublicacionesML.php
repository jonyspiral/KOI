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
    $this->info('📦 Importando publicaciones desde JSON...');

    $folder = storage_path('app/private/mlibre/items');
    $archivos = glob($folder . '/*.json');

    foreach ($archivos as $path) {
        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!$data || empty($data['id'])) {
            $this->error("❌ Archivo inválido: " . basename($path));
            continue;
        }

        $itemId     = $data['id'];
        $variations = $data['variations'] ?? [];
        $precio     = $this->extraerPrecio($data);
        $stock      = $this->extraerStock($data);

        // Atributos del nivel raíz
        $attrs = collect($data['attributes'] ?? []);
        $modelo     = $attrs->firstWhere('id', 'MODEL')['value_name'] ?? null;
        $sellerSku  = $attrs->firstWhere('id', 'SELLER_SKU')['value_name'] ?? null;

        // Manejo de identificadores raíz (solo si no hay variaciones)
        $productNumber = $data['product_number'] ?? $data['user_product_id'] ?? null;
        $sellerCustomField = $data['seller_custom_field'] ?? null;

        // Guardar la publicación
MlPublicacion::updateOrCreate(
    ['ml_id' => $itemId],
    [
        'ml_name'        => $data['title'] ?? null,
        'ml_reference'   => $this->inferirMlReference($variations),
        'ml_description' => $data['description'] ?? null,
        'status'         => $data['status'] ?? null,
        'category_id'    => $data['category_id'] ?? null,
        'logistic_type'  => $data['shipping']['logistic_type'] ?? null,
        'family_id'      => $data['family']['id'] ?? null,
        'family_name'    => $data['family']['name'] ?? null,
        'raw_json'       => $data,
    ]
);

        // Si no hay variaciones, crear una única variante
        if (empty($variations)) {
            MlVariante::updateOrCreate(
                ['ml_id' => $itemId, 'variation_id' => null],
                [
                    'product_number'      => $productNumber,
                    'seller_custom_field' => $sellerCustomField,
                    'talle'               => $this->extraerTalle($data),
                    'color'               => $this->extraerColor($data),
                    'modelo'              => $modelo,
                    'seller_sku'          => $sellerSku,
                    'precio'              => $precio,
                    'stock'               => $stock,
                    'family_id'           => $data['family']['id'] ?? null,
                    'titulo'              => $data['title'] ?? null,
                ]
            );

            $this->info("✅ Publicación simple importada: $itemId");
            continue;
        }

        // Si hay variaciones, recorrerlas
        foreach ($variations as $variation) {
            $variationId     = $variation['id'] ?? null;
            $sku             = $variation['seller_custom_field'] ?? null;
            $productNumber   = $variation['product_number'] ?? $variation['user_product_id'] ?? null;

            $attrs = collect($variation['attributes'] ?? []);
            $modelo     = $attrs->firstWhere('id', 'MODEL')['value_name'] ?? $modelo;
            $sellerSku  = $attrs->firstWhere('id', 'SELLER_SKU')['value_name'] ?? null;

            MlVariante::updateOrCreate(
                ['ml_id' => $itemId, 'variation_id' => $variationId],
                [
                    'product_number'      => $productNumber,
                    'seller_custom_field' => $sku,
                    'talle'               => $this->extraerTalle($variation),
                    'color'               => $this->extraerColor($variation),
                    'modelo'              => $modelo,
                    'seller_sku'          => $sellerSku,
                    'precio'              => $variation['price'] ?? $precio,
                    'stock'               => $variation['available_quantity'] ?? null,
                    'family_id'           => $data['family']['id'] ?? null,
                    'titulo'              => $data['title'] ?? null,
                ]
            );
        }

        $this->info("✅ Publicación con variaciones importada: $itemId");
    }

    $this->info('✅ Importación finalizada.');
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

   private function extraerTalle(array $data): ?string
{
    // Buscar primero en attribute_combinations
    foreach ($data['attribute_combinations'] ?? [] as $attr) {
        if (Str::contains(strtolower($attr['name']), 'talle')) {
            return $attr['value_name'] ?? null;
        }
    }

    // Si no está, buscar en attributes
    foreach ($data['attributes'] ?? [] as $attr) {
        if (Str::contains(strtolower($attr['name']), 'talle')) {
            return $attr['value_name'] ?? null;
        }
    }

    return null;
}

private function extraerColor(array $data): ?string
{
    // Buscar primero en attribute_combinations
    foreach ($data['attribute_combinations'] ?? [] as $attr) {
        if (Str::contains(strtolower($attr['name']), 'color')) {
            return $attr['value_name'] ?? null;
        }
    }

    // Si no está, buscar en attributes
    foreach ($data['attributes'] ?? [] as $attr) {
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
