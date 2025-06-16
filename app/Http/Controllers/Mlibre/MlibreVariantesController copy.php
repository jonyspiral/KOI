<?php

namespace App\Http\Controllers\Mlibre;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MlVariante;
use App\Services\Mlibre\MlibreTokenService;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MlVariantesExport;
use Illuminate\Support\Sleep;

class MlibreVariantesControllercopy extends Controller
{
   public function index(Request $request)
{
    $sort = $request->get('sort');
    $dir  = $request->get('dir', 'asc');

    $query = MlVariante::query()
        ->with('publicacion')
        ->join('ml_publicaciones', 'ml_publicaciones.ml_id', '=', 'ml_variantes.ml_id')
        ->select('ml_variantes.*', 'ml_publicaciones.ml_name', 'ml_publicaciones.status', 'ml_publicaciones.logistic_type');

    // Filtros disponibles
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
        $query->where('ml_variantes.seller_sku', 'like', '%' . $request->seller_sku . '%');
    }

    if ($request->filled('titulo')) {
        $query->where('ml_publicaciones.ml_name', 'like', '%' . $request->titulo . '%');
    }

    if ($request->filled('variation_id')) {
        $query->where('ml_variantes.variation_id', $request->variation_id);
    }

    if ($request->filled('product_number')) {
        $query->where('ml_variantes.product_number', 'like', '%' . $request->product_number . '%');
    }

    if ($request->filled('seller_custom_field')) {
        $query->where('ml_variantes.seller_custom_field', 'like', '%' . $request->seller_custom_field . '%');
    }

    if ($sort) {
        $query->orderBy($sort, $dir);
    } else {
        $query->orderBy('ml_variantes.color')
              ->orderBy('ml_variantes.talle')
              ->orderByDesc('ml_variantes.stock');
    }

    $variantes = $query->paginate(50)->appends($request->except('page'));

    return view('mlibre.variantes', compact('variantes'));
}




