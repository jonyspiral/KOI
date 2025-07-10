<?php
use Illuminate\Support\Facades\Http;
use App\Models\MlVariante;
use App\Services\Mlibre\MlibreTokenService;

// ID interno del registro que querés testear
$id = 123; // ⬅️ cambialo por el ID real en tu base

// Buscar variante
$v = MlVariante::find($id);
if (!$v) {
    dd('❌ No se encontró la variante con ID: ' . $id);
}

// Acceso a token
$token = app(MlibreTokenService::class)->getValidAccessToken();

// Construcción del endpoint
if ($v->variation_id) {
    $url = "https://api.mercadolibre.com/items/{$v->ml_id}/variations/{$v->variation_id}";
} else {
    $url = "https://api.mercadolibre.com/items/{$v->ml_id}";
}

// Payload
$data = ['seller_custom_field' => $v->seller_custom_field];

// Envío del PUT
$response = Http::withToken($token)->put($url, $data);

// Resultado
if ($response->ok()) {
    dd("✅ SCF actualizado correctamente", $response->json());
} else {
    dd("❌ Error al actualizar SCF", [
        'status' => $response->status(),
        'body'   => $response->body()
    ]);
}
