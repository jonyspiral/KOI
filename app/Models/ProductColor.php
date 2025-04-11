<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $table = 'colores_por_articulo'; // tu tabla real

    protected $primaryKey = 'id'; // cambiá si tu tabla usa otro campo como clave primaria

    public $timestamps = false; // si no usás created_at y updated_at

    protected $fillable = [
        'cod_articulo',
        'denom_color',
        'precio_mayorista',
        // agregá aquí los campos editables
    ];
}
