<?php
// mlibre_check_individual.php - Comprobación de una publicación específica

// 1️⃣ Cargar Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\Mlibre\MlibreTokenService;

// Configura el ID de la publicación que quieres comprobar
$publicacionId = 'MLA2208551010';

// 2️⃣ Obtener token válido
echo "Obteniendo token de ML...\n";
$token = app(MlibreTokenService::class)->getValidAccessToken();

// 3️⃣ Consultar detalles en ML
echo "Consultando detalles de {$publicacionId} en ML...\n";
$itemResp = Http::withHeaders([
    'Authorization' => "Bearer {$token}",
    'Accept'        => 'application/json',
])->get("https://api.mercadolibre.com/items/{$publicacionId}")->json();

// Mostrar info básica
if (empty($itemResp['id'])) {
    echo "⚠️ No se encontró la publicación en ML o no tienes permiso para verla.\n";
    exit;
}

echo "📌 ID: {$itemResp['id']}\n";
echo "📄 Título: " . ($itemResp['title'] ?? 'N/A') . "\n";
echo "📍 Estado: " . ($itemResp['status'] ?? 'N/A') . "\n";
echo "📦 Variantes: " . count($itemResp['variations'] ?? []) . "\n\n";

// 4️⃣ Revisar cada variante contra KOI2
foreach ($itemResp['variations'] ?? [] as $var) {
    $varId = $var['id'] ?? '';
    $scf   = $var['seller_custom_field'] ?? '';

    $exists = false;
    if ($scf) {
        $exists = DB::table('ml_variantes')
            ->where('seller_custom_field', $scf)
            ->exists();
    } else {
        $exists = DB::table('ml_variantes')
            ->where('ml_id', $publicacionId)
            ->where('variation_id', $varId)
            ->exists();
    }

    if ($exists) {
        echo "   ✅ [OK] {$publicacionId} (var: {$varId}) SCF: {$scf}\n";
    } else {
        echo "   ➕ [NUEVO] {$publicacionId} (var: {$varId}) SCF: {$scf}\n";
    }
}
