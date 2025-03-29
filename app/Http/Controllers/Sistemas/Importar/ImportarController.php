<?php

namespace App\Http\Controllers\Sistemas\Importar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ImportarController extends Controller
{
    /**
     * Mostrar formulario de selección de tabla y opciones.
     */
    public function form()
    {
        // Obtener lista de tablas desde SQL Server 2000 usando la conexión sqlsrv_koi
        $tablas = DB::connection('sqlsrv_koi')
            ->select("SELECT name FROM sysobjects WHERE xtype = 'U' ORDER BY name");
        $lista = collect($tablas)->pluck('name');

        return view('sistemas.importar.form', [
            'tablas' => $lista,
        ]);
    }

    /**
     * Ejecutar el comando artisan importar:tabla según los parámetros recibidos del formulario.
     */
    public function importar(Request $request)
    {
        $request->validate([
            'tabla' => 'required|string',
        ]);

        $nombreComando = 'importar:tabla';

        $argumentos = [
            'nombre_tabla' => $request->input('tabla'),
        ];

        $opciones = [];

        foreach (['force_models', 'force_table', 'with_sql_model'] as $flag) {
            if ($request->boolean($flag)) {
                $opciones['--' . str_replace('_', '-', $flag)] = true;
            }
        }

        if ($request->filled('unique')) {
            $opciones['--unique'] = $request->input('unique');
        }

        if ($request->boolean('fill_all')) {
            $opciones['--fill-all'] = true;
        }

        // Construir comando completo como string (para mostrarlo al usuario)
        $comando = "$nombreComando {$argumentos['nombre_tabla']}";
        foreach ($opciones as $clave => $valor) {
            $comando .= is_bool($valor) ? " {$clave}" : " {$clave}={$valor}";
        }

        // Ejecutar el comando con argumentos y opciones
        Artisan::call($nombreComando, array_merge($argumentos, $opciones));
        $output = Artisan::output();

        // Preparar información técnica para desarrolladores
        $detallesTecnicos = [
            'comando' => $comando,
            'tabla' => $request->input('tabla'),
            'unique' => $request->input('unique'),
            'fill_all' => $request->boolean('fill_all'),
        ];

        return back()
            ->with('output', $output)
            ->with('tecnico', $detallesTecnicos);
    }
}

