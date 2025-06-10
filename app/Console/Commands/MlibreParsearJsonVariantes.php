<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\MlVariante;

class MlibreParsearJsonVariantes extends Command
{
    protected $signature = 'mlibre:parsear-json-variantes';
    protected $description = 'Parsea los JSON descargados y llena la tabla ml_variantes';

    public function handle()
    {
        $path = storage_path('app/private/mlibre/items');
        $files = File::files($path);

        if (empty($files)) {
            $this->warn("No se encontraron archivos JSON en $path");
            return;
        }

        foreach ($files as $file) {
            $json = json_decode(file_get_contents($file->getPathname()), true);
            $mlId = $json['id'] ?? null;

            if (!$mlId || empty($json['variations'])) {
                $this->warn("Publicación inválida o sin variaciones: " . $file->getFilename());
                continue;
            }

            foreach ($json['variations'] as $variation) {
                $variationId = $variation['id'] ?? null;
                if (!$variationId) continue;

                $price = $variation['price'] ?? null;
                $stock = $variation['available_quantity'] ?? null;
                $scf = $variation['seller_custom_field'] ?? null;

                // Extraer color y talle
                $color = null;
                $talle = null;

                foreach ($variation['attribute_combinations'] ?? [] as $attr) {
                    $name = strtolower($attr['name']);
                    if (str_contains($name, 'color')) {
                        $color = $attr['value_name'];
                    } elseif (str_contains($name, 'talle') || str_contains($name, 'size')) {
                        $talle = $attr['value_name'];
                    }
                }

                // SKU sugerido: ej. MLA123456GN40
                $varSku = null;
                if ($mlId && $color && $talle) {
                    $codColor = strtoupper(preg_replace('/[^A-Z]/', '', substr($color, 0, 2)));
                    $codTalle = str_pad(preg_replace('/\D/', '', $talle), 2, '0', STR_PAD_LEFT);
                    $varSku = $mlId . $codColor . $codTalle;
                }

                MlVariante::updateOrCreate(
                    ['variation_id' => $variationId],
                    [
                        'ml_id' => $mlId,
                        'color' => $color,
                        'talle' => $talle,
                        'precio' => $price,
                        'stock' => $stock,
                        'seller_custom_field_actual' => $scf,
                        'var_sku_sugerido' => $varSku,
                        'sincronizado' => false,
                    ]
                );
            }
        }

        $this->info("✅ Variantes procesadas correctamente desde JSON.");
    }
}
