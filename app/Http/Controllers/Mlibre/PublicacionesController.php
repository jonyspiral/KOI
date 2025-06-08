<?php

namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MlPublicacion;

class PublicacionesController extends Controller
{
   public function index(Request $request)
{
    $campo = $request->get('campo');
    $buscar = $request->get('buscar');

    $query = MlPublicacion::query();

    if ($campo && $buscar) {
        // Validar campos permitidos para evitar inyección
        $camposPermitidos = ['ml_id', 'ml_reference', 'ml_name', 'status'];
        if (in_array($campo, $camposPermitidos)) {
            $query->where($campo, 'like', '%' . $buscar . '%');
        }
    }

    $publicaciones = $query->paginate(20);

    return view('mlibre.publicaciones.index', compact('publicaciones', 'campo', 'buscar'));
}


    public function edit($id)
{
    $publicacion = MlPublicacion::with('variantes')->findOrFail($id);
    return view('mlibre.publicaciones.edit', compact('publicacion'));
}

   

public function update(Request $request, $id)
{
    $request->validate([
        'ml_name'        => 'nullable|string|max:255',
        'ml_description' => 'nullable|string',
        'ml_reference'   => 'nullable|string|max:255',
        'mlibre_precio'  => 'nullable|numeric|min:0',
        'mlibre_stock'   => 'nullable|integer|min:0',
    ]);

    $publicacion = MlPublicacion::findOrFail($id);

    $publicacion->update($request->only([
        'ml_name',
        'ml_description',
        'ml_reference',
        'mlibre_precio',
        'mlibre_stock',
    ]));

    return redirect()
        ->route('mlibre.publicaciones.index')
        ->with('success', '✅ Publicación actualizada correctamente.');
}


}
