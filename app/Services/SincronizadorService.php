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

    public function syncCreate(string $modeloNombre, array $data, string $destino = 'default', bool $debug = false): bool
{
    $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
    $tabla = (new $modelo)->getTable();

    // 🔍 Formatear datos para SQL Server
    $datos = $this->formatearParaSqlServer($data, $modeloNombre);

    // 🧱 Armar SQL dinámico
    $campos = array_keys($datos);
    $valores = array_values($datos);

    $camposStr = implode(', ', $campos);
    $valoresStr = implode(', ', array_map(function ($v) {
        return ($v instanceof \Illuminate\Database\Query\Expression || str_starts_with($v, 'CAST('))
            ? $v instanceof \Illuminate\Database\Query\Expression
                ? $v->getValue(\DB::connection()->getQueryGrammar())
                : $v
            : (is_null($v) ? 'NULL' : "'$v'");
    }, $valores));

    $sql = "INSERT INTO {$tabla} ({$camposStr}) VALUES ({$valoresStr})";

    // 🔍 Mostrar si estás debugueando
    if ($debug) {
        dd([
            '🧪 SQL generado' => $sql,
            '📦 Datos originales' => $data,
            '🧱 Formateado para SQL Server' => $datos,
        ]);
    }

    try {
        DB::connection($this->getConexionDestino($destino))->statement($sql);
        \Log::info("✅ Sincronización (create) con SQL Server exitosa: " . $sql);
        return true;
    } catch (\Throwable $e) {
        \Log::debug("🧪 SQL generado: {$sql}");
        \Log::error("❌ Error al sincronizar (create) con SQL Server: " . $e->getMessage());
        return false;
    }
}
public function syncUpdate(string $modeloNombre, array $data, string $campoClave, string $destino = 'default'): bool
{
    $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
    $tabla = (new $modelo)->getTable();

    // 🔍 Formatear datos para SQL Server (CAST, NULLs, etc.)
    $datos = $this->formatearParaSqlServer($data, $modeloNombre);

    // Validar existencia del campo clave
    if (!isset($data[$campoClave])) {
        \Log::error("❌ syncUpdate: Campo clave '{$campoClave}' no encontrado en el array de datos.");
        return false;
    }

    $valorClave = $data[$campoClave];
    unset($datos[$campoClave]); // se mueve al WHERE

    // Armar pares campo=valor
    $sets = [];
    foreach ($datos as $campo => $valor) {
        if ($valor instanceof \Illuminate\Database\Query\Expression) {
            $sets[] = "{$campo} = " . $valor->getValue(\DB::connection()->getQueryGrammar());
        } else {
            $sets[] = "{$campo} = " . (is_null($valor) ? 'NULL' : "'$valor'");
        }
    }

    // Escapar valor clave si es string
    $valorClaveSql = is_numeric($valorClave) ? $valorClave : "'" . str_replace("'", "''", $valorClave) . "'";

    $sql = "UPDATE {$tabla} SET " . implode(', ', $sets) . " WHERE {$campoClave} = {$valorClaveSql}";

    // 🧪 Traza
    \Log::debug('🧪 SQL generado UPDATE:', [
        'tabla' => $tabla,
        'clave' => $campoClave,
        'valor_clave' => $valorClave,
        'sql' => $sql
    ]);

    try {
        \DB::connection($this->getConexionDestino($destino))->statement($sql);
        \Log::info("✅ Sincronización (update) con SQL Server exitosa: {$valorClave}");
        return true;
    } catch (\Throwable $e) {
        \Log::error("❌ Error al sincronizar (update) con SQL Server: " . $e->getMessage());
        return false;
    }
}



public function syncDelete(string $modeloNombre, array $data, string $campoClave, string $destino = 'default'): bool
{
    $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
    $tabla = (new $modelo)->getTable();

    $valorClave = $data[$campoClave] ?? null;

    if (empty($valorClave)) {
        \Log::error("❌ No se encontró valor para la clave primaria en delete.");
        return false;
    }

    // Escapar comillas simples
    $valorEscapado = str_replace("'", "''", $valorClave);

    // Construir SQL seguro con CAST por compatibilidad
    $sql = "DELETE FROM {$tabla} WHERE CAST({$campoClave} AS VARCHAR(255)) = '{$valorEscapado}'";

    try {
        DB::connection($this->getConexionDestino($destino))->statement($sql);
        \Log::info("🗑️ Eliminación física en SQL Server exitosa: {$valorClave}");
        return true;
    } catch (\Throwable $e) {
        \Log::error("❌ Error al eliminar físicamente en SQL Server: " . $e->getMessage());
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
public function formatearParaSqlServer(array $data, string $modeloNombre): array
{
    $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
    $configPath = resource_path("meta_abms/config_form_" . class_basename($modeloNombre) . ".json");
    $config = \File::exists($configPath) ? json_decode(\File::get($configPath), true) : [];

    $camposConfig = $config['campos'] ?? [];
    $datos = [];

    foreach ($camposConfig as $campo => $meta) {
        if (!($meta['sync'] ?? false)) continue;

        $valor = $data[$campo] ?? null;
        $tipo = $meta['input_type'] ?? 'text';

        switch ($tipo) {
            case 'date':
                
                if (!empty($valor)) {
                    if ($valor instanceof \Carbon\Carbon) {
                        $valor = $valor->format('Y-m-d H:i:s');
                    }
                    $datos[$campo] = DB::raw("CAST('{$valor}' AS DATETIME)");
                } else {
                    $datos[$campo] = DB::raw("NULL");
                }
                break;

            case 'number':
                $datos[$campo] = is_numeric($valor)
                    ? DB::raw("CAST({$valor} AS INT)")
                    : DB::raw("NULL");
                break;

            case 'decimal':
            case 'moneda':
                $datos[$campo] = is_numeric($valor)
                    ? DB::raw("CAST(" . number_format((float) $valor, 2, '.', '') . " AS DECIMAL(10,2))")
                    : DB::raw("NULL");
                break;

            case 'float':
                $datos[$campo] = is_numeric($valor)
                    ? DB::raw("CAST({$valor} AS FLOAT)")
                    : DB::raw("NULL");
                break;

            case 'checkbox':
                $valor = in_array($valor, ['S', 'N']) ? $valor : 'N';
                $datos[$campo] = DB::raw("'" . str_replace("'", "''", $valor) . "'");
                break;

            case 'text':
            case 'select_list':
            case 'email':
            case 'tel':
            case 'telefono':
            case 'color':
            case 'url':
            case 'file':
                $datos[$campo] = is_null($valor)
                    ? DB::raw("NULL")
                    : DB::raw("'" . str_replace("'", "''", $valor) . "'");
                break;

            default:
                $datos[$campo] = is_null($valor)
                    ? DB::raw("NULL")
                    : DB::raw("'" . str_replace("'", "''", $valor) . "'");
        }
    }

    // 🔑 Remover 'id' si no está definido como clave real en SQL Server
    $primaryKeySql = $modelo::$primaryKeySql ?? [];
    if (!in_array('id', $primaryKeySql)) {
        unset($datos['id']);
    }

    return $datos;
}



}

