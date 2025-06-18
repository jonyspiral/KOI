<?php

use Illuminate\Support\Facades\DB;
use App\Models\MlVariante;
use PhpOffice\PhpSpreadsheet\IOFactory;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 📁 Ruta al archivo Excel
$excelFile = __DIR__ . '/scf_import.xlsx'; // <-- Cambiá el nombre si es distinto

echo "📥 Leyendo archivo: $excelFile\n";

// 🧾 Leer el Excel
$spreadsheet = IOFactory::load($excelFile);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

$contador = 0;

foreach ($rows as $index => $row) {
    if ($index === 0) continue; // saltear encabezado

    $product_number = trim($row[3]); // columna D
    $scf = trim($row[4]);            // columna E (suponiendo SCF ahí, ajustá si está en otra)

    if ($product_number && $scf) {
        $affected = MlVariante::where('product_number', $product_number)
            ->update(['seller_custom_field' => $scf]);

        if ($affected) {
            echo "✅ Actualizado: $product_number con SCF: $scf\n";
            $contador++;
        } else {
            echo "❌ No encontrado: $product_number\n";
        }
    }
}

echo "🔚 Finalizado. Total actualizados: $contador\n";
