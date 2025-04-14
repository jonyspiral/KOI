<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'Marcas';
    protected $primaryKey = null; // Clave compuesta, gestionada por KOI
    public static array $primaryKeySql = ['cod_marca'];
    public $timestamps = false;
    public $incrementing = false;
    protected $connection = 'sqlsrv_koi';
    protected $fillable = ['cod_marca'];

    public static function fieldsMeta()
    {
        return array (
  'cod_marca' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'cod_cliente' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_marca' => 
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
  'cod_prov' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'logo' => 
  array (
    'type' => 'nvarchar',
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
  'fechaBaja' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_marca' => 
    array (
      'columns' => 
      array (
        0 => 'cod_marca',
      ),
      'unique' => true,
    ),
  ),
);
    }
}
