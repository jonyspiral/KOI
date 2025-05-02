<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class MenuHelper
{
    public static function obtenerMenu(): array
{
    $menu = [];

    $jsonFiles = glob(resource_path('meta_abms/config_form_*.json'));

    foreach ($jsonFiles as $file) {
        $config = json_decode(file_get_contents($file), true);
        $modelo = $config['modelo'] ?? null;
        $namespace = strtolower($config['namespace'] ?? 'general');

        if (!$modelo || !isset($config['menu'])) {
            continue;
        }

        $entradas = is_array($config['menu']) && isset($config['menu'][0])
                    ? $config['menu']
                    : [$config['menu']];

        foreach ($entradas as $m) {
            if (!($m['mostrar'] ?? false)) continue;

            $modulo = trim(strtolower($m['modulo'] ?? $namespace));
            $grupo = trim(ucfirst(strtolower($m['grupo'] ?? $namespace)));

            $menu[$modulo][$grupo][] = [
                'ruta' => "{$namespace}.abms." . Str::snake(Str::plural($modelo)) . ".index",
                'label' => $m['label'] ?? $modelo,
                'icon' => $m['icon'] ?? '',
                'posicion' => intval($m['posicion'] ?? 99),
            ];
        }
    }

    // Ordenar todo
    foreach ($menu as &$grupos) {
        foreach ($grupos as &$items) {
            usort($items, fn($a, $b) => $a['posicion'] <=> $b['posicion']);
        }
        ksort($grupos); // grupos dentro del módulo
    }

    ksort($menu); // módulos principales

    return $menu;
}

}
