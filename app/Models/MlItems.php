<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlItems extends Model
{
    protected $table = 'ecomexperts_articulos_update_v';
    protected $connection = 'sqlsrv_koi';

    public $incrementing = false;
    public $timestamps = false;

    // No se permite escritura: view de solo lectura
}

