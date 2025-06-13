<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Services\Mlibre\MlibreTokenService;

class DescargarPublicacionML extends Command
{  protected $signature = 'mlibre:descargar-publicacion {ml_id}'; 
    protected $description = 'Descarga una publicación individual de Mercado Libre con atributos y variaciones';

    public function handle()
    {
        $mlId = $this->argument('ml_id');
        $this->info("🔍 Descargando publicación $mlId...");

        // Obtener token
        $tokenService = app(MlibreTokenService::class);

        try {
            $accessToken = $tokenService->getValidAccessToken();
        } catch (\Exception $e) {
            $this->error("❌ Error al obtener token: " . $e->getMessage());
            return 1;
        }

        // Hacer request con todos los datos posibles
        $url = "https://api.mercadolibre.com/items/{$mlId}?include_attributes=all&include_variations=true";
        $res = Http::withToken($accessToken)->get($url);

        if (!$res->ok()) {
            $this->error("❌ Error al descargar $mlId: " . $res->status());
            return 1;
        }

        // Guardar JSON en carpeta dedicada
        $json = json_encode($res->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::put("private/mlibre/items/{$mlId}.json", $json);

        $this->info("✅ Publicación guardada en storage/app/private/mlibre/items/{$mlId}.json");
        return 0;
    }
}