public function guardar(Request $request)
    {
        $successCount = 0;
        $errorMessages = [];
        $maxRetries = 3;
        $baseDelay = 1000;
        $requestDelay = 200;

        foreach ($request->input('variantes', []) as $id => $data) {
            $variante = MlVariante::find($id);

            if (!$variante) {
                $errorMessages[] = "Variante con ID {$id} no encontrada.";
                continue;
            }

            $scf = $data['seller_custom_field'] ?? null;

            // Actualizar seller_custom_field localmente
            $variante->update([
                'seller_custom_field' => $scf,
            ]);

            // Si no hay seller_custom_field o no cambió, considerar como éxito
            if (!$scf || $scf === $variante->seller_custom_field_actual) {
                $successCount++;
                \Log::info('Variante sin cambios en ML', [
                    'variante_id' => $id,
                    'ml_id' => $variante->ml_id,
                    'variation_id' => $variante->variation_id,
                    'sku' => $scf,
                ]);
                continue;
            }

            // Publicar en Mercado Libre
            if (!$variante->ml_id) {
                $errorMessages[] = "Variante ID {$id}: ml_id no definido.";
                continue;
            }

            $attempt = 0;
            $success = false;
            $sku = trim($scf);

            // Verificar si la variante existe en Mercado Libre
            try {
                $token = (new MlibreTokenService())->getValidAccessToken(448490530);
                $url = $variante->variation_id
                    ? "https://api.mercadolibre.com/items/{$variante->ml_id}?attributes=variations"
                    : "https://api.mercadolibre.com/items/{$variante->ml_id}?attributes=seller_custom_field";

                $response = Http::withToken($token)->get($url);

                if (!$response->ok()) {
                    \Log::error('Error al verificar existencia de variante', [
                        'variante_id' => $id,
                        'ml_id' => $variante->ml_id,
                        'variation_id' => $variante->variation_id,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    $errorMessages[] = "Variante ID {$id}: Error al verificar existencia: {$response->status()} - {$response->body()}";
                    continue;
                }

                $data = $response->json();
                $variantExists = true;

                if ($variante->variation_id) {
                    $variantExists = false;
                    $variations = $data['variations'] ?? [];
                    foreach ($variations as $variation) {
                        if ($variation['id'] == $variante->variation_id) {
                            $variantExists = true;
                            break;
                        }
                    }
                }

                if (!$variantExists) {
                    $variante->update([
                        'sincronizado' => 0,
                        'notes' => "Variante no encontrada en ML (variation_id {$variante->variation_id})",
                    ]);
                    \Log::warning('Variante no encontrada en ML', [
                        'variante_id' => $id,
                        'ml_id' => $variante->ml_id,
                        'variation_id' => $variante->variation_id,
                    ]);
                    $errorMessages[] = "Variante ID {$id}: Variante no encontrada en Mercado Libre.";
                    continue;
                }
            } catch (\Exception $e) {
                \Log::error('Excepción al verificar variante en ML', [
                    'variante_id' => $id,
                    'ml_id' => $variante->ml_id,
                    'variation_id' => $variante->variation_id,
                    'error' => $e->getMessage(),
                ]);
                $errorMessages[] = "Variante ID {$id}: Error al verificar variante: {$e->getMessage()}";
                continue;
            }

            while ($attempt < $maxRetries && !$success) {
                $attempt++;
                try {
                    $token = (new MlibreTokenService())->getValidAccessToken(448490530);
                    \Log::info('Token usado para guardar: ' . substr($token, 0, 10) . '...');

                    $payload = [
                        'seller_custom_field' => $sku
                    ];

                    $url = $variante->variation_id
                        ? "https://api.mercadolibre.com/items/{$variante->ml_id}/variations/{$variante->variation_id}"
                        : "https://api.mercadolibre.com/items/{$variante->ml_id}";

                    $response = Http::withToken($token)
                        ->asJson()
                        ->put($url, $payload);

                    if ($response->ok()) {
                        $variante->update([
                            'seller_custom_field_actual' => $sku,
                            'sincronizado' => 1,
                        ]);
                        $successCount++;
                        \Log::info('SCF actualizado en ML', [
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                        ]);
                        $success = true;
                    } elseif ($response->status() === 404) {
                        $variante->update([
                            'sincronizado' => 0,
                            'notes' => "Variante no encontrada en ML (variation_id {$variante->variation_id})",
                        ]);
                        \Log::warning('Variante no encontrada al intentar actualizar SCF', [
                            'variante_id' => $id,
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                        $errorMessages[] = "Variante ID {$id}: Variante no encontrada en Mercado Libre (404).";
                        break;
                    } elseif ($response->status() === 409 && $attempt < $maxRetries) {
                        $delay = $baseDelay * pow(2, $attempt - 1);
                        \Log::warning('Conflicto 409, reintentando', [
                            'attempt' => $attempt,
                            'delay_ms' => $delay,
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                        ]);
                        Sleep::for($delay)->milliseconds();
                        continue;
                    } elseif ($response->status() === 429) {
                        $retryAfter = $response->header('Retry-After', 5);
                        \Log::warning('Límite de solicitudes 429, esperando', [
                            'retry_after_seconds' => $retryAfter,
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                        ]);
                        Sleep::for($retryAfter)->seconds();
                        continue;
                    } else {
                        \Log::error('❌ Error al publicar SCF en ML', [
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                            'status' => $response->status(),
                            'body' => $response->body(),
                            'url' => $url,
                            'payload' => $payload,
                            'attempt' => $attempt,
                        ]);
                        $errorMessages[] = "Variante ID {$id}: Error al publicar SCF: {$response->status()} - {$response->body()}";
                        break;
                    }
                } catch (\Exception $e) {
                    \Log::error('🔥 Excepción al publicar SCF en ML', [
                        'ml_id' => $variante->ml_id,
                        'variation_id' => $variante->variation_id,
                        'sku' => $sku ?? 'N/A',
                        'error' => $e->getMessage(),
                        'url' => $url ?? 'N/A',
                        'attempt' => $attempt,
                    ]);
                    $errorMessages[] = "Variante ID {$id}: Error al publicar SCF: {$e->getMessage()}";
                    break;
                }
            }

            Sleep::for($requestDelay)->milliseconds();
        }

        $message = "Se procesaron {$successCount} variantes correctamente.";
        if (!empty($errorMessages)) {
            $message .= " Errores: " . implode(' | ', $errorMessages);
            return redirect()->route('mlibre.variantes.index')->with('error', $message);
        }

        return redirect()->route('mlibre.variantes.index')->with('success', $message);
    }



   
   public function publicarSCF($id)
{
    $variante = MlVariante::with('publicacion')->findOrFail($id);

    if (!$variante->ml_id || !$variante->nuevo_seller_custom_field) {
        return back()->with('error', 'Datos incompletos para publicar SCF.');
    }

    try {
        $token = (new MlibreTokenService())->getValidAccessToken();
        $sku = trim($variante->nuevo_seller_custom_field);

        $payload = [
            'seller_custom_field' => $sku
        ];

        $url = $variante->variation_id
            ? "https://api.mercadolibre.com/items/{$variante->ml_id}/variations/{$variante->variation_id}"
            : "https://api.mercadolibre.com/items/{$variante->ml_id}";

        $response = Http::withToken($token)
            ->asJson()
            ->put($url, $payload);

        if ($response->ok()) {
            $variante->update([
                'seller_custom_field' => $sku,
                'seller_custom_field_actual' => $sku,
                'sincronizado' => 1,
            ]);

            return back()->with('success', 'SCF actualizado correctamente.');
        }

        \Log::error('Error al publicar en ML', [
            'estado' => $response->status(),
            'mensaje' => $response->body(),
            'url' => $url,
            'payload' => $payload,
        ]);
        return back()->with('error', 'Error al publicar: ' . $response->status() . ' - ' . $response->body());
    } catch (\Exception $e) {
        \Log::error('Excepción en publicación ML', [
            'error' => $e->getMessage(),
            'e' => $url ?? null,
            'error' => $payload ?? null
        ]);
        return back()->with('error', 'Error al obtener token o enviar solicitud: ' . $e->getMessage());
    }
}
public function guardarIndividual(Request $request, $id)
{
    $variante = MlVariante::findOrFail($id);
    $errorMessages = [];
    $success = false;

    try {
        $scf = $request->validate([
            'seller_custom_field' => 'nullable|string|max:255',
        ])['seller_custom_field'] ?? null;

        // Inicializar $sku
        $sku = $scf ? trim($scf) : null;

        // Loggear valor antes de la actualización
        \Log::info('Valor de seller_custom_field antes de actualizar', [
            'variante_id' => $id,
            'seller_custom_field' => $variante->seller_custom_field,
        ]);

        // Actualizar seller_custom_field localmente
        $variante->update([
            'seller_custom_field' => $scf,
        ]);

        // Refrescar y loggear valor después de la actualización
        $variante->refresh();
        \Log::info('Valor de seller_custom_field después de actualizar', [
            'variante_id' => $id,
            'seller_custom_field' => $variante->seller_custom_field,
        ]);

        // Si no hay seller_custom_field o no cambió respecto a ML, retornar éxito
        if (!$sku || $sku === $variante->seller_custom_field_actual) {
            \Log::info('Sin cambios detectados en SCF para variante', ['variante_id' => $id, 'sku' => $sku]);
            return back()->with('success', "Variante ID {$id} guardada correctamente (sin cambios en ML).");
        }

        // Publicar en Mercado Libre
        if (!$variante->ml_id) {
            $errorMessages[] = "Error: ml_id no definido.";
        } else {
            $token = (new MlibreTokenService())->getValidAccessToken(448490530);
            \Log::info('Token usado para guardar_individual: ' . substr($token, 0, 10) . '...');

            // Verificar si la variante existe
            $url = $variante->variation_id
                ? "https://api.mercadolibre.com/items/{$variante->ml_id}?attributes=variations"
                : "https://api.mercadolibre.com/items/{$variante->ml_id}?attributes=seller_custom_field";

            $response = Http::withToken($token)->get($url);

            if (!$response->ok()) {
                \Log::error('Error al verificar existencia de variante (individual)', [
                    'variante_id' => $id,
                    'ml_id' => $variante->ml_id,
                    'variation_id' => $variante->variation_id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $errorMessages[] = "Error al verificar existencia: {$response->status()} - {$response->body()}";
            } else {
                $data = $response->json();
                $variantExists = true;

                if ($variante->variation_id) {
                    $variantExists = false;
                    $variations = $data['variations'] ?? [];
                    foreach ($variations as $variation) {
                        if ($variation['id'] == $variante->variation_id) {
                            $variantExists = true;
                            break;
                        }
                    }
                }

                if (!$variantExists) {
                    \Log::warning('Variante no encontrada en ML (individual)', [
                        'variante_id' => $id,
                        'ml_id' => $variante->ml_id,
                        'variation_id' => $variante->variation_id,
                    ]);
                    $errorMessages[] = "Variante no encontrada en Mercado Libre.";
                } else {
                    // Proceder con la actualización
                    $payload = [
                        'seller_custom_field' => $sku
                    ];

                    $url = $variante->variation_id
                        ? "https://api.mercadolibre.com/items/{$variante->ml_id}/variations/{$variante->variation_id}"
                        : "https://api.mercadolibre.com/items/{$variante->ml_id}";

                    $response = Http::withToken($token)
                        ->asJson()
                        ->put($url, $payload);

                    if ($response->ok()) {
                        $variante->update([
                            'seller_custom_field_actual' => $sku,
                            'sincronizado' => 1,
                        ]);
                        \Log::info('SCF actualizado en ML (individual)', [
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                        ]);
                        $success = true;
                        return back()->with('success', "Variante ID {$id} guardada correctamente.");
                    } elseif ($response->status() === 404) {
                        \Log::warning('Variante no encontrada al actualizar SCF (individual)', [
                            'variante_id' => $id,
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                        $errorMessages[] = "Variante no encontrada en Mercado Libre (404).";
                    } else {
                        \Log::error('❌ Error al publicar SCF en ML (individual)', [
                            'ml_id' => $variante->ml_id,
                            'variation_id' => $variante->variation_id,
                            'sku' => $sku,
                            'status' => $response->status(),
                            'body' => $response->body(),
                            'url' => $url,
                            'payload' => $payload,
                        ]);
                        $errorMessages[] = "Error al publicar SCF: {$response->status()} - {$response->body()}";
                    }
                }
            }
        }
    } catch (\Exception $e) {
        \Log::error('🔥 Excepción al publicar SCF en ML (individual)', [
            'ml_id' => $variante->ml_id ?? 'N/A',
            'variation_id' => $variante->variation_id ?? 'N/A',
            'sku' => $sku ?? 'null',
            'error' => $e->getMessage(),
            'url' => $url ?? 'null',
        ]);
        $errorMessages[] = "Error: {$e->getMessage()}";
    }

    if ($success || empty($errorMessages)) {
        return back()->with('success', "Variante ID {$id} guardada correctamente.");
    }

    return back()->with('error', "Variante ID {$id}: " . implode(' | ', $errorMessages));
}




public function exportar()
{
    return Excel::download(new MlVariantesExport, 'ml_variantes.xlsx');
}
public function verificarSCF($id)
{
    $variante = MlVariante::with('publicacion')->findOrFail($id);

    if (!$variante->ml_id) {
        return back()->with('error', 'El ml_id no está definido para esta variante.');
    }

    try {
        $token = (new MlibreTokenService())->getValidAccessToken(448490530);
        \Log::info('Token usado para verificarSCF: ' . substr($token, 0, 10) . '...');

        $url = $variante->variation_id
            ? "https://api.mercadolibre.com/items/{$variante->ml_id}?attributes=variations"
            : "https://api.mercadolibre.com/items/{$variante->ml_id}?attributes=seller_custom_field";

        $response = Http::withToken($token)->get($url);

        if ($response->ok()) {
            $data = $response->json();
            $ml_seller_custom_field = null;

            if ($variante->variation_id) {
                $variations = $data['variations'] ?? [];
                foreach ($variations as $variation) {
                    if ($variation['id'] == $variante->variation_id) {
                        $ml_seller_custom_field = $variation['seller_custom_field'] ?? null;
                        break;
                    }
                }
            } else {
                $ml_seller_custom_field = $data['seller_custom_field'] ?? null;
            }

            \Log::info('Verificación de SCF', [
                'ml_id' => $variante->ml_id,
                'variation_id' => $variante->variation_id,
                'ml_seller_custom_field' => $ml_seller_custom_field,
                'local_seller_custom_field' => $variante->seller_custom_field,
                'local_seller_custom_field_actual' => $variante->seller_custom_field_actual,
            ]);

            if ($ml_seller_custom_field === null) {
                return back()->with('warning', 'No se encontró seller_custom_field en Mercado Libre.');
            }

            if ($ml_seller_custom_field === $variante->seller_custom_field && $ml_seller_custom_field === $variante->seller_custom_field_actual) {
                return back()->with('success', 'El seller_custom_field está sincronizado correctamente: ' . $ml_seller_custom_field);
            }

            return back()->with('error', 'El seller_custom_field no está sincronizado. ML: ' . ($ml_seller_custom_field ?? 'N/A') . ', Local: ' . ($variante->seller_custom_field ?? 'N/A') . ', Actual: ' . ($variante->seller_custom_field_actual ?? 'N/A'));
        }

        \Log::error('Error al verificar SCF en ML', [
            'status' => $response->status(),
            'body' => $response->body(),
            'url' => $url,
        ]);
        return back()->with('error', 'Error al verificar en ML: ' . $response->status() . ' - ' . $response->body());
    } catch (\Exception $e) {
        \Log::error('Excepción al verificar SCF en ML', [
            'error' => $e->getMessage(),
            'url' => $url ?? null,
        ]);
        return back()->with('error', 'Error al obtener token o verificar: ' . $e->getMessage());
    }
}
}
