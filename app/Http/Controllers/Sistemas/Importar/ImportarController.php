<?php

namespace App\Http\Controllers\Sistemas\Importar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class ImportarController extends Controller
{
    /**
     * Mostrar el formulario de importación
     */
    public function form(Request $request)
    {
        // Conexión default
        $conexion = $request->input('connection', 'sqlsrv_koi');

        // Obtener todas las tablas del SQL Server indicado
        try {
            $tablas = DB::connection($conexion)
                ->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");

            $nombres = array_map(fn($t) => $t->TABLE_NAME, $tablas);
        } catch (\Exception $e) {
            return back()->withErrors("❌ Error de conexión [$conexion]: " . $e->getMessage());
        }

        return view('sistemas.importar.form', [
            'tablas' => $nombres,
            'conexion' => $conexion,
        ]);
    }

    /**
     * Ejecutar la importación
     */
    public function importar(Request $request)
    {
        $request->validate([
            'nombre_tabla' => 'required|string',
            'connection' => 'nullable|string',
        ]);

        $tabla = $request->input('nombre_tabla');
        $conexion = $request->input('connection', 'sqlsrv_koi');

        // Armamos los flags
        $flags = [];

        if ($request->has('force_table')) $flags['--force-table'] = true;
        if ($request->has('force_models')) $flags['--force-models'] = true;
        if ($request->has('with_sql_model')) $flags['--with-sql-model'] = true;
        if ($request->has('fill_all')) $flags['--fill-all'] = true;
        if ($request->has('skip_data')) $flags['--skip-data'] = true;
        if ($request->has('insert_simple')) $flags['--insert-simple'] = true;
        if ($conexion !== 'sqlsrv_koi') $flags['--connection'] = $conexion;

        // Ejecutamos el comando
        Artisan::call('importar:tabla', array_merge(
            ['nombre_tabla' => $tabla],
            $flags
        ));

        $output = Artisan::output();

        // Recargamos las tablas
        try {
            $tablas = DB::connection($conexion)
                ->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");

            $nombres = array_map(fn($t) => $t->TABLE_NAME, $tablas);
        } catch (\Exception $e) {
            $nombres = [];
        }

        return view('sistemas.importar.form', [
            'tablas' => $nombres,
            'output' => $output,
            'tablaSeleccionada' => $tabla,
            'conexion' => $conexion,
        ]);
    }

    public function eliminarConfig(Request $request)
    {
        $modelo = $request->input('modelo');
        $path = resource_path("meta_abms/config_form_{$modelo}.json");

        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', "🗑️ Archivo config_form_{$modelo}.json eliminado correctamente.");
        }

        return back()->withErrors("⚠️ El archivo no existe o ya fue eliminado.");
    }
}
