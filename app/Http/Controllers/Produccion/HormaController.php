<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horma;
use Illuminate\Support\Facades\File;

class HormaController extends Controller
{
    public function index()
    {
        $registros = Horma::all();

        // 🛠 Cargar configuración del formulario
        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $columnas = array_keys($campos);
        $modelo = 'Horma';

        return view('produccion/abms/hormas.index', compact('registros', 'campos', 'columnas', 'modelo'));
    }

    public function create()
{
    $configPath = resource_path("meta_abms/config_form_Horma.json");
    $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
  
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

    $modelo = 'Horma';

    $siguiente = [];
    $opciones = [];
    $labels = [];
    $defaults = [];

    foreach ($campos as $campo => $meta) {
        if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
            $max = Horma::max($campo);
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
 
    return view('produccion/abms/hormas.create', array_merge(
        compact('campos', 'siguiente', 'modelo', 'labels', 'defaults'),
        $opciones
    ));
}


    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        // Asignar automáticamente valores de campos auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $datos[$campo] = Horma::max($campo) + 1;
            }
        }

        Horma::create($datos);
       
        return redirect()->route('produccion.abms.hormas.index');
    }
    public function edit($id)
    {
        $registro = Horma::findOrFail($id);
    
        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
    
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'Horma';
    
        $opciones = [];
        $labels = [];        // Asignar automáticamente valores de campos auto_increment_plus

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
        
        return view('produccion/abms/hormas.edit', array_merge(
            compact('registro', 'campos', 'modelo', 'labels', 'defaults'),
            $opciones
        ));
    }
   public function update(Request $request, $id)
    {
        $registro = Horma::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $registro->update($request->only(array_keys($campos)));

        return redirect()->route('produccion.abms.hormas.index');
    }

    public function destroy($id)
    {
        Horma::destroy($id);
        return redirect()->route('produccion.abms.hormas.index');
    }
}
