<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeccionesProduccion;

class SeccionesProduccionController extends Controller
{
    public function index()
    {
        $registros = SeccionesProduccion::all();
        $modelo = 'Secciones Produccion';
        return view('produccion/secciones_produccion.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = 'Secciones Produccion';
        return view('produccion/secciones_produccion.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        SeccionesProduccion::create($request->only(['cod_seccion', 'ejecucion', 'denom_seccion', 'denom_corta', 'unid_med_cap_prod', 'interrumpible', 'anulado', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.secciones_produccion.index');
    }

    public function edit($id)
    {
        $registro = SeccionesProduccion::findOrFail($id);
        $modelo = 'Secciones Produccion';
        return view('produccion/secciones_produccion.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = SeccionesProduccion::findOrFail($id);
        $registro->update($request->only(['cod_seccion', 'ejecucion', 'denom_seccion', 'denom_corta', 'unid_med_cap_prod', 'interrumpible', 'anulado', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.secciones_produccion.index');
    }

    public function destroy($id)
    {
        SeccionesProduccion::destroy($id);
        return redirect()->route('produccion.secciones_produccion.index');
    }
}