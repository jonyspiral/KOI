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
 * Sincroniza precio y stock desde el SKU si no son manuales.
 */

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





   

