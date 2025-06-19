<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class TipoProductoStock extends Model
{
    protected $table = 'Tipo_producto_Stock';
    protected $primaryKey = null; // Clave compuesta, gestionada por KOI
    public static array $primaryKeySql = ['id_tipo_producto_stock'];
    public $timestamps = false;
    public $incrementing = false;
    protected $connection = 'sqlsrv_koi';
    protected $fillable = ['id_tipo_producto_stock', 'denom_tipo_producto', 'id_tipo_producto_stock_nro', 'nombre_catalogo', 'mostrar_en_catalogo', 'exclusivo_catalogo', 'descuento_porc', 'indices', 'created_at', 'updated_at', 'sync_status', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'id_tipo_producto_stock' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_tipo_producto' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'id_tipo_producto_stock_nro' => 
  array (
    'type' => 'smallint',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'nombre_catalogo' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'mostrar_en_catalogo' => 
  array (
    'type' => 'char',
    'nullable' => false,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'exclusivo_catalogo' => 
  array (
    'type' => 'char',
    'nullable' => false,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'descuento_porc' => 
  array (
    'type' => 'real',
    'nullable' => false,
    'default' => '(0)',
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_id_tipo_producto_stock' => 
    array (
      'columns' => 
      array (
        0 => 'id_tipo_producto_stock',
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
