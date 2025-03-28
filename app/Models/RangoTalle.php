<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RangoTalle extends Model
{
    protected $table = 'rango_talles';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
