<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutasProduccion extends Model
{
    protected $table = 'Rutas_produccion';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_ruta'];
                protected $fillable = ['cod_ruta', 'denom_ruta', 'anulado', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_ruta' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_ruta' => 
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
  'fecha_baja' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_ultima_modificacion' => 
  array (
    'type' => 'datetime',
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
  'fechaAlta' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_ruta' => 
    array (
      'columns' => 
      array (
        0 => 'cod_ruta',
      ),
      'unique' => true,
    ),
  ),
);
    }
}
