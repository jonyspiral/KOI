<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlVariante extends Model
{
    protected $table = 'ml_variantes';

    protected $fillable = [
        'ml_publicacion_id',
        'sku_',
        'talle',
        'precio',
        'stock',
        'raw_json',
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];

    public function publicacion()
    {
        return $this->belongsTo(MlPublicacion::class, 'ml_publicacion_id');
    }
}
