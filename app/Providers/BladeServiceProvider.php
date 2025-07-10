<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 📌 Directiva para generar TH con ordenamiento dinámico
        Blade::directive('sortableth', function ($expression) {
            [$campo, $label] = array_map('trim', explode(',', str_replace(['(', ')', "'"], '', $expression)));

            return "<?php
                \$dir = request('sort') === '$campo' && request('dir') === 'asc' ? 'desc' : 'asc';
                \$icon = request('sort') === '$campo' ? (request('dir') === 'asc' ? '⬆️' : '⬇️') : '';
                \$url = route(Route::currentRouteName(), array_merge(request()->all(), ['sort' => '$campo', 'dir' => \$dir]));
            ?>
            {!! '<a href=\"' . \$url . '\">' . e('$label') . ' ' . \$icon . '</a>' !!}";
        });

        // 📌 Directiva para generar inputs de filtros por columna
        Blade::directive('filterInput', function ($campo) {
            $campo = trim(str_replace(['(', ')', "'"], '', $campo));

            return "<?php
                \$valor = request('$campo');
            ?>
            <input type=\"text\" name=\"$campo\" value=\"<?= e(\$valor) ?>\" class=\"form-control form-control-sm\">";
        });

        Blade::directive('filterSelect', function ($expression) {
    return "<?php
        list(\$campo, \$opciones) = [$expression];
        \$valorActual = request(\$campo);
        echo '<select name=\"' . \$campo . '\" class=\"form-control form-control-sm\">';
        echo '<option value=\"\">—</option>';
        foreach (\$opciones as \$valor => \$texto) {
            \$selected = \$valor == \$valorActual ? 'selected' : '';
            echo '<option value=\"' . \$valor . '\" ' . \$selected . '>' . \$texto . '</option>';
        }
        echo '</select>';
    ?>";
});
    Blade::directive('ordenIcon', function ($campo) {
    return "<?php
        if (request('sort') === $campo) {
            echo request('dir') === 'asc' ? '⬆️' : '⬇️';
        }
    ?>";
});
Blade::directive('filterSelectMultiple', function ($expresion) {
    // Parseo seguro con list()
    [$campo, $opciones] = explode(',', preg_replace("/[\(\)\\\"\']/", '', $expresion));

    return <<<BLADE
<?php
    \$valores = request()->input(trim('$campo'), []);
    echo '<select name="' . trim('$campo') . '[]" class="form-control form-control-sm select2" multiple>';
    foreach (trim($opciones) as \$key => \$texto) {
        \$selected = in_array(\$key, (array) \$valores) ? 'selected' : '';
        echo "<option value=\"{\$key}\" {\$selected}>{\$texto}</option>";
    }
    echo '</select>';
?>
BLADE;
});

Blade::directive('filterInputLike', function ($campo) {
    return "<?php echo view('components.filtros.input-like', ['campo' => $campo]); ?>";
});

    }

    public function register()
    {
        //
    }
}
