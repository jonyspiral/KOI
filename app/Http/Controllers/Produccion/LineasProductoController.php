<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LineasProducto;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Helpers\SubformManager;

class LineasProductoController extends Controller
{
    public function index(Request $request)
    {
        // 🧩 Cargar configuración JSON del formulario
        $modelo = 'LineasProducto';
        $configPath = resource_path("meta_abms/config_form_{$modelo}.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
    
        // 🔐 Validación obligatoria de clave primaria
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración no tiene definida la clave 'primary_key'.");
        }
    
        $primaryKey = $config['primary_key'];
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $columnas = array_keys($campos);
        $subformularios = $config['subformularios'] ?? [];
        $carpeta_vistas = $config['carpeta_vistas'] ?? 'produccion/abms/lineas_productos';
        $formConfig = $config['form_config'] ?? [];
    
        $defaultPerPage = (int) ($formConfig['per_page'] ?? 100);
        $perPage = (int) $request->input('por_pagina', $defaultPerPage);
    
        $query = LineasProducto::query();
    
        if ($request->filled('buscar') && $request->filled('buscar_campo')) {
            $campo = $request->input('buscar_campo');
            $valor = $request->input('buscar');
        
            if (in_array($campo, $columnas)) {
                $query->where($campo, 'LIKE', "%{$valor}%");
            }
        }
       // 💡 Asegura que los parámetros del filtro persistan al avanzar página
       $registros = $query->paginate($perPage);
        // 🧠 Cacheo de labels para campos tipo select
        $selectCache = [];
        foreach ($campos as $campo => $meta) {
            if (($meta['input_type'] ?? null) === 'select' &&
                !empty($meta['referenced_table']) &&
                !empty($meta['referenced_column']) &&
                !empty($meta['referenced_label'])) {
    
                $tabla = $meta['referenced_table'];
                $columna = $meta['referenced_column'];
                $label = $meta['referenced_label'];
    
                $selectCache[$tabla] = \Cache::remember("select_cache_{$tabla}", 3600, function () use ($tabla, $columna, $label) {
                    try {
                        return DB::table($tabla)->pluck($label, $columna)->toArray();
                    } catch (\Throwable $e) {
                        logger()->warning("Error precargando '$tabla': " . $e->getMessage());
                        return [];
                    }
                });
            }
        }
        // 🧾 Para todos los demás tipos de index
        $registros = $query->paginate($perPage);
    
        foreach ($registros as $registro) {
            foreach ($campos as $campo => $meta) {
                if (($meta['input_type'] ?? null) === 'select' && !empty($meta['referenced_table'])) {
                    $tabla = $meta['referenced_table'];
                    $valor = $registro->$campo;
                    $registro->$campo = $selectCache[$tabla][$valor] ?? $valor;
                }
            }
        }
     

        // 🧷 Selección dinámica de la vista según el tipo de index
        $indexViewType = $formConfig['index_view_type'] ?? 'default';
        $viewName = match ($indexViewType) {
            'inline' => "{$carpeta_vistas}.index-inline",
            'tab'    => "{$carpeta_vistas}.index-tab",
            'ficha'  => "{$carpeta_vistas}.index-ficha",
            default  => "{$carpeta_vistas}.index",
        };
    
        if (!view()->exists($viewName)) {
            logger()->warning("Vista '{$viewName}' no encontrada. Usando fallback a 'index'.");
            $viewName = "{$carpeta_vistas}.index";
        }
    
        // 🎯 Cargar variables dinámicas según el tipo de vista
        if ($indexViewType === 'ficha') {
            $registro = $query->first();
    
            if ($registro) {
                foreach ($campos as $campo => $meta) {
                    if (($meta['input_type'] ?? null) === 'select' && !empty($meta['referenced_table'])) {
                        $tabla = $meta['referenced_table'];
                        $valor = $registro->$campo;
                        $registro->$campo = $selectCache[$tabla][$valor] ?? $valor;
                    }
                }
            }
    
            return view($viewName, compact(
                'registro',
                'campos',
                'columnas',
                'modelo',
                'subformularios',
                'carpeta_vistas',
                'primaryKey',
                'formConfig',
                'perPage',
                'registros'
            ));
        }
    
    
        return view($viewName, compact(
            'registros',
            'campos',
            'columnas',
            'modelo',
            'subformularios',
            'carpeta_vistas',
            'primaryKey',
            'perPage',
            'formConfig'
        ));
    }
    
        
    
