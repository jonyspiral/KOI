<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Mlibre\MlibreTokenService;

class DescargarPublicacionesML extends Command
{
    protected $signature = 'mlibre:descargar-publicaciones';
    protected $description = 'Descarga todas las publicaciones activas de Mercado Libre y guarda los JSON localmente';

    public function handle()
{
    $this->info('🔐 Obteniendo access_token válido...');
    $tokenService = app(MlibreTokenService::class);

    try {
        $accessToken = $tokenService->getValidAccessToken();
    } catch (\Exception $e) {
        $this->error("❌ Error al obtener token: " . $e->getMessage());
        return 1;
    }

    $userRes = Http::withToken($accessToken)->get("https://api.mercadolibre.com/users/me");

    if (!$userRes->ok()) {
        $this->error("❌ Error obteniendo datos de usuario: " . $userRes->body());
        return 1;
    }

    $userId = $userRes->json('id');
    $this->info("👤 Usuario ML: $userId");

    $offset = 0;
    $limit = 50;
    $total = 1;

    $this->info('🚀 Descargando publicaciones (activas, pausadas, cerradas)...');

    while ($offset < $total) {
        $listRes = Http::withToken($accessToken)->get("https://api.mercadolibre.com/users/{$userId}/items/search", [
            'status' => 'active,paused,closed',
            'offset' => $offset,
            'limit' => $limit,
        ]);

        if (!$listRes->ok()) {
            $this->error("❌ Error en listado: " . $listRes->body());
            return 1;
        }

        $data = $listRes->json();
        $total = $data['paging']['total'];
        $ids = $data['results'];

        foreach ($ids as $itemId) {
            $url = "https://api.mercadolibre.com/items/{$itemId}?include_attributes=all&include_variations=true";
            $itemRes = Http::withToken($accessToken)->get($url);

            if ($itemRes->ok()) {
                $json = json_encode($itemRes->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                Storage::put("mlibre/items/{$itemId}.json", $json);
                $this->line("✅ Guardado: {$itemId}");
            } else {
                $this->warn("⚠️  No se pudo obtener {$itemId}: " . $itemRes->status());
            }
        }

        $offset += $limit;
    }

    $this->info("🎉 Proceso finalizado. Archivos en /storage/app/mlibre/items/");
    return 0;
}

}
