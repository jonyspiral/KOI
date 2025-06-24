<?php

namespace App\Models;

use App\Services\Mlibre\MlibreTokenService;
use Illuminate\Database\Eloquent\Model;
use App\Services\StockSkuService;
use Illuminate\Support\Facades\Http;


  class MlVariante extends Model
{
    protected $fillable = [
        'ml_id',
        'variation_id',
        'product_number',
        'seller_custom_field',
        'talle',
        'color',
        'modelo',
        'titulo',
        'seller_sku',
        'precio',
        'stock',
        'stock_flex',
        'stock_full',
        'family_id',
        'seller_custom_field_actual',
        'var_sku_sugerido',
        'nuevo_seller_custom_field',        
        'raw_json',
        'created_at',
        'updated_at',
        'sync_status',
        'sync_log',
        'manual_price',
        'manual_stock',
        'sync_log_stock', 
        'sync_log_precio',
         'sync_status_stock',
          'sync_status_precio'
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];

    public function publicacion()
    {
        return $this->hasOne(MlPublicacion::class, 'ml_id', 'ml_id');
    }
     public function family()
    {
        return $this->belongsTo(MlPublicacion::class, 'family_id', 'family_id');
    }
 public function skuVariante()
{
    return $this->belongsTo(SkuVariante::class, 'seller_custom_field', 'var_sku');
}
       public function mlPublication()
    {
        return $this->belongsTo(MlPublicacion::class, 'ml_id', 'ml_id');
    }


    // En MlVariante.php
public function sincronizarVariante(string $token): bool
{
    if (!$this->seller_custom_field) {
        $this->markAsError('SCF vacío o nulo');
        return false;
    }

    if (!$this->skuVariante) {
        $this->markAsError('SKU Variante no encontrado');
        return false;
    }

    if (!$this->ml_id) {
        $this->markAsError('Falta ml_id');
        return false;
    }

    $itemRes = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$this->ml_id}?attributes=variations");

    if (!$itemRes->ok()) {
        $this->markAsError("Error al obtener item ML ({$itemRes->status()})");
        return false;
    }

    $item = $itemRes->json();
    $hasVariants = !empty($item['variations']);

