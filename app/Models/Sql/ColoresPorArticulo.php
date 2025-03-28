<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class ColoresPorArticulo extends Model
{
    protected $table = 'colores_por_articulo';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
