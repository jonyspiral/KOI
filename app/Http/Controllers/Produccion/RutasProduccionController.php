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

        // 🛠 Cargar configuración del formulario
        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $columnas = array_keys($campos);
        $modelo = 'RutasProduccion';

        return view('produccion/abms/rutas_produccion.index', compact('registros', 'campos', 'columnas', 'modelo'));
    }

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $modelo = 'RutasProduccion';
        $siguiente = [];

        // Calcular valores para campos con max+1 o auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $max = RutasProduccion::max($campo);
                $siguiente[$campo] = $max + 1;
            }
        }

        return view('produccion/abms/rutas_produccion.create', compact('campos', 'siguiente', 'modelo'));
    }

    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        // Asignar automáticamente valores de campos auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $datos[$campo] = RutasProduccion::max($campo) + 1;
            }
        }

        RutasProduccion::create($datos);
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }

    public function edit($id)
    {
        $registro = RutasProduccion::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'RutasProduccion';

        return view('produccion/abms/rutas_produccion.edit', compact('registro', 'campos', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = RutasProduccion::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_RutasProduccion.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $registro->update($request->only(array_keys($campos)));

        return redirect()->route('produccion.abms.rutas_produccion.index');
    }

    public function destroy($id)
    {
        RutasProduccion::destroy($id);
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }
}
