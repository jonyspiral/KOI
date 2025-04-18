<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasosRutasProduccion;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Helpers\SubformManager;

class PasosRutasProduccionController extends Controller
{
    public function index(Request $request)
{
    

    $modelo = 'PasosRutasProduccion';
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
    $carpeta_vistas = $config['carpeta_vistas'] ?? 'produccion/abms/pasos_rutas_produccions';

    $query = PasosRutasProduccion::query();

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
    $registros = $query->paginate($perPage);
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

   

    return view("{$carpeta_vistas}.index", compact(
        'registros', 'campos', 'columnas', 'modelo', 'subformularios', 'carpeta_vistas','primaryKey', 'perPage'
    ));
}

    

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form_PasosRutasProduccion.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'PasosRutasProduccion';

        $siguiente = [];
        $labels = [];
        $defaults = [];
        $opciones = [];

        foreach ($campos as $campo => $meta) {
            $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
            $defaults[$campo] = $meta['default'] ?? '';

            if (($meta['input_type'] ?? null) === 'autonumerico') {
                $siguiente[$campo] = PasosRutasProduccion::max($campo) + 1;
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

        return view('produccion/abms/pasos_rutas_produccion.create', compact(
            'campos', 'siguiente', 'modelo', 'labels', 'defaults', 'opciones'
        ));
    }

    public function edit($id)
    {
        $configPath = resource_path("meta_abms/config_form_PasosRutasProduccion.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];

        
        $registro = PasosRutasProduccion::where($primaryKey, $id)->firstOrFail();

        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'PasosRutasProduccion';

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

        return view('produccion/abms/pasos_rutas_produccion.edit', compact(
            'registro', 'campos', 'modelo', 'labels', 'defaults', 'siguiente', 'opciones','primaryKey'
        ));
    }

    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_PasosRutasProduccion.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = [];

        foreach ($campos as $campo => $meta) {
            $valor = $request->input($campo);

            if (($meta['input_type'] ?? null) === 'autonumerico') {
                $valor = PasosRutasProduccion::max($campo) + 1;
            } elseif (($meta['input_type'] ?? null) === 'checkbox') {
                $valor = $request->has($campo) ? 'S' : 'N';
            }

            $datos[$campo] = $valor;
        }

        $datos = $this->aplicarSyncStatus($datos, 'create'); // ← 👈 APLICACIÓN DEL SYNC STATUS

        PasosRutasProduccion::create($datos);

        $redirect = $this->redirectToParent($request, 'PasosRutasProduccion');
        return $redirect ?? redirect()->route('produccion.abms.pasos_rutas_produccion.index')->with('success', 'Guardado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $configPath = resource_path("meta_abms/config_form_PasosRutasProduccion.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];
    
        $registro = PasosRutasProduccion::where($primaryKey, $id)->firstOrFail();
    
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
    
        $datos = [];
    
        foreach ($campos as $campo => $meta) {
            $valor = $request->input($campo);
    
            // Ya no necesitamos lógica para checkbox gracias al input hidden en Blade
            $datos[$campo] = $valor;
        }
    
        $datos = $this->aplicarSyncStatus($datos, 'update'); // ← 👈 APLICACIÓN DEL SYNC STATUS
        $registro->update($datos);
    
        $redirect = $this->redirectToParent($request, 'PasosRutasProduccion');
        return $redirect ?? redirect()->route('produccion.abms.pasos_rutas_produccion.index')->with('success', 'Actualizado correctamente.');
    }
    

    public function destroy(Request $request, $id)
{
    $configPath = resource_path("meta_abms/config_form_PasosRutasProduccion.json");
    $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

    if (!isset($config['primary_key'])) {
        abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
    }

    $primaryKey = $config['primary_key'];

    // 🧠 Buscar registro por clave primaria real
    $registro = PasosRutasProduccion::where($primaryKey, $id)->firstOrFail();

    // ✅ Marcar como eliminado (soft delete vía sincronizador)
    $registro->sync_status = 'D';
    $registro->save();

    // 🧭 Redirección al padre si corresponde
    return $this->redirectToParent($request->merge($registro->toArray()), 'PasosRutasProduccion')
        ?? redirect()->route('produccion.abms.pasos_rutas_produccion.index')->with('success', 'Marcado como eliminado correctamente.');
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
        $configPath = resource_path("meta_abms/config_form_PasosRutasProduccion.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];
    
        $registro = PasosRutasProduccion::where($primaryKey, $id)->firstOrFail();
    
        $campos = array_filter($config['campos'] ?? [], fn($cfg) => !empty($cfg['incluir']));
    
        return view('produccion/abms/pasos_rutas_produccion.show', compact('registro', 'campos'));
    }
    /**
 * 🧠 Aplica el estado de sincronización y timestamps manuales.
 *
 * @param array $datos  Datos a guardar.
 * @param string $modo  'create' o 'update'.
 * @return array
 */
private function aplicarSyncStatus(array $datos, string $modo): array
{
    // Estado de sincronización
    if (!isset($datos['sync_status'])) {
        $datos['sync_status'] = $modo === 'create' ? 'N' : 'U';
    }

    // Timestamps manuales si están deshabilitados en el modelo
    $now = now()->toDateTimeString();
    if ($modo === 'create' && !isset($datos['created_at'])) {
        $datos['created_at'] = $now;
    }
    if (!isset($datos['updated_at'])) {
        $datos['updated_at'] = $now;
    }

    return $datos;
}


}
