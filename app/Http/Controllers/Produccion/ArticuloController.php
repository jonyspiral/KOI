<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;
use Illuminate\Support\Facades\File;

class ArticuloController extends Controller
{
    public function index()
    {
        $registros = Articulo::all();

        // 🛠 Cargar configuración del formulario
        $configPath = resource_path("meta_abms/config_form_Articulo.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $columnas = array_keys($campos);
        $modelo = 'Articulo';

        return view('produccion/abms/articulos.index', compact('registros', 'campos', 'columnas', 'modelo'));
    }

    public function create()
{
    $configPath = resource_path("meta_abms/config_form_Articulo.json");
    $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
    //dd($camposRaw);
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    //dd($campos);
    $modelo = 'Articulo';

    $siguiente = [];
    $opciones = [];
    $labels = [];
    $defaults = [];

    foreach ($campos as $campo => $meta) {
        if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
            $max = Articulo::max($campo);
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
    return view('produccion/abms/articulos.create', array_merge(
        compact('campos', 'siguiente', 'modelo', 'labels', 'defaults'),
        $opciones
    ));
}


    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_Articulo.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        // Asignar automáticamente valores de campos auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $datos[$campo] = Articulo::max($campo) + 1;
            }
        }

        Articulo::create($datos);
       
        return redirect()->route('produccion.abms.articulos.index');
    }
    public function edit($id)
    {
        $registro = Articulo::findOrFail($id);
    
        $configPath = resource_path("meta_abms/config_form_Articulo.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];
    
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'Articulo';
    
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
        
        return view('produccion/abms/articulos.edit', array_merge(
            compact('registro', 'campos', 'modelo', 'labels', 'defaults'),
            $opciones
        ));
    }
   public function update(Request $request, $id)
    {
        $registro = Articulo::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_Articulo.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $registro->update($request->only(array_keys($campos)));

        return redirect()->route('produccion.abms.articulos.index');
    }

    public function destroy($id)
    {
        Articulo::destroy($id);
        return redirect()->route('produccion.abms.articulos.index');
    }
}
