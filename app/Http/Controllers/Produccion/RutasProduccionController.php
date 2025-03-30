<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RutasProduccion;

class RutasProduccionController extends Controller
{
    public function index()
    {
        $registros = RutasProduccion::all();
        $modelo = 'Rutas Produccion';
        return view('produccion.abms.rutas_produccion.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = 'Rutas Produccion';
        return view('produccion.abms.rutas_produccion.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        RutasProduccion::create($request->only(['cod_ruta', 'denom_ruta', 'anulado', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }

    public function edit($id)
    {
        $registro = RutasProduccion::findOrFail($id);
        $modelo = 'Rutas Produccion';
        return view('produccion.abms.rutas_produccion.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = RutasProduccion::findOrFail($id);
        $registro->update($request->only(['cod_ruta', 'denom_ruta', 'anulado', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }

    public function destroy($id)
    {
        RutasProduccion::destroy($id);
        return redirect()->route('produccion.abms.rutas_produccion.index');
    }
}