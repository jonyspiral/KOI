<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticulosNew extends Model
{
    protected $table = 'articulos_new';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_articulo'];
        protected $fillable = ['cod_articulo', 'denom_articulo', 'created_at', 'updated_at', 'sync_status', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_articulo' => 
    array (
      'columns' => 
      array (
        0 => 'cod_articulo',
      ),
      'unique' => true,
    ),
  ),
  'created_at' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'updated_at' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'sync_status' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'id' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
);
    }
}
