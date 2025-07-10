<?php
// 📄 Generado por Sofía
// Función: Comparar timestamps entre dos modelos para detectar diferencias en campos clave
// Fecha: 2025-06-24
// Fuente: propuesta de Vicente en KOI
//Ultima edicion del canvas

namespace App\Services\Sync;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ComparadorTimestampsService
{
    /**
     * Compara los timestamps (y opcionalmente otros campos) entre dos modelos
     * Si detecta diferencias, marca el registro destino con sync_status = 'U'.
     *
     * @param  string $modeloOrigen      Modelo base (ej. SkuVariante::class)
     * @param  string $modeloDestino     Modelo destino (ej. MlVariante::class)
     * @param  string $campoClave        Campo común de comparación (ej. var_sku)
     * @param  array  $camposAComparar   Campos adicionales a comparar (además de updated_at)
     * @return Collection                Registros del modeloDestino que tienen diferencias
     */
    public function compararTimestamps(
        string $modeloOrigen,
        string $modeloDestino,
        string $campoClave,
        array $camposAComparar = []
    ): Collection {
        $origen = app($modeloOrigen)->all()->keyBy($campoClave);
        $destino = app($modeloDestino)->all();

        $diferentes = collect();

        foreach ($destino as $registroDestino) {
            $clave = $registroDestino->{$campoClave};
            $registroOrigen = $origen->get($clave);

            if (!$registroOrigen) {
                continue; // No hay matching, no se puede comparar
            }

            $updatedOrigen  = optional($registroOrigen->updated_at)->timestamp;
            $updatedDestino = optional($registroDestino->updated_at)->timestamp;

            $hayDiferencia = $updatedOrigen > $updatedDestino;

            foreach ($camposAComparar as $campo) {
                if ($registroOrigen->{$campo} !== $registroDestino->{$campo}) {
                    $hayDiferencia = true;
                    break;
                }
            }

            if ($hayDiferencia) {
                if (isset($registroDestino->sync_status)) {
                    $registroDestino->sync_status = 'U';
                    $registroDestino->save();
                }
                $diferentes->push($registroDestino);
            }
        }

        return $diferentes;
    }
}
