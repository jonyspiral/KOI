<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlVariante extends Model
{
    protected $table = 'ml_variantes';

    protected $fillable = [
         'ml_id',
        'variation_id',
        
        'modelo',
        'seller_sku',
        'color',
        'talle',
        'precio',
        'stock',
        'seller_custom_field_actual',
        'var_sku_sugerido',
        'nuevo_seller_custom_field',
        'sincronizado',
        'raw_json',
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];

    public function publicacion()
    {
        return $this->hasOne(MlPublicacion::class, 'ml_id', 'ml_id');
    }
}





   

