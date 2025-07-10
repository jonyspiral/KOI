<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RubrosIva extends Model
{
    protected $table = 'rubros_iva';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_rubro_iva'];
    protected $fillable = ['cod_rubro_iva', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_rubro_iva' => 
  array (
    'type' => 'int identity',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'nombre' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => false,
  ),
  'anulado' => 
  array (
    'type' => 'char',
    'nullable' => false,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'columna_iva' => 
  array (
    'type' => 'smallint',
    'nullable' => false,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_rubro_iva' => 
    array (
      'columns' => 
      array (
        0 => 'cod_rubro_iva',
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
