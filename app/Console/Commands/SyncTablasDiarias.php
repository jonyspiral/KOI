<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SyncTablasDiarias extends Command
{
    protected $signature = 'sync:tablas-diarias';

    protected $description = 'Ejecuta la sincronización completa de tablas clave desde SQL Server a MySQL';


   public function handle()
{
    $tablas = [
        'tipo_producto_stock',
        'lineas_productos',
        'articulos',
        'colores_por_articulo',
        // Agregá más si querés
    ];

    $this->info('🚀 Iniciando sincronización de tablas diarias...');

    foreach ($tablas as $tabla) {
        $this->info("🔄 Importando tabla: $tabla...");

        try {
            \Artisan::call('importar:tabla', [
    'nombre_tabla' => $tabla
]);

            $this->info("✅ Importación de $tabla completada.");
        } catch (\Exception $e) {
            $this->error("❌ Error importando $tabla: " . $e->getMessage());
        }
    }

    $this->info('🎯 Sincronización de tablas diarias completada.');
}
}