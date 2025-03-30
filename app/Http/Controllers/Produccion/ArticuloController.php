<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;

class ArticuloController extends Controller
{
    public function index()
    {
        $registros = Articulo::all();
        $modelo = 'Articulo';
        return view('produccion.abms.articulos.index', compact('registros', 'modelo'));
    }

    public function create()
    {
        $modelo = 'Articulo';
        return view('produccion.abms.articulos.form', compact('modelo'));
    }

    public function store(Request $request)
    {
        Articulo::create($request->only(['cod_articulo', 'cod_ruta', 'cod_linea', 'cod_marca', 'cod_rango', 'denom_articulo', 'vigente', 'cod_horma', 'naturaleza', 'cod_familia_producto', 'denom_articulo_largo', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.abms.articulos.index');
    }

    public function edit($id)
    {
        $registro = Articulo::findOrFail($id);
        $modelo = 'Articulo';
        return view('produccion.abms.articulos.form', compact('registro', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $registro = Articulo::findOrFail($id);
        $registro->update($request->only(['cod_articulo', 'cod_ruta', 'cod_linea', 'cod_marca', 'cod_rango', 'denom_articulo', 'vigente', 'cod_horma', 'naturaleza', 'cod_familia_producto', 'denom_articulo_largo', 'created_at', 'updated_at', 'sync_status']));
        return redirect()->route('produccion.abms.articulos.index');
    }

    public function destroy($id)
    {
        Articulo::destroy($id);
        return redirect()->route('produccion.abms.articulos.index');
    }
}