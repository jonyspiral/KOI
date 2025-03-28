<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class ForecastDetalle extends Model
{
    protected $table = 'Forecast_detalle';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
