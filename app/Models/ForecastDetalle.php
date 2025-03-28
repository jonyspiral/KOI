<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastDetalle extends Model
{
    protected $table = 'Forecast_detalle';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'precio_lista', 'cod_ruta'];
}
