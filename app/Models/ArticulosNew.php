<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticulosNew extends Model
{
    protected $table = 'articulos_new';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'denom_articulo', 'created_at', 'updated_at', 'sync_status'];
}
