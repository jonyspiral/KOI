<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SincronizadorService
{
    protected string $conexion = 'sqlsrv_koi'; // conexión fija por ahora

    public function syncCreate(string $modeloNombre, array $data, string $destino = 'default')
{
    $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
    $tabla = (new $modelo)->getTable();

    // Transformamos solo las fechas al formato que SQL Server acepta
    $datos = $this->transformarFechas($data);

    // Quitamos campos que no existen en SQL Server
    unset($datos['id']);

    // Logueamos la query antes de ejecutarla (opcional)
    \Log::info('Query ejecutada en SQL Server:', [
        'query' => 'insert into ' . $tabla,
        'bindings' => $datos
    ]);

    // Ejecutamos
    return DB::connection($this->getConexionDestino($destino))
        ->table($tabla)
        ->insert($datos);
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

