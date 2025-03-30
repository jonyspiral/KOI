<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'articulos';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'cod_ruta', 'cod_linea', 'cod_marca', 'cod_rango', 'denom_articulo', 'vigente', 'cod_horma', 'naturaleza', 'cod_familia_producto', 'denom_articulo_largo', 'created_at', 'updated_at', 'sync_status'];
}
