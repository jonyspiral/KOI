<?php

namespace App\Http\Controllers\__NAMESPACE__;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\__MODELO__;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Helpers\SubformManager;

class __MODELO__Controller extends Controller
{
    // ⚙️ MÉTODOS OMITIDOS PARA BREVEDAD...

    public function store(Request $request)
    {
        $modeloNombre = '__MODELO__';
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
        return redirect()->route('__NOMBRE_RUTA__.index')->with('success', '✅ Registro creado y sincronizado.');
    }

    public function update(Request $request, $id)
    {
        $modeloNombre = '__MODELO__';
        $modeloClase = "\\App\\Models\\{$modeloNombre}";
        $modeloSql = "\\App\\Models\\Sql\\{$modeloNombre}";
        $rutaNombre = '__NOMBRE_RUTA__';

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
        $modeloNombre = '__MODELO__';
        $modeloSql = "\\App\\Models\\Sql\\{$modeloNombre}";

        $configPath = resource_path("meta_abms/config_form_{$modeloNombre}.json");
        $config = File::exists($configPath) ? json_decode(File::get($configPath), true) : [];

        if (!isset($config['primary_key'])) {
            abort(500, "El archivo de configuración del modelo no tiene definida la clave 'primary_key'.");
        }

        $primaryKey = $config['primary_key'];
        $registro = __MODELO__::where($primaryKey, $id)->firstOrFail();

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
            ?? redirect()->route('__NOMBRE_RUTA__.index')->with('success', $mensaje);
    }
}
