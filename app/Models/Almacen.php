<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'Almacenes';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_almacen'];
                                            protected $fillable = ['cod_almacen', 'denom_almacen', 'denom_almacen_mp', 'created_at', 'updated_at', 'sync_status', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_empresa' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_sucursal' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_almacen' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_almacen' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_almacen_mp' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'anulado' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'fecha_baja' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'centro_costos' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'horario' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'direccion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_ultima_modificacion' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'autor_ultima_modificacion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_abrev' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'almacen_mp' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_denom_almacen' => 
    array (
      'columns' => 
      array (
        0 => 'denom_almacen',
      ),
      'unique' => true,
    ),
    'idx_unico_cod_almacen' => 
    array (
      'columns' => 
      array (
        0 => 'cod_almacen',
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
