<?php

namespace App\Http\Controllers\__NAMESPACE__;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\__MODELO__;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class __MODELO__Controller extends Controller
{
   public function index(Request $request)
{
    $configPath = resource_path("meta_abms/config_form___MODELO__.json");
    $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    $columnas = array_keys($campos);
    $modelo = '__MODELO__';

    $query = __MODELO__::query();

    // 🔍 Búsqueda simple sobre columnas visibles
    if ($request->filled('buscar')) {
        $search = $request->input('buscar');
        $query->where(function ($q) use ($columnas, $search) {
            foreach ($columnas as $col) {
                $q->orWhere($col, 'LIKE', "%{$search}%");
            }
        });
    }

    $registros = $query->get();

    // 🔁 Reemplazar campos tipo select con valor mostrado
    foreach ($registros as $registro) {
        foreach ($campos as $campo => $meta) {
            if (($meta['input_type'] ?? null) === 'select' &&
                !empty($meta['referenced_table']) &&
                !empty($meta['referenced_column']) &&
                !empty($meta['referenced_label'])
            ) {
                $tabla = $meta['referenced_table'];
                $columna = $meta['referenced_column'];
                $label = $meta['referenced_label'];

                try {
                    $valorRelacionado = DB::table($tabla)
                        ->where($columna, $registro->$campo)
                        ->value($label);

                    // Mostrar valor de texto en lugar del código
                    $registro->$campo = $valorRelacionado ?? $registro->$campo;
                } catch (\Throwable $e) {
                    logger()->warning("Error mostrando '$campo' (select): " . $e->getMessage());
                }
            }
        }
    }

    return view('__CARPETA_VISTAS__.index', compact('registros', 'campos', 'columnas', 'modelo'));
}



    public function create()
    {
        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = '__MODELO__';

        $siguiente = [];
        $opciones = [];
        $labels = [];
        $defaults = [];

            foreach ($campos as $campo => $meta) {
                $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
                $defaults[$campo] = $meta['default'] ?? '';

                if (($meta['input_type'] ?? null) === 'autonumerico') {
                    $siguiente[$campo] = __MODELO__::max($campo) + 1;
                }

                if (
                    ($meta['input_type'] ?? null) === 'select' &&
                    !empty($meta['referenced_table']) &&
                    !empty($meta['referenced_label']) &&
                    !empty($meta['referenced_column'])
                ) {
                    $tabla = $meta['referenced_table'];
                    $columna = $meta['referenced_column'];
                    $label = $meta['referenced_label'];
                
                    try {
                        $opciones["{$campo}_opciones"] = DB::table($tabla)
                            ->select($columna, $label)
                            ->orderBy($label)
                            ->get();
                    } catch (\Throwable $e) {
                        $opciones["{$campo}_opciones"] = collect(); // Evita errores si la tabla no existe
                        logger()->error("Error al cargar opciones para $campo desde $tabla: " . $e->getMessage());
                    }
                }
            }

            return view('__CARPETA_VISTAS__.create', compact(
                'campos',
                'siguiente',
                'modelo',
                'labels',
                'defaults',
                'opciones'
            ));
            
    }

    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        foreach ($campos as $campo => $meta) {
            if (($meta['input_type'] ?? null) === 'autonumerico') {
                $datos[$campo] = __MODELO__::max($campo) + 1;
            }
            if (($meta['input_type'] ?? null) === 'checkbox') {
                $datos[$campo] = $request->has($campo) ? 'S' : 'N';
            }
        }

        __MODELO__::create($datos);
        return redirect()->route('__NOMBRE_RUTA__.index');
    }

    public function edit($id)
{
    $registro = __MODELO__::findOrFail($id);

    $configPath = resource_path("meta_abms/config_form___MODELO__.json");
    $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
   
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    $modelo = '__MODELO__';

    $siguiente = [];
    $labels = [];
    $defaults = [];
    $opciones = [];

    foreach ($campos as $campo => $meta) {
        $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
        $defaults[$campo] = $registro->$campo ?? $meta['default'] ?? '';

        if (
            ($meta['input_type'] ?? null) === 'select' &&
            !empty($meta['referenced_table']) &&
            !empty($meta['referenced_column']) &&
            !empty($meta['referenced_label'])
        ) {
            $tabla = $meta['referenced_table'];
            $columna = $meta['referenced_column'];
            $label = $meta['referenced_label'];

            try {
                $opciones["{$campo}_opciones"] = DB::table($tabla)
                    ->select($columna, $label)
                    ->orderBy($label)
                    ->get();
            } catch (\Throwable $e) {
                $opciones["{$campo}_opciones"] = collect(); // fallback en caso de error
                logger()->error("Error al cargar opciones para $campo desde $tabla: " . $e->getMessage());
            }
        }
    }


    return view('__CARPETA_VISTAS__.edit', 
        compact('registro', 'campos', 'modelo', 'labels', 'defaults', 'siguiente','opciones'
    ));
}


    public function update(Request $request, $id)
    {
        $registro = __MODELO__::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        foreach ($campos as $campo => $meta) {
            if (($meta['input_type'] ?? null) === 'checkbox') {
                $datos[$campo] = $request->has($campo) ? 'S' : 'N';
            }
        }

        $registro->update($datos);
        return redirect()->route('__NOMBRE_RUTA__.index');
    }

    public function destroy($id)
    {
        __MODELO__::destroy($id);
        return redirect()->route('__NOMBRE_RUTA__.index');
    }
}
