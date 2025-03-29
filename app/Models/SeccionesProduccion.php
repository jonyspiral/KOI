<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeccionesProduccion extends Model
{
    protected $table = 'secciones_produccion';
    public $timestamps = false;
    protected $fillable = ['cod_seccion', 'ejecucion', 'denom_seccion', 'denom_corta', 'unid_med_cap_prod', 'interrumpible', 'anulado', 'created_at', 'updated_at', 'sync_status'];
}
