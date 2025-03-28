<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class ForecastEncabezado extends Model
{
    protected $table = 'Forecast_encabezado';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
