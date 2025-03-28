<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class PasosRutasProduccion extends Model
{
    protected $table = 'Pasos_rutas_produccion';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
