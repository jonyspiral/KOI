<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horma extends Model
{
    protected $table = 'hormas';
    public $timestamps = false;
    protected $fillable = ['cod_horma', 'denom_horma', 'talles_desde', 'talles_hasta', 'punto', 'color_externo', 'diseñador', 'fabricante', 'activa', 'observaciones', 'incorporada_fecha', 'desactivada_fecha', 'decidio_retirar'];
}
