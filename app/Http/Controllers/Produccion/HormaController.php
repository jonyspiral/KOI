<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Horma;

class HormaController extends Controller
{
    public function index()
    {
        $registros = Horma::all();
        $modelo = 'Horma';
        return view('produccion.abms.hormas.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = 'Horma';
        return view('produccion.abms.hormas.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        Horma::create($request->only(['cod_horma', 'denom_horma', 'talles_desde', 'talles_hasta', 'punto', 'observaciones', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.abms.hormas.index');
    }

    public function edit($id)
    {
        $registro = Horma::findOrFail($id);
        $modelo = 'Horma';
        return view('produccion.abms.hormas.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = Horma::findOrFail($id);
        $registro->update($request->only(['cod_horma', 'denom_horma', 'talles_desde', 'talles_hasta', 'punto', 'observaciones', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.abms.hormas.index');
    }

    public function destroy($id)
    {
        Horma::destroy($id);
        return redirect()->route('produccion.abms.hormas.index');
    }
}