<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class Curva extends Model
{
    protected $table = 'curvas';
    protected $primaryKey = null; // Clave compuesta, gestionada por KOI
    public static array $primaryKeySql = ['cod_curva'];
    public $timestamps = false;
    public $incrementing = false;
    protected $connection = 'sqlsrv_koi';
    protected $fillable = ['cod_curva'];

    public static function fieldsMeta()
    {
        return array (
  'cod_curva' => 
  array (
    'type' => 'int identity',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_curva' => 
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
    'default' => NULL,
    'primary' => false,
  ),
  'tipo_curva' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'total_pares_curva' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_1' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_2' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_3' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_4' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_5' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_6' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_7' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_8' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_9' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_10' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_11' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_12' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_13' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_14' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'pos_15' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_curva' => 
    array (
      'columns' => 
      array (
        0 => 'cod_curva',
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
