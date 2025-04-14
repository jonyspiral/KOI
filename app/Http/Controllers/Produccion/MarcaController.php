<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marca;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Helpers\SubformManager;

class MarcaController extends Controller
{
    public function index(Request $request)
{
    $inicio = microtime(true);

    $modelo = 'Marca';
    $configPath = resource_path("meta_abms/config_form_{$modelo}.json");
    $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

    if (!isset($config['primary_key'])) {
        abort(500, "El archivo de configuración no tiene definida la clave 'primary_key'.");
    }
    $primaryKey = $config['primary_key'];
    $camposRaw = $config['campos'] ?? [];
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    $columnas = array_keys($campos);

    $subformularios = $config['subformularios'] ?? [];
    $carpeta_vistas = $config['carpeta_vistas'] ?? 'produccion/abms/marcas';

    $query = Marca::query();

    // 🔍 Búsqueda simple sobre columnas visibles
    if ($request->filled('buscar')) {
        $search = $request->input('buscar');
        $query->where(function ($q) use ($columnas, $search) {
            foreach ($columnas as $col) {
                $q->orWhere($col, 'LIKE', "%{$search}%");
            }
        });
    }

    // 📦 Paginación configurable desde JSON o input, valor por defecto: 100

   $formConfig = $config['form_config'] ?? [];
    $defaultPerPage = (int) ($formConfig['per_page'] ?? 100);
    $perPage = (int) $request->input('por_pagina', $defaultPerPage);

   // $perPage = max(10, min($perPage, 500));

    $tiempoAntesGet = microtime(true);
    $registros = $query->paginate($perPage)->appends($request->except('page'));
    $tiempoDespuesGet = microtime(true);

    // 🔁 Reemplazar campos tipo select con valor mostrado usando cache persistente
    $selectCache = [];

    foreach ($campos as $campo => $meta) {
        if (($meta['input_type'] ?? null) === 'select' &&
            !empty($meta['referenced_table']) &&
            !empty($meta['referenced_column']) &&
            !empty($meta['referenced_label'])
        ) {
            $tabla = $meta['referenced_table'];
            $columna = $meta['referenced_column'];
            $label = $meta['referenced_label'];

            $selectCache[$tabla] = \Cache::remember("select_cache_{$tabla}", 3600, function () use ($tabla, $columna, $label) {
                try {
                    return DB::table($tabla)
                        ->pluck($label, $columna)
                        ->toArray();
                } catch (\Throwable $e) {
                    logger()->warning("Error precargando '$tabla': " . $e->getMessage());
                    return [];
                }
            });
        }
    }

    foreach ($registros as $registro) {
        foreach ($campos as $campo => $meta) {
            if (($meta['input_type'] ?? null) === 'select' &&
                !empty($meta['referenced_table']) &&
                !empty($meta['referenced_column']) &&
                !empty($meta['referenced_label'])
            ) {
                $tabla = $meta['referenced_table'];
                $valor = $registro->$campo;
                $registro->$campo = $selectCache[$tabla][$valor] ?? $valor;
            }
        }
    }

    $fin = microtime(true);

    \Log::info('📄 CARGA index()');
    \Log::info('⏱ TOTAL: ' . round($fin - $inicio, 4) . ' s');
    \Log::info('📥 .get(): ' . round($tiempoDespuesGet - $tiempoAntesGet, 4) . ' s');
    \Log::info('🧩 Proceso extra: ' . round($fin - $tiempoDespuesGet, 4) . ' s');

    return view("{$carpeta_vistas}.index", compact(
        'registros', 'campos', 'columnas', 'modelo', 'subformularios', 'carpeta_vistas','primaryKey', 'perPage'
    ));
}

    

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form_Marca.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'Marca';

        $siguiente = [];
        $labels = [];
        $defaults = [];
        $opciones = [];

