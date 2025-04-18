<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horma extends Model
{
    protected $table = 'hormas';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_horma'];
    protected $fillable = ['cod_horma', 'denom_horma', 'talles_desde', 'talles_hasta', 'punto', 'activa', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_horma' => 
  array (
    'type' => 'char',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_horma' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'talles_desde' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'talles_hasta' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'punto' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'color_externo' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'diseñador' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fabricante' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'activa' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'observaciones' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'incorporada_fecha' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'desactivada_fecha' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'decidio_retirar' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_horma' => 
    array (
      'columns' => 
      array (
        0 => 'cod_horma',
      ),
      'unique' => true,
    ),
  ),
);
    }
}
