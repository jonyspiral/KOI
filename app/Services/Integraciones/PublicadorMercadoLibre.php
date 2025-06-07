<?php

namespace App\Services\Integraciones;

use App\Models\IntegracionPublicacion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class PublicadorMercadoLibre
{
    public function publicarLote(array $itemsSeleccionados): array
    {
        $agrupados = collect($itemsSeleccionados)->groupBy('agrupador');
        $resultados = [];

        foreach ($agrupados as $agrupador => $items) {
            $json = $this->armarJsonPublicacion($items);

            $response = Http::withToken(env('ML_ACCESS_TOKEN'))
                ->post('https://api.mercadolibre.com/items', $json);

            $body = $response->json();
            $statusCode = $response->status();

            // Registrar en la base
            foreach ($items as $item) {
                IntegracionPublicacion::create([
                    'cod_articulo'         => $item['cod_articulo'],
                    'cod_color_articulo'   => $item['cod_color_articulo'],
                    'plataforma'           => 'ML',
                    'external_id'          => $body['id'] ?? null,
                    'status'               => $body['status'] ?? 'error',
                    'sync_price'           => true,
                    'sync_stock'           => true,
                    'fecha_ultima_sync'    => Carbon::now(),
                    'observaciones'        => json_encode($body),
                ]);
            }

            $resultados[] = [
                'agrupador' => $agrupador,
                'status' => $statusCode,
                'body' => $body,
            ];
        }

        return $resultados;
    }

    private function armarJsonPublicacion($items)
    {
        $primer = $items->first();

        return [
            "title" => "Zapatillas POW SKATEB - Negro",
            "category_id" => "MLA378011",
            "price" => $primer['precio'],
            "currency_id" => "ARS",
            "available_quantity" => 1,
            "buying_mode" => "buy_it_now",
            "listing_type_id" => "gold_special",
            "condition" => "new",
            "description" => [
                "plain_text" => "Zapatillas técnicas para skateboarding, modelo POW SKATEB...",
            ],
            "pictures" => [
                ["source" => $primer['imagen_1_url']],
                ["source" => $primer['imagen_2_url'] ?? $primer['imagen_1_url']],
            ],
            "variations" => $items->map(function ($item) {
                return [
                    "attribute_combinations" => [
                        ["id" => "COLOR", "value_name" => $item['nombre_color']],
                        ["id" => "SIZE", "value_name" => $item['curva_talle']],
                    ],
                    "price" => $item['precio'],
                    "available_quantity" => $item['stock_total'],
                    "picture_ids" => [$item['imagen_1_url']],
                    "seller_custom_field" => "{$item['cod_articulo']}-{$item['cod_color_articulo']}",
                ];
            })->toArray()
        ];
    }
}
