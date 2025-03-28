<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\;

class Controller extends Controller
{
    public function index()
    {
        $registros = ::all();
        $modelo = '';
        return view('produccion/.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = '';
        return view('produccion/.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        ::create($request->only(['cod_articulo', 'denom_articulo', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion..index');
    }

    public function edit($id)
    {
        $registro = ::findOrFail($id);
        $modelo = '';
        return view('produccion/.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = ::findOrFail($id);
        $registro->update($request->only(['cod_articulo', 'denom_articulo', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion..index');
    }

    public function destroy($id)
    {
        ::destroy($id);
        return redirect()->route('produccion..index');
    }
}