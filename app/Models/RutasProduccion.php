<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutasProduccion extends Model
{
    protected $table = 'Rutas_produccion';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = ['cod_ruta', 'denom_ruta', 'anulado', 'created_at', 'updated_at', 'sync_status'];
}