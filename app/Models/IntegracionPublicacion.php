<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegracionPublicacion extends Model
{
    protected $table = 'integracion_publicaciones';

    protected $fillable = [
        'cod_articulo',
        'cod_color_articulo',
        'plataforma',
        'external_id',
        'status',
        'sync_price',
        'sync_stock',
        'fecha_ultima_sync',
        'observaciones',
    ];

    public $timestamps = true;
}