    try {
        if ($hasVariants) {
            if (!$this->variation_id) {
                $this->markAsError('Falta variation_id');
                return false;
            }

            $variacion = collect($item['variations'])->firstWhere('id', $this->variation_id);
            if (!$variacion) {
                $this->markAsError('Variación no encontrada en ML');
                return false;
            }

            if (!empty($variacion['inventory_id'])) {
                $this->markAsError('Stock FULL (no editable)');
                return false;
            }

            $put = Http::withToken($token)->put(
                "https://api.mercadolibre.com/items/{$this->ml_id}/variations/{$this->variation_id}",
                ['available_quantity' => (int) $this->stock]
            );
        } else {
            $put = Http::withToken($token)->put(
                "https://api.mercadolibre.com/items/{$this->ml_id}",
                ['available_quantity' => (int) $this->stock]
            );
        }

        if ($put->ok()) {
            $this->markAsSynced();
            return true;
        } else {
            $this->markAsError("Error PUT: " . $put->status() . " - " . $put->body());
            return false;
        }
    } catch (\Exception $e) {
        $this->markAsError("Excepción: " . $e->getMessage());
        return false;
    }
}

 /**
     * Envía el stock de esta variante a Mercado Libre.
     * Actualiza sync_status y sync_log según el resultado.
     */
   public function actualizarStockML(): bool
{
    if (!$this->ml_id) {
        $this->markAsError('stock', 'Falta ml_id');
        return false;
    }

    $ml_id = $this->ml_id;
$variation_id = $this->variation_id;

// Usar stock del SKU si no está override manual
$stock = $this->manual_stock ? (int) $this->stock : (int) optional($this->skuVariante)->stock;

try {
    $token = app(\App\Services\Mlibre\MlibreTokenService::class)->getValidAccessToken();

    // Consultar publicación para determinar si tiene variantes
    $itemRes = \Illuminate\Support\Facades\Http::withToken($token)->get("https://api.mercadolibre.com/items/{$ml_id}?attributes=variations");

    if (!$itemRes->ok()) {
        $this->markAsError('stock', "❌ Error al obtener item ML: " . $itemRes->status());
        return false;
    }

    $item = $itemRes->json();
    $has_variants = !empty($item['variations']); // ✅ CORRECTAMENTE DEFINIDO

    // Publicación SIN variantes
    if (!$has_variants) {
        $put = \Illuminate\Support\Facades\Http::withToken($token)->put("https://api.mercadolibre.com/items/{$ml_id}", [
            'available_quantity' => $stock
        ]);

        if ($put->ok()) {
            $this->stock = $stock;
            $this->markAsSynced('stock', "✅ Stock actualizado SIN variantes: $stock");
            return true;
        } else {
            $this->markAsError('stock', "❌ Error ML (sin variantes): " . $put->status() . " - " . $put->body());
            return false;
        }
    }

    // Publicación CON variantes
    if (!$variation_id) {
        $this->markAsError('stock', "❌ Falta variation_id para publicación con variantes");
        return false;
    }

    $variacion = collect($item['variations'])->firstWhere('id', $variation_id);

    if (!$variacion) {
        $this->markAsError('stock', "❌ Variación {$variation_id} no encontrada en ML");
        return false;
    }

    if (!empty($variacion['inventory_id'])) {
        $this->markAsSynced('stock', "ℹ️ Stock FULL (no editable)");
        return false;
    }

    $put = \Illuminate\Support\Facades\Http::withToken($token)->put(
        "https://api.mercadolibre.com/items/{$ml_id}/variations/{$variation_id}",
        ['available_quantity' => $stock]
    );

    if ($put->ok()) {
        $this->stock = $stock;
        $this->markAsSynced('stock', "✅ Stock actualizado CON variantes: $stock");
        return true;
    } else {
        $this->markAsError('stock', "❌ Error ML (con variantes): " . $put->status() . " - " . $put->body());
        return false;
    }

} catch (\Throwable $e) {
    $this->markAsError('stock', "❌ Excepción: " . $e->getMessage());
    return false;
}

}





public function syncFromSku(): bool
{
    if (!$this->skuVariante) {
        $this->sync_status = 'E';
        $this->sync_log = 'SKU no encontrado en view_sku_variantes';
        return false;
    }

    $updated = false;

    if (!$this->manual_price && $this->precio != $this->skuVariante->precio) {
        $this->precio = $this->skuVariante->precio;
        $updated = true;
    }

    if (!$this->manual_stock && $this->stock != $this->skuVariante->stock) {
        $this->stock = $this->skuVariante->stock;
        $updated = true;
    }

    if ($updated) {
        $this->sync_status = 'U';
        $this->sync_log = 'Actualizado desde SKU';
    } else {
        $this->sync_status = 'S';
        $this->sync_log = 'Sin cambios desde SKU';
    }

    return $updated;
}
public function has_variants(): bool
{
    return !empty($this->variation_id);
}



/**
 * Marca la variante como sincronizada correctamente.
 */
public function markAsSynced(string $campo = null, string $mensaje = 'Sincronizado correctamente con ML'): void
{
    if ($campo === 'stock') {
        $this->sync_status_stock = 'S';
        $this->sync_log_stock = "✅ $mensaje";
    } elseif ($campo === 'precio') {
        $this->sync_status_precio = 'S';
        $this->sync_log_precio = "✅ $mensaje";
    } else {
        $this->sync_status = 'S';
        $this->sync_log = $mensaje;
    }

    $this->save();
}

/**
 * Marca un error de sincronización.
 */
public function markAsError(string $message, string $campo = null): void
{
    if ($campo === 'stock') {
        $this->sync_status_stock = 'E';
        $this->sync_log_stock = $message;
    } elseif ($campo === 'precio') {
        $this->sync_status_precio = 'E';
        $this->sync_log_precio = $message;
    } else {
        $this->sync_status = 'E';
        $this->sync_log = $message;
    }

    $this->save();
}
}





   

