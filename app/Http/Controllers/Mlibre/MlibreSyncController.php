<?php

namespace App\Http\Controllers\Mlibre;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;

class MlibreSyncController extends Controller
{
    public function sincronizar(Request $request)
    {
        try {
            Artisan::call('mlibre:actualizar-sku', ['--sync' => true]);
            return back()->with('success', '✅ SKUs sincronizados correctamente con Mercado Libre.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error al sincronizar: ' . $e->getMessage());
        }
    }
}

