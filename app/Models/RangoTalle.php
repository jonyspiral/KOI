<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RangoTalle extends Model
{
    protected $table = 'rango_talles';
    public $timestamps = false;
    protected $fillable = ['cod_rango', 'denom_rango', 'punto', 'posic_1', 'posic_2', 'posic_3', 'posic_4', 'posic_5', 'posic_6', 'posic_7', 'posic_8', 'posic_9', 'posic_10', 'posic_11', 'posic_12', 'posic_13', 'posic_14', 'posic_15', 'posic_16', 'posic_17', 'posic_18', 'posic_19', 'posic_20', 'anulado', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'cod_curva', 'tramos_escala', 'cod_rango_nro', 'fechaAlta', 'fechaBaja', 'usa_1', 'usa_2', 'usa_3', 'usa_4', 'usa_5', 'usa_6', 'usa_7', 'usa_8', 'usa_9', 'usa_10', 'created_at', 'updated_at', 'sync_status'];
}
