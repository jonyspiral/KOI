<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasosRutasProduccion extends Model
{
    protected $table = 'Pasos_rutas_produccion';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
