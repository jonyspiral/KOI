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
    protected $description = 'Importa publicaciones ML desde archivos JSON almacenados localmente con validaciones mejoradas';

    public function handle()
    {
        $path = 'mlibre/items';
        $files = Storage::files($path);

        $this->info("\n📁 Archivos JSON detectados: " . count($files));

        foreach ($files as $file) {
            $json = Storage::get($file);
            $data = json_decode($json, true);

            if (!isset($data['id'])) {
                $this->warn("❌ Archivo sin ID de publicación: {$file}");
                continue;
            }

            $ml_id = $data['id'];
            $this->line("\n🟢 Procesando publicación: {$ml_id}");

            $variations = $data['variations'] ?? [];

            if (empty($variations)) {
                $this->warn("⚠️ Publicación {$ml_id} no tiene variantes. Se guarda sin variantes.");
            }

            $ml_reference = $this->inferirMlReference($variations);

            $publicacion = MlPublicacion::updateOrCreate(
                ['ml_id' => $ml_id],
                [
                    'ml_reference'    => $ml_reference,
                    'ml_name'         => $data['title'] ?? null,
                    'ml_description'  => null,
                    'mlibre_precio'   => $this->extraerPrecio($data),
                    'mlibre_stock'    => $this->extraerStock($data),
                    'status'          => $data['status'] ?? null,
                    'raw_json'        => $data,
                ]
            );

            $publicacion->variantes()->delete();

            foreach ($variations as $v) {
                $sku = $v['seller_custom_field'] ?? null;

                if (!$sku) {
                    $this->warn("❌ Variante sin SKU en publicación {$ml_id}. Omitida.");
                    continue;
                }

                MlVariante::create([
                    'ml_publicacion_id' => $publicacion->id,
                    'sku_'              => $sku,
                    'talle'             => $this->extraerTalle($v),
                    'precio'            => $v['price'] ?? null,
                    'stock'             => $v['available_quantity'] ?? null,
                    'raw_json'          => $v,
                ]);
            }

            $this->info("✅ Publicación {$ml_id} importada con " . count($variations) . " variantes.");
        }

        $this->info("\n🎉 Proceso finalizado.");
    }

    private function extraerPrecio(array $data): ?float
    {
        return $data['variations'][0]['price'] ?? $data['price'] ?? null;
    }

    private function extraerStock(array $data): ?int
    {
        if (!empty($data['variations'])) {
            return collect($data['variations'])->sum(function ($v) {
                return $v['available_quantity'] ?? 0;
            });
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
