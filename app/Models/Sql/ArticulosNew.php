<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class ArticulosNew extends Model
{
    protected $table = 'articulos_new';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_articulo'];
}
