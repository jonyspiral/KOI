<?php

namespace App\Http\Controllers\Sku;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SkuVariante;
use Illuminate\Support\Facades\Schema;

class SkuVarianteController extends Controller
{
    public function index(Request $request)
    {
        $query = SkuVariante::query();

        // Campos que se pueden filtrar
        $camposFiltrables = [
            'sku', 'var_sku', 'ml_name', 'cod_articulo', 'cod_color_articulo',
            'familia', 'color', 'talle', 'precio'
        ];

        foreach ($camposFiltrables as $campo) {
            if ($request->filled($campo)) {
                $query->where($campo, 'like', '%' . $request->$campo . '%');
            }
        }

        // Ordenamiento dinámico
        $sort = $request->get('sort', 'sku');
        $dir  = $request->get('dir', 'asc');

        if (Schema::hasColumn('sku_variantes', $sort)) {
            $query->orderBy($sort, $dir);
        }

        // Paginación con preservación de filtros
        $registros = $query->paginate(30)->appends($request->query());

        return view('sku.sku_variantes.index', compact('registros'));
    }

    public function show($id)
    {
        $registro = SkuVariante::findOrFail($id);
        return view('sku.sku_variantes.show', compact('registro'));
    }
}
