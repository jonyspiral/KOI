<?php

namespace App\Http\Controllers\__NAMESPACE__;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\__MODELO__;
use Illuminate\Support\Facades\File;

class __MODELO__Controller extends Controller
{
    public function index()
    {
        $registros = __MODELO__::all();

        // 🛠 Cargar configuración del formulario
        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $columnas = array_keys($campos);
        $modelo = '__MODELO__';

        return view('__CARPETA_VISTAS__.index', compact('registros', 'campos', 'columnas', 'modelo'));
    }

    public function create()
{
    $configPath = resource_path("meta_abms/config_form___MODELO__.json");
    $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
    //dd($camposRaw);
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    //dd($campos);
    $modelo = '__MODELO__';

    $siguiente = [];
    $opciones = [];
    $labels = [];
    $defaults = [];

    foreach ($campos as $campo => $meta) {
        if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
            $max = __MODELO__::max($campo);
            $siguiente[$campo] = $max + 1;
        }

        $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
        $defaults[$campo] = $meta['default'] ?? '';

        if (!empty($meta['referenced_table']) && !empty($meta['referenced_label'])) {  
            $tabla = $meta['referenced_table'];
            $label = $meta['referenced_label'];
            $referenced_column = $meta['referenced_column'];

            $modeloRelacionado = 'App\\Models\\' . \Str::studly(\Str::singular($tabla));
            
            if (class_exists($modeloRelacionado)) {  
                $opciones["{$campo}_opciones"] = $modeloRelacionado::orderBy($label)->get();
            }
        }
    }
    dd($opciones);
    return view('__CARPETA_VISTAS__.create', array_merge(
        compact('campos', 'siguiente', 'modelo', 'labels', 'defaults'),
        $opciones
    ));
}


    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        // Asignar automáticamente valores de campos auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $datos[$campo] = __MODELO__::max($campo) + 1;
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
    
        $opciones = [];
        $labels = [];
        $defaults = [];
    
        foreach ($campos as $campo => $meta) {
            // 🔠 Label personalizado
            $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
    
            // 🧩 Valor por defecto
            $defaults[$campo] = $meta['default'] ?? '';
    
            // 🔽 Opciones para campos select
            if (!empty($meta['referenced_table']) && !empty($meta['referenced_label'])) {
                $tabla = $meta['referenced_table'];
                $label = $meta['referenced_label'];
                $modeloRelacionado = 'App\\Models\\' . \Str::studly(\Str::singular($tabla));
    
                if (class_exists($modeloRelacionado)) {
                    $opciones["{$campo}_opciones"] = $modeloRelacionado::orderBy($label)->get();
                }
            }
        }
        
        return view('__CARPETA_VISTAS__.edit', array_merge(
            compact('registro', 'campos', 'modelo', 'labels', 'defaults'),
            $opciones
        ));
    }
   public function update(Request $request, $id)
    {
        $registro = __MODELO__::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $registro->update($request->only(array_keys($campos)));

        return redirect()->route('__NOMBRE_RUTA__.index');
    }

    public function destroy($id)
    {
        __MODELO__::destroy($id);
        return redirect()->route('__NOMBRE_RUTA__.index');
    }
}
