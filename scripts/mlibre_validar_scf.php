<?php
// mlibre_validar_scf.php - Verifica SCF de ml_variantes contra var_sku de sku_variantes y genera CSV con título

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Validando SCF en ml_variantes contra SKU en sku_variantes...\n\n";

// Archivo CSV de salida
$fecha = date('Ymd_His');
$csvFile = __DIR__ . "/scf_validacion_{$fecha}.csv";
$fp = fopen($csvFile, 'w');

// Encabezado CSV
fputcsv($fp, ['Tipo', 'ID ml_variantes', 'ml_id', 'variation_id', 'SCF', 'var_sku encontrado', 'Título/Nombre']);

// 1️⃣ SCF NULOS
$scfNulos = DB::table('ml_variantes as mv')
    ->leftJoin('ml_publicaciones as mp', 'mv.ml_id', '=', 'mp.ml_id')
    ->whereNull('mv.seller_custom_field')
    ->orWhere('mv.seller_custom_field', '')
    ->select(
        'mv.id',
        'mv.ml_id',
        'mv.variation_id',
        'mv.seller_custom_field',
        'mv.titulo',
        'mp.ml_name'
    )
    ->get();

if ($scfNulos->count()) {
    echo "⚠️ SCF NULOS (" . $scfNulos->count() . " registros)\n";
    foreach ($scfNulos as $row) {
        $titulo = $row->titulo ?: $row->ml_name ?: '';
        echo " - ID: {$row->id} | ml_id: {$row->ml_id} | variation_id: {$row->variation_id} | {$titulo}\n";
        fputcsv($fp, ['SCF_NULO', $row->id, $row->ml_id, $row->variation_id, null, null, $titulo]);
    }
} else {
    echo "✅ No se encontraron SCF nulos.\n\n";
}

// 2️⃣ SCF HUÉRFANOS
$scfHuerfanos = DB::table('ml_variantes as mv')
    ->leftJoin('sku_variantes as sv', 'mv.seller_custom_field', '=', 'sv.var_sku')
    ->leftJoin('ml_publicaciones as mp', 'mv.ml_id', '=', 'mp.ml_id')
    ->whereNotNull('mv.seller_custom_field')
    ->whereNull('sv.var_sku')
    ->select(
        'mv.id',
        'mv.ml_id',
        'mv.variation_id',
        'mv.seller_custom_field',
        'mv.titulo',
        'mp.ml_name'
    )
    ->get();

if ($scfHuerfanos->count()) {
    echo "⚠️ SCF HUÉRFANOS (" . $scfHuerfanos->count() . " registros)\n";
    foreach ($scfHuerfanos as $row) {
        $titulo = $row->titulo ?: $row->ml_name ?: '';
        echo " - ID: {$row->id} | ml_id: {$row->ml_id} | SCF: {$row->seller_custom_field} | {$titulo}\n";
        fputcsv($fp, ['SCF_HUERFANO', $row->id, $row->ml_id, $row->variation_id, $row->seller_custom_field, null, $titulo]);
    }
} else {
    echo "✅ No se encontraron SCF huérfanos.\n\n";
}

fclose($fp);

echo "📁 CSV generado: {$csvFile}\n";
