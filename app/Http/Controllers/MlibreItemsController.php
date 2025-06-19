<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MlibreToken;

class MlibreItemsController extends Controller

{
    public function index()
{
    $items = MeliItem::latest()->take(50)->get(); // o el criterio que prefieras
    return view('meli.items.index', compact('items'));
}

    private function getToken()
    {
        return MlibreToken::where('user_id', 2481996336)->latest()->first()?->access_token;
    }

   public function listar()
{
    $token = $this->getToken();

    // Paso 1: obtener los IDs
    $res = Http::withToken($token)
        ->get('https://api.mercadolibre.com/users/2481996336/items/search')
        ->json();

    $itemIds = $res['results'] ?? [];

    // Paso 2: obtener detalles de cada ítem
    $items = [];

    foreach ($itemIds as $id) {
        $item = Http::withToken($token)
            ->get("https://api.mercadolibre.com/items/$id")
            ->json();
        $items[] = $item;
    }

    // Paso 3: enviar a la vista Blade
    return view('meli.items.index', compact('items'));
}

    public function ver($id)
    {
        $token = $this->getToken();
        $response = Http::withToken($token)
            ->get("https://api.mercadolibre.com/items/$id");

        return response()->json($response->json());
    }

public function activar($id)
{
    $token = $this->getToken();

    Http::withToken($token)
        ->put("https://api.mercadolibre.com/items/{$id}", [
            'status' => 'active',
        ]);

    return redirect()->back()->with('status', "Ítem $id activado.");
}


 public function pausar($id)
{
    $token = $this->getToken();

    Http::withToken($token)
        ->put("https://api.mercadolibre.com/items/{$id}", [
            'status' => 'paused',
        ]);

    return redirect()->back()->with('status', "Ítem $id pausado.");
}


public function actualizar(Request $request, $id)
{
    $token = $this->getToken();

    $payload = [
        'title' => $request->input('title'),
        'price' => (float)$request->input('price'),
    ];

    $res = Http::withToken($token)
        ->put("https://api.mercadolibre.com/items/{$id}", $payload);

    return redirect()->back()->with('status', 'Item actualizado correctamente.');
}

}
