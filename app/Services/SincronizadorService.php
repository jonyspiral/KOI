<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class SincronizadorService
{
    protected string $conexion = 'sqlsrv_koi'; // conexión fija por ahora

    public function syncCreate(string $modeloNombre, array $data, string $destino = 'default')
{
    $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
    $tabla = (new $modelo)->getTable();

    // Obtener campos sincronizables desde el archivo config_form
    $configPath = resource_path("meta_abms/config_form_" .(class_basename($modeloNombre)) . ".json");

    $config = \File::exists($configPath) ? json_decode(\File::get($configPath), true) : [];

    $campos = collect($config['campos'] ?? [])
        ->filter(fn($c) => $c['sync'] ?? false)
        ->keys()
        ->toArray();
    
    // Filtrar solo los campos sincronizables
  
    $datos = array_intersect_key($data, array_flip($campos));

    // Si no es parte del primaryKeySql, lo removemos
    $primaryKeySql = $modelo::$primaryKeySql ?? [];
    if (!in_array('id', $primaryKeySql)) {
        unset($datos['id']);
    }

    // Convertir fechas a formato compatible con SQL Server
    foreach (['created_at', 'updated_at'] as $campo) {
        if (isset($datos[$campo]) && !empty($datos[$campo])) {
            $datos[$campo] = \DB::raw("CAST('" . date('Y-m-d H:i:s', strtotime($datos[$campo])) . "' AS DATETIME)");
        }
    }

    try {
        \DB::connection($this->getConexionDestino($destino))->table($tabla)->insert($datos);
        \Log::info("✅ Sincronización (create) con SQL Server exitosa: " . json_encode($datos));
        return true;
    } catch (\Throwable $e) {
        \Log::error("❌ Error al sincronizar (create) con SQL Server: " . $e->getMessage());
        return false;
    }
}


    public function syncUpdate(string $tabla, string $campoClave, string $valorClave, array $datos): bool
    {
        try {
            return DB::connection($this->conexion)
                ->table($tabla)
                ->where($campoClave, $valorClave)
                ->update($this->transformarFechas($datos));
        } catch (\Throwable $e) {
            Log::error("❌ [SYNC-UPDATE] {$tabla} - " . $e->getMessage());
            return false;
        }
    }

    public function syncDelete(string $tabla, string $campoClave, string $valorClave): bool
    {
        try {
            $sql = "DELETE FROM {$tabla} WHERE {$campoClave} = ?";
            return DB::connection($this->conexion)->statement($sql, [$valorClave]);
        } catch (\Throwable $e) {
            Log::error("❌ [SYNC-DELETE] {$tabla} - " . $e->getMessage());
            return false;
        }
    }

    protected function transformarFechas(array $datos): array
    {
        foreach (['created_at', 'updated_at'] as $campo) {
            if (isset($datos[$campo]) && !empty($datos[$campo])) {
                $fecha = date('Y-m-d H:i:s', strtotime($datos[$campo]));
                $datos[$campo] = DB::raw("CAST('$fecha' AS DATETIME)");
            }
        }
    
        return $datos;
    }
    private function getConexionDestino(string $destino): string
{
    return match ($destino) {
        'desarrollo', 'sqlsrv_koi' => 'sqlsrv_koi',
        default => $this->conexion, // usa la propiedad protegida si no se especifica otra
    };
}
}

