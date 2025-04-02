<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curva;
use Illuminate\Support\Facades\File;

class CurvaController extends Controller
{
    public function index()
    {
        $registros = Curva::all();

        // 🛠 Cargar configuración del formulario
        $configPath = resource_path("meta_abms/config_form_Curva.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $columnas = array_keys($campos);
        $modelo = 'Curva';

        return view('produccion/abms/curvas.index', compact('registros', 'campos', 'columnas', 'modelo'));
    }

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form_Curva.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $modelo = 'Curva';
        $siguiente = [];

        // Calcular valores para campos con max+1 o auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $max = Curva::max($campo);
                $siguiente[$campo] = $max + 1;
            }
        }

        return view('produccion/abms/curvas.create', compact('campos', 'siguiente', 'modelo'));
    }

    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_Curva.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        // Asignar automáticamente valores de campos auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $datos[$campo] = Curva::max($campo) + 1;
            }
        }

        Curva::create($datos);
        return redirect()->route('produccion.abms.curva.index');
    }

    public function edit($id)
    {
        $registro = Curva::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_Curva.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'Curva';

        return view('produccion/abms/curvas.edit', compact('registro', 'campos', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = Curva::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_Curva.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $registro->update($request->only(array_keys($campos)));

        return redirect()->route('produccion.abms.curva.index');
    }

    public function destroy($id)
    {
        Curva::destroy($id);
        return redirect()->route('produccion.abms.curva.index');
    }
}
