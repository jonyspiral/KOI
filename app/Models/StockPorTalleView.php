<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPorTalleView extends Model
{
    protected $table = 'stock_01_14_20_por_talle_v';
    protected $connection = 'sqlsrv_koi';

    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
}
