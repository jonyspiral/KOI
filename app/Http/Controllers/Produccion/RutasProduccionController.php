<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RutasProduccion;
use Illuminate\Support\Facades\File;

class RutasProduccionController extends Controller
{
    public function index()
    {
        $registros = RutasProduccion::all();

        // Cargamos configuración del formulario
        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $campos = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $columnas = array_keys($campos);

        return view('produccion/abms/rutas_produccion.index', compact('registros', 'campos', 'columnas'));
    }

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $campos = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        return view('produccion/abms/rutas_produccion.create', compact('campos'));
    }

    public function store(Request $request)
    {
        RutasProduccion::create($request->all());
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }

    public function edit($id)
    {
        $registro = RutasProduccion::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $campos = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        return view('produccion/abms/rutas_produccion.edit', compact('registro', 'campos'));
    }

    public function update(Request $request, $id)
    {
        $registro = RutasProduccion::findOrFail($id);
        $registro->update($request->all());
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }

    public function destroy($id)
    {
        RutasProduccion::destroy($id);
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }
}
