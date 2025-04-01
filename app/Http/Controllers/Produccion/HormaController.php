<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horma;
use Illuminate\Support\Facades\File;

class HormaController extends Controller
{
    public function index()
    {
        $registros = Horma::all();

        // 🛠 Cargar configuración del formulario
        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $columnas = array_keys($campos);
        $modelo = 'Horma';

        return view('produccion/abms/hormas.index', compact('registros', 'campos', 'columnas', 'modelo'));
    }

    public function create()
    {
        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        // Filtrar solo los campos incluidos
        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $modelo = 'Horma';
        $siguiente = [];

        // Calcular valores para campos con max+1 o auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $max = Horma::max($campo);
                $siguiente[$campo] = $max + 1;
            }
        }

        return view('produccion/abms/hormas.create', compact('campos', 'siguiente', 'modelo'));
    }

    public function store(Request $request)
    {
        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $datos = $request->only(array_keys($campos));

        // Asignar automáticamente valores de campos auto_increment_plus
        foreach ($campos as $campo => $meta) {
            if (!empty($meta['max_mas_uno']) || !empty($meta['auto_increment_plus'])) {
                $datos[$campo] = Horma::max($campo) + 1;
            }
        }

        Horma::create($datos);
        return redirect()->route('produccion.abms.horma.index');
    }

    public function edit($id)
    {
        $registro = Horma::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));
        $modelo = 'Horma';

        return view('produccion/abms/hormas.edit', compact('registro', 'campos', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = Horma::findOrFail($id);

        $configPath = resource_path("meta_abms/config_form_Horma.json");
        $camposRaw = File::exists($configPath) ? json_decode(File::get($configPath), true)['campos'] : [];

        $campos = array_filter($camposRaw, fn($cfg) => !empty($cfg['incluir']));

        $registro->update($request->only(array_keys($campos)));

        return redirect()->route('produccion.abms.horma.index');
    }

    public function destroy($id)
    {
        Horma::destroy($id);
        return redirect()->route('produccion.abms.horma.index');
    }
}
