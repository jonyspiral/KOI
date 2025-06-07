<?php
namespace App\Http\Controllers\Sistemas\Importar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ImportarController extends Controller
{
    /**
     * Mostrar el formulario de importación
     */
    public function form()
    {
        // Obtener todas las tablas del SQL Server 2000
        $tablas = DB::connection('sqlsrv_koi')
            ->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");

        $nombres = array_map(fn($t) => $t->TABLE_NAME, $tablas);

        return view('sistemas.importar.form', [
            'tablas' => $nombres,
        ]);
    }





    /**
     * Ejecutar la importación
     * 
     */


     public function importar(Request $request)
    {
        $request->validate([
            'nombre_tabla' => 'required|string',
        ]);

        $tabla = $request->input('nombre_tabla');

        // Armamos los flags
        $flags = [];

        if ($request->has('force_table')) $flags['--force-table'] = true;
        if ($request->has('force_models')) $flags['--force-models'] = true;
        if ($request->has('with_sql_model')) $flags['--with-sql-model'] = true;
        if ($request->has('fill_all')) $flags['--fill-all'] = true;
        if ($request->has('skip_data')) $flags['--skip-data'] = true;
        if ($request->has('insert_simple')) $flags['--insert-simple'] = true;
       // dd("entro");
        // Ejecutamos el comando
        Artisan::call('importar:tabla', array_merge(
            ['nombre_tabla' => $tabla],
            $flags
        ));

        $output = Artisan::output();

        // Recargamos las tablas
        $tablas = DB::connection('sqlsrv_koi')
            ->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        $nombres = array_map(fn($t) => $t->TABLE_NAME, $tablas);

        // Retornamos directamente a la vista con el resultado
        return view('sistemas.importar.form', [
            'tablas' => $nombres,
            'output' => $output,
            'tablaSeleccionada' => $tabla,
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
