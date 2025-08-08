<?php
// mlibre_import_nuevos.php - Inserta nuevos registros en ml_publicaciones y ml_variantes con atributos extra

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Mlibre\MlibreTokenService;

// 🔹 Función segura para codificar JSON para MySQL
function safeJsonEncode($data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $json;
    }
    Log::warning('JSON inválido al guardar en raw_json', [
        'error' => json_last_error_msg(),
        'data_sample' => mb_substr(print_r($data, true), 0, 500)
    ]);
    return '{}';
}

$LIMITE_POR_PAGINA = 50;
$LIMITE_POR_LOTE   = 5;

echo "Obteniendo token de ML...\n";
$token = app(MlibreTokenService::class)->getValidAccessToken();
$userId = env('MLIBRE_USER_ID');

if (!$userId) {
    echo "ERROR: No está configurado MLIBRE_USER_ID en .env\n";
    exit;
}

$totalPublicaciones = 0;
$nuevosEncontrados  = 0;
$insertadosVariantes = 0;
$offset = 0;

while (true) {
    $searchUrl = "https://api.mercadolibre.com/users/{$userId}/items/search?status=active&limit={$LIMITE_POR_PAGINA}&offset={$offset}";
    $searchResp = Http::withHeaders([
        'Authorization' => "Bearer {$token}",
        'Accept'        => 'application/json',
    ])->get($searchUrl)->json();

    $ids = $searchResp['results'] ?? [];
    if (empty($ids)) break;

    $totalPublicaciones += count($ids);
    echo "\n📦 Página con " . count($ids) . " publicaciones (offset: {$offset})\n";

    foreach (array_chunk($ids, $LIMITE_POR_LOTE) as $lote) {
        foreach ($lote as $itemId) {
            echo "🔍 Consultando detalles de: {$itemId}...\n";

            $itemResp = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept'        => 'application/json',
            ])->get("https://api.mercadolibre.com/items/{$itemId}")->json();

            if (empty($itemResp['id'])) {
                echo "⚠️ No se pudo obtener detalles de {$itemId}\n";
                continue;
            }

            // 1️⃣ Insertar o actualizar ml_publicaciones
            $pubExiste = DB::table('ml_publicaciones')->where('ml_id', $itemId)->exists();

            $publicacionData = [
                'ml_id'         => $itemId,
                'status'        => $itemResp['status'] ?? null,
                'logistic_type' => $itemResp['shipping']['logistic_type'] ?? null,
                'family_id'     => $itemResp['family_id'] ?? null,
                'family_name'   => $itemResp['family_name'] ?? null,
                'ml_name'       => $itemResp['title'] ?? null,
                'mlibre_precio' => $itemResp['price'] ?? null,
                'mlibre_stock'  => $itemResp['available_quantity'] ?? null,
                'raw_json'      => safeJsonEncode($itemResp),
                'updated_at'    => now(),
            ];

            if ($pubExiste) {
                DB::table('ml_publicaciones')->where('ml_id', $itemId)->update($publicacionData);
            } else {
                $publicacionData['created_at'] = now();
                DB::table('ml_publicaciones')->insert($publicacionData);
            }

            // 2️⃣ Procesar variaciones
            $variations = $itemResp['variations'] ?? [];

            // 🔹 Con variaciones
            if (!empty($variations)) {
                foreach ($variations as $var) {
                    $varId = $var['id'] ?? null;
                    $scf   = $var['seller_custom_field'] ?? null;
                    $sku   = null;

                    // Extraer SKU
                    foreach ($var['attributes'] ?? [] as $attr) {
                        if (($attr['id'] ?? '') === 'SELLER_SKU' && !empty($attr['value_name'])) {
                            $sku = $attr['value_name'];
                            break;
                        }
                    }

                    // Extraer color, talle, modelo
                    $color = null;
                    $talle = null;
                    $modelo = null;

                    foreach ($var['attribute_combinations'] ?? [] as $comb) {
                        if (in_array(strtoupper($comb['id']), ['COLOR', 'COLOR_PRINCIPAL'])) {
                            $color = $comb['value_name'] ?? null;
                        }
                        if (in_array(strtoupper($comb['id']), ['SIZE', 'TALLE'])) {
                            $talle = $comb['value_name'] ?? null;
                        }
                    }
                    foreach ($var['attributes'] ?? [] as $attr) {
                        if (strtoupper($attr['id']) === 'MODEL') {
                            $modelo = $attr['value_name'] ?? null;
                        }
                    }

                    // Verificar existencia en ml_variantes
                    $exists = $scf
                        ? DB::table('ml_variantes')->where('seller_custom_field', $scf)->exists()
                        : DB::table('ml_variantes')->where('ml_id', $itemId)->where('variation_id', $varId)->exists();

                    if ($exists) {
                        echo "   ✅ [OK] {$itemId} (var: {$varId}) SCF: {$scf}\n";
                        continue;
                    }

                    echo "   ➕ [NUEVO] {$itemId} (var: {$varId}) SCF: {$scf} SKU: {$sku} Color: {$color} Talle: {$talle} Modelo: {$modelo}\n";
                    $nuevosEncontrados++;

                    DB::table('ml_variantes')->insert([
                        'ml_id'                  => $itemId,
                        'variation_id'           => $varId,
                        'product_number'         => null,
                        'seller_custom_field'    => $scf ?: ($sku ?: null),
                        'talle'                  => $talle,
                        'color'                  => $color,
                        'modelo'                 => $modelo,
                        'titulo'                 => $itemResp['title'] ?? null,
                        'seller_sku'             => $sku,
                        'precio'                 => $itemResp['price'] ?? null,
                        'stock'                  => $var['available_quantity'] ?? null,
                        'sync_status'            => 'N',
                        'vigente'                => 1,
                        'manual_price'           => 0,
                        'manual_stock'           => 0,
                        'stock_flex'             => null,
                        'stock_full'             => null,
                        'seller_custom_field_actual' => $scf ?: ($sku ?: null),
                        'sincronizado'           => 0,
                        'raw_json'               => safeJsonEncode($var),
                        'created_at'             => now(),
                        'updated_at'             => now(),
                    ]);
                    $insertadosVariantes++;
                }
            }
            // 🔹 Sin variaciones
            else {
                $exists = DB::table('ml_variantes')->where('ml_id', $itemId)->exists();

                if ($exists) {
                    echo "   ✅ [OK] {$itemId} (sin variaciones)\n";
                    continue;
                }

                $sku = null;
                $color = null;
                $talle = null;
                $modelo = null;

                foreach ($itemResp['attributes'] ?? [] as $attr) {
                    if (($attr['id'] ?? '') === 'SELLER_SKU' && !empty($attr['value_name'])) {
                        $sku = $attr['value_name'];
                    }
                    if (in_array(strtoupper($attr['id']), ['COLOR', 'COLOR_PRINCIPAL'])) {
                        $color = $attr['value_name'] ?? null;
                    }
                    if (in_array(strtoupper($attr['id']), ['SIZE', 'TALLE'])) {
                        $talle = $attr['value_name'] ?? null;
                    }
                    if (strtoupper($attr['id']) === 'MODEL') {
                        $modelo = $attr['value_name'] ?? null;
                    }
                }

                echo "   ➕ [NUEVO] {$itemId} (sin variaciones) SKU: {$sku} Color: {$color} Talle: {$talle} Modelo: {$modelo}\n";
                $nuevosEncontrados++;

                DB::table('ml_variantes')->insert([
                    'ml_id'                  => $itemId,
                    'variation_id'           => null,
                    'product_number'         => null,
                    'seller_custom_field'    => $sku ?: null,
                    'talle'                  => $talle,
                    'color'                  => $color,
                    'modelo'                 => $modelo,
                    'titulo'                 => $itemResp['title'] ?? null,
                    'seller_sku'             => $sku,
                    'precio'                 => $itemResp['price'] ?? null,
                    'stock'                  => $itemResp['available_quantity'] ?? null,
                    'sync_status'            => 'N',
                    'vigente'                => 1,
                    'manual_price'           => 0,
                    'manual_stock'           => 0,
                    'stock_flex'             => null,
                    'stock_full'             => null,
                    'seller_custom_field_actual' => $sku ?: null,
                    'sincronizado'           => 0,
                    'raw_json'               => safeJsonEncode($itemResp),
                    'created_at'             => now(),
                    'updated_at'             => now(),
                ]);
                $insertadosVariantes++;
            }
        }
    }

    $offset += $LIMITE_POR_PAGINA;
}

echo "\n✅ Revisión completada.\n";
echo "📊 Total publicaciones revisadas: {$totalPublicaciones}\n";
echo "➕ Nuevas detectadas: {$nuevosEncontrados}\n";
echo "📥 Nuevos insertados en ml_variantes: {$insertadosVariantes}\n";
