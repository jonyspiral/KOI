<?php

namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MlVariante;
use App\Services\MlibreTokenService;
use Illuminate\Support\Facades\Http;

class MlibreVariantesController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort');
        $dir  = $request->get('dir', 'asc');

        $query = MlVariante::query()
            ->with('publicacion')
            ->join('ml_publicaciones', 'ml_publicaciones.ml_id', '=', 'ml_variantes.ml_id')
            ->select('ml_variantes.*');

        if ($request->filled('ml_id')) {
            $query->where('ml_variantes.ml_id', 'like', '%' . $request->ml_id . '%');
        }

        if ($request->filled('color')) {
            $query->where('ml_variantes.color', 'like', '%' . $request->color . '%');
        }

        if ($request->filled('talle')) {
            $query->where('ml_variantes.talle', $request->talle);
        }

        if ($request->filled('modelo')) {
            $query->where('ml_variantes.modelo', 'like', '%' . $request->modelo . '%');
        }
        if ($request->filled('seller_sku')) {
                $query->where('seller_sku', 'like', '%' . $request->seller_sku . '%');
            }


        if ($request->filled('titulo')) {
            $query->where('ml_publicaciones.ml_name', 'like', '%' . $request->titulo . '%');
        }

        // Orden dinámico o por defecto en 3 campos
        if ($sort) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderBy('ml_variantes.color')
                  ->orderBy('ml_variantes.talle')
                  ->orderByDesc('ml_variantes.stock');
        }

        $variantes = $query->get();

        return view('mlibre.variantes', compact('variantes'));
    }

    public function guardar(Request $request)
    {
        foreach ($request->input('variantes', []) as $id => $data) {
            MlVariante::where('id', $id)->update([
                'nuevo_seller_custom_field' => $data['nuevo_seller_custom_field'] ?? null,
            ]);
        }

        return redirect()->route('mlibre.variantes.index')->with('success', 'Cambios guardados correctamente');
    }

   
    public function publicarSCF($id)
{
    $variante = MlVariante::with('publicacion')->findOrFail($id);

    if (!$variante->ml_id || !$variante->variation_id || !$variante->nuevo_seller_custom_field) {
        return back()->with('error', 'Datos incompletos para publicar SCF.');
    }

    try {
        $token = (new MlibreTokenService())->getValidAccessToken();

        $response = Http::withToken($token)->put(
            "https://api.mercadolibre.com/items/{$variante->ml_id}/variations/{$variante->variation_id}",
            [
                'seller_custom_field' => $variante->nuevo_seller_custom_field,
            ]
        );

        if ($response->ok()) {
            $variante->update([
                'seller_custom_field_actual' => $variante->nuevo_seller_custom_field,
                'sincronizado' => 1,
            ]);
            return back()->with('success', 'SCF publicado correctamente.');
        } else {
            return back()->with('error', 'Error al publicar: ' . $response->body());
        }

    } catch (\Exception $e) {
        return back()->with('error', 'Error al obtener token o enviar petición: ' . $e->getMessage());
    }
}

}
