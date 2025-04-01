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

        // Cargamos configuración del formulario
        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $campos = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $columnas = array_keys($campos);

        return view('__CARPETA_VISTAS__.index', compact('registros', 'campos', 'columnas'));
    }

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $campos = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        return view('__CARPETA_VISTAS__.create', compact('campos'));
    }

    public function store(Request $request)
    {
        __MODELO__::create($request->all());
        return redirect()->route('__NOMBRE_RUTA__.index');
    }

    public function edit($id)
    {
        $registro = __MODELO__::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form___MODELO__.json");
        $campos = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        return view('__CARPETA_VISTAS__.edit', compact('registro', 'campos'));
    }

    public function update(Request $request, $id)
    {
        $registro = __MODELO__::findOrFail($id);
        $registro->update($request->all());
        return redirect()->route('__NOMBRE_RUTA__.index');
    }

    public function destroy($id)
    {
        __MODELO__::destroy($id);
        return redirect()->route('__NOMBRE_RUTA__.index');
    }
}
