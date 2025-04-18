<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurvasPorArticulo extends Model
{
    protected $table = 'curvas_por_articulo';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_articulo', 'cod_color_articulo', 'cod_curva'];
    protected $fillable = ['cod_articulo', 'cod_color_articulo', 'cod_curva', 'id'];

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
  'cod_color_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'cod_curva' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'indices' => 
  array (
    'idx_unico_cod_articulo_cod_color_articulo_cod_curva' => 
    array (
      'columns' => 
      array (
        0 => 'cod_articulo',
        1 => 'cod_color_articulo',
        2 => 'cod_curva',
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
