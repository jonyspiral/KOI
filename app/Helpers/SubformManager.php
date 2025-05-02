<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SubformManager
{
    /**
     * Devuelve un array de subformularios configurados para el modelo padre.
     *
     * @param string $modeloPadre Nombre del modelo padre (ej: 'RutasProduccion')
     * @return array Lista de subformularios con carpeta_vistas completada
     */
    public static function getFromModeloPadre(string $modeloPadre): array
    {
        $rutaJson = resource_path("meta_abms/config_form_{$modeloPadre}.json");
    
        if (!file_exists($rutaJson)) {
            return [];
        }
    
        $config = json_decode(file_get_contents($rutaJson), true);
        $subformularios = $config['subformularios'] ?? [];
    
        foreach ($subformularios as $sub) {
            if (empty($sub['carpeta_vistas'])) {
                throw new \Exception("Falta definir 'carpeta_vistas' en el subformulario del modelo {$modeloPadre}");
            }
    
            $sub['ruta'] = strtolower(basename($sub['carpeta_vistas']));
        }
    
        return $subformularios;
    }
    public static function asegurarCarpetaVistas(array &$sub): void
{
    if (empty($sub['carpeta_vistas'])) {
        throw new \Exception("Falta 'carpeta_vistas' en el subformulario {$sub['modelo']}. Completalo desde el preview o manualmente en el JSON.");
    }
}
public static function getCamposFromModelo(string $modelo): array
{
    $rutaJson = resource_path("meta_abms/config_form_{$modelo}.json");

    if (!file_exists($rutaJson)) {
        return [];
    }

    $config = json_decode(file_get_contents($rutaJson), true);
    $campos = $config['campos'] ?? [];

    // Filtramos solo los campos que tienen "incluir = true"
    return array_filter($campos, fn($cfg) => !empty($cfg['incluir']));
}


}
