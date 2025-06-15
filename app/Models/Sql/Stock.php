<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';
    protected $primaryKey = null; // Clave compuesta, gestionada por KOI
    public static array $primaryKeySql = ['cod_almacen', 'cod_articulo', 'cod_color_articulo'];
    public $timestamps = false;
    public $incrementing = false;
    protected $connection = 'sqlsrv_encinitas';
    protected $fillable = ['cod_almacen', 'cod_articulo', 'cod_color_articulo', 'cantidad', 'cant_1', 'cant_2', 'cant_3', 'cant_4', 'cant_5', 'cant_6', 'cant_7', 'cant_8', 'cant_9', 'cant_10', 'indices', 'created_at', 'updated_at', 'sync_status', 'id'];

    public static function obtenerCantidadPorPosicion($codArticulo, $codColor, $posicion, $codAlmacen = '01')
{
    $stock = self::whereRaw("CAST(cod_articulo AS VARCHAR) = '$codArticulo'")
        ->whereRaw("CAST(cod_color_articulo AS VARCHAR) = '$codColor'")
        ->whereRaw("CAST(cod_almacen AS VARCHAR) = '$codAlmacen'")
        ->first();

    return $stock ? $stock["cant_{$posicion}"] ?? 0 : 0;
}

    public static function fieldsMeta()
    {
        return array (
  'cod_almacen' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
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
  'cantidad' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_1' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_2' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_3' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_4' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_5' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_6' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_7' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_8' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_9' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cant_10' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_almacen_cod_articulo_cod_color_articulo' => 
    array (
      'columns' => 
      array (
        0 => 'cod_almacen',
        1 => 'cod_articulo',
        2 => 'cod_color_articulo',
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
