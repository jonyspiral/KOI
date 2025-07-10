<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MlVariante;
use App\Services\Mlibre\MlibreTokenService;
use App\Services\Mlibre\ValidateMlVariationsService;

class MlibreActualizarScf extends Command
{
    protected $signature = 'mlibre:actualizar-scf {--fill=} {--all}';
    protected $description = 'Actualiza el SCF de variantes ML usando seller_custom_field y marca vigencia';

    public function handle()
    {
        $token = app(MlibreTokenService::class)->getValidAccessToken();
        $validator = app(ValidateMlVariationsService::class);

        $mlIds = collect();

        if ($this->option('fill')) {
            $mlIds = collect([$this->option('fill')]);
        } elseif ($this->option('all')) {
            $mlIds = MlVariante::whereNotNull('variation_id')
                ->where('vigente', true)
                ->distinct()
                ->pluck('ml_id');
        } else {
            $this->error('⚠️ Debes usar --fill=MLA... o --all');
            return;
        }

        foreach ($mlIds as $mlId) {
            $variantes = MlVariante::where('ml_id', $mlId)->get();

            $variations = [];
            $variacionesInvalidas = [];

            foreach ($variantes as $v) {
                if ($v->variation_id) {
                    if ($validator->existsInML($mlId, $v->variation_id, $token)) {
                        $variations[] = [
                            'id' => (int) $v->variation_id,
                            'seller_custom_field' => $v->seller_custom_field,
                        ];
                    } else {
                        $variacionesInvalidas[] = $v->variation_id;
                        $v->vigente = false;
                        $v->save();
                    }
                }
            }

            if (count($variations) === 0) {
                $this->warn("❌ No hay variations válidas para $mlId");
                continue;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer $token",
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'KOI2LaravelSync/1.0 (spiralshoessa@gmail.com)'
            ])->put("https://api.mercadolibre.com/items/$mlId", [
                'variations' => $variations
            ]);

            if ($response->successful()) {
                $this->info("✅ $mlId actualizado con éxito.");
            } else {
                $status = $response->status();
                if ($status === 429) {
                    $this->error("⏳ $mlId recibió HTTP 429 (Too Many Requests), se recomienda reintentar más tarde.");
                } elseif ($status === 400 || $status === 404) {
                    foreach ($variantes as $v) {
                        $v->vigente = false;
                        $v->save();
                    }
                    $this->error("❌ Error al actualizar $mlId - HTTP $status - marcado como NO vigente.");
                } else {
                    $this->error("❌ Error al actualizar $mlId (HTTP $status)");
                }

                if (count($variacionesInvalidas)) {
                    $this->line("Variaciones inválidas: " . implode(', ', $variacionesInvalidas));
                    dd($variacionesInvalidas); // 🧪 Debug
                }
            }
        }
    }
}