        public function create()
        {
            $configPath = resource_path("meta_abms/config_form_LineasProducto.json");
            $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
            $camposRaw = $config['campos'] ?? [];
            $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
            $modelo = 'LineasProducto';
    
            $siguiente = [];
            $labels = [];
            $defaults = [];
            $opciones = [];
    
            foreach ($campos as $campo => $meta) {
                $labels[$campo] = $meta['label_custom'] ?? ucfirst(str_replace('_', ' ', $campo));
                $defaults[$campo] = $meta['default'] ?? '';
    
                if (($meta['input_type'] ?? null) === 'autonumerico') {
                    $siguiente[$campo] = LineasProducto::max($campo) + 1;
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
    
            return view('produccion/abms/lineas_productos.create', compact(
                'campos', 'siguiente', 'modelo', 'labels', 'defaults', 'opciones'
            ));
        }
    
        public function edit($id)
        {
            $configPath = resource_path("meta_abms/config_form_LineasProducto.json");
            $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
            if (!isset($config['primary_key'])) {
                abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
            }
            $primaryKey = $config['primary_key'];
    
            
            $registro = LineasProducto::where($primaryKey, $id)->firstOrFail();
    
            $camposRaw = $config['campos'] ?? [];
            $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
            $modelo = 'LineasProducto';
    
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
    
            return view('produccion/abms/lineas_productos.edit', compact(
                'registro', 'campos', 'modelo', 'labels', 'defaults', 'siguiente', 'opciones','primaryKey'
            ));
        }

    public function store(Request $request)
    {
        $modeloNombre = 'LineasProducto';
        $modelo = "\\App\\Models\\{$modeloNombre}";
        $modeloSql = "\\App\\Models\\Sql\\{$modeloNombre}";

        // 📄 Cargar configuración
        $configPath = resource_path("meta_abms/config_form_{$modeloNombre}.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

        // 🔁 Procesar campos
        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $usaTimestamps = $config['timestamps'] ?? false;

        $datos = [];
        foreach ($campos as $campo => $meta) {
            $valor = $request->input($campo);
            if (($meta['input_type'] ?? null) === 'autonumerico') {
                $valor = $modelo::max($campo) + 1;
            } elseif (($meta['input_type'] ?? null) === 'checkbox') {
                $valor = $request->has($campo) ? 'S' : 'N';
            }
            $datos[$campo] = $valor;
        }

        // 🕒 Timestamps manuales
        if ($usaTimestamps) {
            $datos['created_at'] = now();
            $datos['updated_at'] = now();
        }

        $datos = $this->aplicarSyncStatus($datos, 'create');

        // 🔄 Sincronización con SQL Server
        if ($config['sincronizable'] ?? false) {
            $syncService = new \App\Services\SincronizadorService;
            $datosSql = $syncService->formatearParaSqlServer($datos, $modeloNombre);

            $conexionSql = (new $modeloSql)->getConnectionName();
            $ok = $syncService->syncCreate($modeloNombre, $datosSql, $conexionSql);

            if (!$ok) {
                \Log::error("❌ No se pudo sincronizar con SQL Server. ABORTANDO.");
                return redirect()->back()->withInput()->with('error', '❌ No se pudo crear el registro porque la sincronización con SQL Server falló.');
            }

            $datos['sync_status'] = 'S';
            \Log::info("✅ Registro sincronizado correctamente con SQL Server.");
        }

        // ✅ Guardar en MySQL
        $registro = $modelo::create($datos);
        return redirect()->route('produccion.abms.lineas_producto.index')->with('success', '✅ Registro creado y sincronizado.');
    }

    public function update(Request $request, $id)
    {
        $modeloNombre = 'LineasProducto';
        $modeloClase = "\\App\\Models\\{$modeloNombre}";
        $modeloSql = "\\App\\Models\\Sql\\{$modeloNombre}";
        $rutaNombre = 'produccion.abms.lineas_producto';

        $configPath = resource_path("meta_abms/config_form_{$modeloNombre}.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }

        $primaryKey = $config['primary_key'];
        $registro = $modeloClase::where($primaryKey, $id)->firstOrFail();

        $camposRaw = $config['campos'] ?? [];
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $usaTimestamps = $config['timestamps'] ?? false;

        $datos = [];
        foreach ($campos as $campo => $meta) {
            $valor = $request->input($campo);
            $datos[$campo] = $valor;
        }

        if ($usaTimestamps) {
            $datos['updated_at'] = now();
        }

        $datos = $this->aplicarSyncStatus($datos, 'update');

        $primaryKeySql = $modeloClase::$primaryKeySql ?? [];
        foreach ($primaryKeySql as $clave) {
            if (!isset($datos[$clave])) {
                $datos[$clave] = $registro->$clave;
            }
        }

        $registro->update($datos);

        $mensaje = 'Actualizado correctamente.';
        if ($config['sincronizable'] ?? false) {
            $syncService = new \App\Services\SincronizadorService;
            $claveReal = $primaryKeySql[0] ?? $primaryKey;
            $conexionSql = (new $modeloSql)->getConnectionName();

            $ok = $syncService->syncUpdate($modeloNombre, $datos, $claveReal, $conexionSql);

            if ($ok) {
                \Log::info("✅ UPDATE sincronizado con éxito: {$datos[$claveReal]}");
                $mensaje .= ' (✅ sincronizado)';
            } else {
                \Log::warning("⚠️ UPDATE guardado pero no sincronizado: {$datos[$claveReal]}");
                $mensaje .= ' (⚠️ no sincronizado)';
            }
        }

        return $this->redirectToParent($request, $modeloNombre) ?? redirect()->route("{$rutaNombre}.index")->with('success', $mensaje);
    }

    public function destroy(Request $request, $id)
    {
        $modeloNombre = 'LineasProducto';
        $modeloSql = "\\App\\Models\\Sql\\{$modeloNombre}";

        $configPath = resource_path("meta_abms/config_form_{$modeloNombre}.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }

        $primaryKey = $config['primary_key'];
        $registro = LineasProducto::where($primaryKey, $id)->firstOrFail();

        $registro->sync_status = 'D';
        if ($config['timestamps'] ?? false) {
            $registro->updated_at = now();
        }
        $registro->save();

        if ($config['sincronizable'] ?? false) {
            $syncService = new \App\Services\SincronizadorService;
            $primaryKeySql = $registro::$primaryKeySql[0] ?? null;

            if (!$primaryKeySql || !isset($registro->{$primaryKeySql})) {
                \Log::error("❌ No se encontró valor para la clave primaria real ($primaryKeySql) en delete.");
                \Log::warning("⚠️ Eliminación local, pero no sincronizada: " . json_encode($registro->toArray()));
                $mensaje = 'Registro marcado como eliminado. (⚠️ no sincronizado)';
            } else {
                $conexionSql = (new $modeloSql)->getConnectionName();
                $ok = $syncService->syncDelete($modeloNombre, $registro->toArray(), $primaryKeySql, $conexionSql);

                if ($ok) {
                    \Log::info("✅ Eliminación sincronizada: {$registro->{$primaryKeySql}}");
                    $mensaje = 'Registro eliminado y sincronizado correctamente.';
                } else {
                    \Log::warning("⚠️ Eliminación local, pero no sincronizada: {$registro->{$primaryKeySql}}");
                    $mensaje = 'Registro marcado como eliminado. (⚠️ no sincronizado)';
                }
            }
        } else {
            $mensaje = 'Registro marcado como eliminado.';
        }

        return $this->redirectToParent($request->merge($registro->toArray()), $modeloNombre)
            ?? redirect()->route('produccion.abms.lineas_producto.index')->with('success', $mensaje);
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
        $configPath = resource_path("meta_abms/config_form_LineasProducto.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];
        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }
        $primaryKey = $config['primary_key'];
    
        $registro = LineasProducto::where($primaryKey, $id)->firstOrFail();
    
        $campos = array_filter($config['campos'] ?? [], fn($cfg) => !empty($cfg['incluir']));
    
        return view('produccion/abms/lineas_productos.show', compact('registro', 'campos'));
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
    // Si no está definido, asignamos el valor correspondiente
    if (!isset($datos['sync_status'])) {
        $datos['sync_status'] = $modo === 'create' ? 'N' : 'U'; // 'N' para nuevo, 'U' para actualizado
    }

    // Asignar timestamps manualmente si están deshabilitados en el modelo
    $now = now()->toDateTimeString(); // Obtenemos la fecha y hora actual

    if ($modo === 'create' && !isset($datos['created_at'])) {
        $datos['created_at'] = $now; // Asignamos created_at solo si es un nuevo registro
    }

    if (!isset($datos['updated_at'])) {
        $datos['updated_at'] = $now; // Siempre asignamos updated_at, incluso en create
    }
    

    return $datos; // Devolvemos los datos con los valores correctos
}

public function restaurar($id)
{
    $configPath = resource_path("meta_abms/config_form_LineasProducto.json");
    $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

    if (!isset($config['primary_key'])) {
        abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
    }

    $primaryKey = $config['primary_key'];

    // 🧠 Buscar registro por clave primaria real
    $registro = LineasProducto::where($primaryKey, $id)->firstOrFail();

    // ✅ Restaurar marcando como actualizado
    $registro->sync_status = 'U';
    if ($config['timestamps'] ?? false) {
        $registro->updated_at = now();
    }

    $registro->save();

    return redirect()->route('produccion.abms.lineas_producto.index')->with('success', 'Registro restaurado correctamente.');
}
}