        foreach ($campos as $campo => $meta) {
            $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
            $defaults[$campo] = $meta['default'] ?? '';

            if (($meta['input_type'] ?? null) === 'autonumerico') {
                $siguiente[$campo] = Marca::max($campo) + 1;
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
                    $opciones["{$campo}_opciones"] = collect();
                    logger()->error("Error al cargar opciones para $campo desde $tabla: " . $e->getMessage());
                }
            }
        }

        return view('produccion/abms/marcas.create', compact(
            'campos', 'siguiente', 'modelo', 'labels', 'defaults', 'opciones'
        ));
    }

    public function edit($id)
    {
        $configPath = resource_path("meta_abms/config_form_Marca.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];

        
        $registro = Marca::where($primaryKey, $id)->firstOrFail();

        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'Marca';

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
                    $opciones["{$campo}_opciones"] = collect();
                    logger()->error("Error al cargar opciones para $campo desde $tabla: " . $e->getMessage());
                }
            }
        }

        return view('produccion/abms/marcas.edit', compact(
            'registro', 'campos', 'modelo', 'labels', 'defaults', 'siguiente', 'opciones','primaryKey'
        ));
    }

    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_Marca.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = [];

        foreach ($campos as $campo => $meta) {
            $valor = $request->input($campo);

            if (($meta['input_type'] ?? null) === 'autonumerico') {
                $valor = Marca::max($campo) + 1;
            } elseif (($meta['input_type'] ?? null) === 'checkbox') {
                $valor = $request->has($campo) ? 'S' : 'N';
            }

            $datos[$campo] = $valor;
        }

        Marca::create($datos);

        $redirect = $this->redirectToParent($request, 'Marca');
        return $redirect ?? redirect()->route('produccion.abms.marcas.index')->with('success', 'Guardado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $configPath = resource_path("meta_abms/config_form_Marca.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];
    
        $registro = Marca::where($primaryKey, $id)->firstOrFail();
    
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    
        $datos = [];
    
        foreach ($campos as $campo => $meta) {
            $valor = $request->input($campo);
    
            // Ya no necesitamos lógica para checkbox gracias al input hidden en Blade
            $datos[$campo] = $valor;
        }
    
        $registro->update($datos);
    
        $redirect = $this->redirectToParent($request, 'Marca');
        return $redirect ?? redirect()->route('produccion.abms.marcas.index')->with('success', 'Actualizado correctamente.');
    }
    

public function destroy(Request $request, $id)
{
    $configPath = resource_path("meta_abms/config_form_Marca.json");
    $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
    $primaryKey = $config['primary_key'] ?? 'id';
    $camposRaw = $config['campos'] ?? [];
    $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

    // 🧠 Detectar si es subform
    $foreignKey = null;
    foreach ($campos as $campo => $meta) {
        if (!empty($meta['referenced_table']) || str_starts_with($campo, 'cod_')) {
            if ($request->has($campo)) {
                $foreignKey = $campo;
                break;
            }
        }
    }

    // 🔍 Buscar registro por clave adecuada
    if ($foreignKey && $request->has($foreignKey)) {
        $registro = Marca::where($foreignKey, $request->input($foreignKey))->firstOrFail();
    } else {
        $registro = Marca::where($primaryKey, $id)->firstOrFail();
    }

    $modeloNombre = class_basename($registro);

    $registro->delete();

    return $this->redirectToParent($request->merge($registro->toArray()), $modeloNombre)
        ?? redirect()->route('produccion.abms.marcas.index')->with('success', 'Registro eliminado correctamente.');
}


    // 📦 Redirección automática al padre (si es subformulario)
    protected function redirectToParent(Request $request, string $modeloNombre)
    {
        $configPath = resource_path("meta_abms/config_form_{$modeloNombre}.json");

        if (!file_exists($configPath)) {
            return null;
        }

        $config = json_decode(file_get_contents($configPath), true);

        $foreignKey = null;
        foreach ($config['campos'] ?? [] as $campo => $meta) {
            if (!empty($meta['referenced_table']) || str_starts_with($campo, 'cod_')) {
                if ($request->has($campo)) {
                    $foreignKey = $campo;
                    break;
                }
            }
        }

        if (!$foreignKey) {
            return null;
        }

        $parentId = $request->input($foreignKey);
        $parentRuta = null;

        foreach (glob(resource_path('meta_abms/config_form_*.json')) as $file) {
            $data = json_decode(file_get_contents($file), true);
            if (!empty($data['subformularios'])) {
                foreach ($data['subformularios'] as $sub) {
                    if ($sub['modelo'] === $modeloNombre) {
                        $parentRuta = basename($data['carpeta_vistas'] ?? '');
                        break 2;
                    }
                }
            }
        }

        if ($parentRuta && $parentId) {
            return redirect()->route("produccion.abms.{$parentRuta}.index", $parentId)
                             ->with('success', 'Registro guardado correctamente.');
        }

        return null;
    }
    public function show($id)
    {
        $configPath = resource_path("meta_abms/config_form_Marca.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];
    
        $registro = Marca::where($primaryKey, $id)->firstOrFail();
    
        $campos = array_filter($config['campos'] ?? [], fn($cfg) => !empty($cfg['incluir']));
    
        return view('produccion/abms/marcas.show', compact('registro', 'campos'));
    }

}
