<?php

namespace App\Services\Mlibre;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ValidateMlVariationsService
{
    protected array $cache = [];

    public function existsInML(string $mlId, int $variationId, string $token): bool
    {
        // Si ya cacheamos las variantes de este ítem, reutilizamos
        if (!isset($this->cache[$mlId])) {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)',
                'Accept'        => 'application/json',
            ])->get("https://api.mercadolibre.com/items/{$mlId}");

            if (!$response->ok()) {
                return false; // Error de conexión o ítem inexistente
            }

            $data = $response->json();
            $this->cache[$mlId] = collect($data['variations'] ?? [])->pluck('id')->all();
        }

        return in_array($variationId, $this->cache[$mlId]);
    }
}
