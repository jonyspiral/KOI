<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticulosNew extends Model
{
    protected $table = 'articulos_new';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_articulo'];
                                                                                                        protected $fillable = ['cod_articulo', 'denom_articulo', 'descripcion_larga', 'precio_unitario', 'cantidad_stock', 'disponible', 'tipo_articulo', 'categoria_id', 'fecha_lanzamiento', 'email_contacto', 'url_manual', 'color_preferido', 'telefono_fabrica', 'archivo_manual', 'created_at', 'updated_at', 'sync_status', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => false,
  ),
  'descripcion_larga' => 
  array (
    'type' => 'text',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_unitario' => 
  array (
    'type' => 'float',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cantidad_stock' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'disponible' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'tipo_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'categoria_id' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_lanzamiento' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'email_contacto' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'url_manual' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'color_preferido' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'telefono_fabrica' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'archivo_manual' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_articulo' => 
    array (
      'columns' => 
      array (
        0 => 'cod_articulo',
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
