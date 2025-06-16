<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineasProducto extends Model
{
    protected $table = 'lineas_productos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_linea'];
        protected $fillable = ['cod_linea', 'denom_linea', 'cod_linea_nro', 'titulo_catalogo', 'titulo_ecommerce', 'created_at', 'updated_at', 'sync_status', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_linea' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_linea' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'origen' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'lanzamiento_inicial' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'estado_de_linea' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_de_baja' => 
  array (
    'type' => 'datetime',
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
  'material' => 
  array (
    'type' => 'varchar',
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
  'cod_linea_nro' => 
  array (
    'type' => 'int',
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
  'titulo_catalogo' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'titulo_ecommerce' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_denom_linea' => 
    array (
      'columns' => 
      array (
        0 => 'denom_linea',
      ),
      'unique' => true,
    ),
    'idx_unico_cod_linea' => 
    array (
      'columns' => 
      array (
        0 => 'cod_linea',
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
