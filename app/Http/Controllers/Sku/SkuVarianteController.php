<?php

namespace App\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use App\Models\SkuVariante;
use Illuminate\Http\Request;

class SkuVarianteController extends Controller
{
    public function index(Request $request)
    {
        $query = SkuVariante::query();

        // Filtros por campo
        if ($request->filled('sku')) {
            $query->where('sku', 'like', '%' . $request->sku . '%');
        }

        if ($request->filled('var_sku')) {
            $query->where('var_sku', 'like', '%' . $request->var_sku . '%');
        }

        if ($request->filled('cod_articulo')) {
            $query->where('cod_articulo', 'like', '%' . $request->cod_articulo . '%');
        }

        if ($request->filled('cod_color_articulo')) {
            $query->where('cod_color_articulo', 'like', '%' . $request->cod_color_articulo . '%');
        }

        if ($request->filled('ml_name')) {
            $query->where('ml_name', 'like', '%' . $request->ml_name . '%');
        }

        if ($request->filled('color')) {
            $query->where('color', 'like', '%' . $request->color . '%');
        }

        if ($request->filled('talle')) {
            $query->where('talle', $request->talle);
        }

        if ($request->filled('precio')) {
            $query->where('precio', $request->precio);
        }

        // Orden y paginación
        $registros = $query->orderBy('var_sku')->paginate(100)->appends($request->all());

        return view('sku.sku_variantes.index', compact('registros'));
    }

    public function show($id)
    {
        $registro = SkuVariante::findOrFail($id);
        return view('sku.sku_variantes.show', compact('registro'));
    }
}
