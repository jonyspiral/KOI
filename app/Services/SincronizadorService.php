<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class SincronizadorService
{
    public function syncCreate(string $modeloNombre, array $data, string $destino = 'default')
    {
        $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
        return $modelo::on($this->getConexionDestino($destino))->create($data);
    }

    public function syncUpdate(string $modeloNombre, mixed $clave, array $data, string $destino = 'default')
{
    $modeloClass = "\\App\\Models\\Sql\\{$modeloNombre}";
    $modelo = new $modeloClass;

    if (!method_exists($modelo, 'fieldsMeta')) {
        throw new \Exception("El modelo {$modeloNombre} no define fieldsMeta()");
    }

    $meta = $modeloClass::fieldsMeta();
    $primaryKey = collect($meta)->filter(fn($cfg) => $cfg['primary'] ?? false)->keys()->first();

    if (!$primaryKey) {
        throw new \Exception("No se encontró clave primaria en fieldsMeta() de {$modeloNombre}");
    }

    $campos = array_keys($data); // campos recibidos para sincronizar

    // Buscar en la base destino (ej: desarrollo)
    $registro = $modeloClass::on($this->getConexionDestino($destino))
                ->select($campos)
                ->where($primaryKey, $clave)
                ->first();

    return $registro ? $registro->update($data) : null;
}

    public function syncDelete(string $modeloNombre, int $id, string $destino = 'default')
    {
        $modelo = "\\App\\Models\\Sql\\{$modeloNombre}";
        return $modelo::on($this->getConexionDestino($destino))->where('id', $id)->delete();
    }

    private function getConexionDestino(string $destino)
    {
        return match ($destino) {
            //'encinitas' => 'sqlsrv_encinitas',
           // 'spiral'    => 'sqlsrv_spiral',
            'desarrollo' => 'sqlsrv_koi',
            default     => 'sqlsrv_koi',
        };
    }
}
