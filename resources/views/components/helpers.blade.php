{{-- 📄 helpers.blade.php
// Función: Directivas Blade reutilizables para columnas ordenables y filtros en tablas KOI
// Fecha: 2025-07-09
// Fuente: propuesta consolidada de filtros y sorters KOI
// Última edición del canvas: (NO REGISTRADO)
--}}

@php
    // 📌 Directiva para cabecera ordenable con ícono de orden
    if (!function_exists('sortableth')) {
        Blade::directive('sortableth', function ($expressionRaw) {
            $expression = str_replace(['(', ')', ' '], '', $expressionRaw);
            [$campo, $titulo] = explode(',', $expression);
            $campo = trim($campo, "'\"");
            $titulo = trim($titulo, "'\"");
            $dir = "<?php echo request('sort') === '{$campo}' && request('dir') === 'asc' ? 'desc' : 'asc'; ?>";
            $icon = "<?php echo request('sort') === '{$campo}' ? (request('dir') === 'asc' ? '⬆️' : '⬇️') : ''; ?>";
            $url = "<?php echo route(\Illuminate\Support\Facades\Route::currentRouteName(), array_merge(request()->all(), ['sort' => '{$campo}', 'dir' => $dir])); ?>";
            return "<?php echo '<th><a href=\"' . $url . '\">' . '{$titulo}' . ' ' . $icon . '</a></th>'; ?>";
        });
    }

    // 📌 Directiva para filtro de texto en el <thead>
    if (!function_exists('filterInput')) {
        Blade::directive('filterInput', function ($campoRaw) {
            $campo = trim($campoRaw, "'\"");
            return "<?php echo '<th><input type=\"text\" name=\"{$campo}\" value=\"' . request('{$campo}') . '\" class=\"form-control form-control-sm bg-white text-dark\"></th>'; ?>";
        });
    }
@endphp
