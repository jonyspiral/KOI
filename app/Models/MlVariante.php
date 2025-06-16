<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\StockSkuService;


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
        'sincronizado',
        'raw_json',
        'created_at',
        'updated_at',
        'sync_status',
        'sync_log',
        'manual_price',
        'manual_stock',
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
 /**
     * Envía el stock de esta variante a Mercado Libre.
     * Actualiza sync_status y sync_log según el resultado.
     */
    public function actualizarStockML(): bool
{
    if (!$this->ml_id) {
        $this->markAsError('Falta ml_id');
        return false;
    }

    $ml_id = $this->ml_id;
    $variation_id = $this->variation_id;
    $stock = (int) ($this->stock ?? 0);

    try {
        $token = app(MlibreTokenService::class)->getValidAccessToken();

        // Consultar publicación para determinar si tiene variantes
        $itemRes = Http::withToken($token)->get("https://api.mercadolibre.com/items/{$ml_id}?attributes=variations");

        if (!$itemRes->ok()) {
            $this->markAsError("Error al obtener item ML: " . $itemRes->status());
            return false;
        }

        $item = $itemRes->json();
        $has_variants = !empty($item['variations']);

        // Publicación SIN variantes
        if (!$has_variants) {
            $put = Http::withToken($token)->put("https://api.mercadolibre.com/items/{$ml_id}", [
                'available_quantity' => $stock
            ]);

            if ($put->ok()) {
                $this->markAsSynced();
                $this->sync_log = "Stock actualizado SIN variantes: $stock";
                $this->save();
                return true;
            } else {
                $this->markAsError("Error ML (sin variantes): " . $put->status() . " - " . $put->body());
                return false;
            }
        }

        // Publicación CON variantes
        if (!$variation_id) {
            $this->markAsError("Falta variation_id para publicación con variantes");
            return false;
        }

        $variacion = collect($item['variations'])->firstWhere('id', $variation_id);

        if (!$variacion) {
            $this->markAsError("Variación {$variation_id} no encontrada en ML");
            return false;
        }

        // FULL: no editable
        if (!empty($variacion['inventory_id'])) {
            $this->markAsSynced();
            $this->sync_log = "Stock FULL (no editable)";
            $this->save();
            return false;
        }

        $put = Http::withToken($token)->put(
            "https://api.mercadolibre.com/items/{$ml_id}/variations/{$variation_id}",
            ['available_quantity' => $stock]
        );

        if ($put->ok()) {
            $this->markAsSynced();
            $this->sync_log = "Stock actualizado CON variantes: $stock";
            $this->save();
            return true;
        } else {
            $this->markAsError("Error ML (con variantes): " . $put->status() . " - " . $put->body());
            return false;
        }

    } catch (\Throwable $e) {
        $this->markAsError("Excepción: " . $e->getMessage());
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
public function markAsSynced(): void
{
    $this->sync_status = 'S';
    $this->sync_log = 'Sincronizado correctamente con ML';
    $this->save();
}

/**
 * Marca un error de sincronización.
 */
public function markAsError(string $message): void
{
    $this->sync_status = 'E';
    $this->sync_log = $message;
    $this->save();
}

}





   

