<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ArticulosNew;

class ArticulosNewController extends Controller
{
    public function index()
    {
        $registros = ArticulosNew::all();
        $modelo = 'Articulos New';
        return view('produccion/articulos_new.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = 'Articulos New';
        return view('produccion/articulos_new.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        ArticulosNew::create($request->only(['cod_articulo', 'denom_articulo', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.articulos_new.index');
    }

    public function edit($id)
    {
        $registro = ArticulosNew::findOrFail($id);
        $modelo = 'Articulos New';
        return view('produccion/articulos_new.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = ArticulosNew::findOrFail($id);
        $registro->update($request->only(['cod_articulo', 'denom_articulo', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.articulos_new.index');
    }

    public function destroy($id)
    {
        ArticulosNew::destroy($id);
        return redirect()->route('produccion.articulos_new.index');
    }
}