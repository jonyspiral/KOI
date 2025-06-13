<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



  class MlVariante extends Model
{
 protected $fillable = [
        'ml_id',
        'variation_id',
        'product_number',
        'seller_custom_field',
        'titulo',      
        'talle',
        'color',
        'modelo',
        'seller_sku',
        'precio',
        'stock',
        'stock_flex',
        'stock_full',
        'seller_custom_field_actual',
        'var_sku_sugerido',
        'nuevo_seller_custom_field',
        'sincronizado',
        'family_id',
        'raw_json',
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
}





   

