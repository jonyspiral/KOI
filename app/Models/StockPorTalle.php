<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPorTalle extends Model
{
    protected $table = 'stock_01_14_20_por_talle_v';

    public $timestamps = false;

    protected $connection = 'sqlsrv_koi';

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'cod_articulo',
        'cod_color_articulo',
        'talle',
        'cantidad',
        'cant_1',
    ];
}
