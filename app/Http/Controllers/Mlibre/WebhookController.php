<?php

namespace App\Http\Controllers\Mlibre;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('🔔 Webhook recibido de Mercado Libre', [
            'data' => $request->all()
        ]);

        return response()->json(['status' => 'ok']);
    }
}
