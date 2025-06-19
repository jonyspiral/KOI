<?php

use App\Models\MlPublicacion;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\File;

$path = storage_path('app/private/mlibre/items');
$files = File::files($path);

if (empty($files)) {
    echo "No se encontraron archivos JSON en: $path\n";
    exit;
}

foreach ($files as $file) {
    $json = json_decode(file_get_contents($file->getPathname()), true);
    $mlId = $json['id'] ?? null;

    if (!$mlId) {
        echo "❌ Archivo inválido: " . $file->getFilename() . "\n";
        continue;
    }

    $familyId = $json['family_id'] ?? null;
    $familyName = $json['family_name'] ?? null;
    $logisticType = $json['shipping']['logistic_type'] ?? null;

    if ($familyId || $familyName || $logisticType) {
        $publi = MlPublicacion::where('ml_id', $mlId)->first();

        if ($publi) {
            $publi->update([
                'family_id' => $familyId,
                'family_name' => $familyName,
                'logistic_type' => $logisticType,
            ]);
            echo "✅ Actualizada publicación $mlId\n";
        } else {
            echo "⚠️ No existe publicación en DB para $mlId\n";
        }
    }
}

echo "🏁 Proceso finalizado.\n";

