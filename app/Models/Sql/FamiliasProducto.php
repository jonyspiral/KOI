<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class FamiliasProducto extends Model
{
    protected $table = 'familias_producto';
    protected $primaryKey = null; // Clave compuesta, gestionada por KOI
    public static array $primaryKeySql = ['id'];
    public $timestamps = false;
    public $incrementing = false;
    protected $connection = 'sqlsrv_koi';
    protected $fillable = ['id'];

    public static function fieldsMeta()
    {
        return array (
  'id' => 
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
  'descripcion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
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
  'cod_usuario' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_alta' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_baja' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_ultima_mod' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_id' => 
    array (
      'columns' => 
      array (
        0 => 'id',
      ),
      'unique' => true,
    ),
  ),
);
    }
}
