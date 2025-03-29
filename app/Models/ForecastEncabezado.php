<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastEncabezado extends Model
{
    protected $table = 'Forecast_encabezado';
    public $timestamps = false;
    protected $fillable = ['IdForecast', 'Denom_Forecast', 'Autor', 'Autoriza', 'aprobado', 'anulado', 'Observaciones', 'created_at', 'updated_at', 'sync_status'];
}
