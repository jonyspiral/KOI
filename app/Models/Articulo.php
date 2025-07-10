<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'articulos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_articulo'];
    protected $fillable = ['cod_articulo', 'id','cod_linea', 'cod_marca', 'cod_ruta', 'cod_rango', 'cod_cliente', 'denom_articulo', 'fabricante', 'vigente', 'forma_comercializacion', 'rubro', 'cod_prov', 'cod_tipo', 'cod_horma', 'categoria', 'cod_material', 'cod_color', 'naturaleza', 'calidad', 'precio_costo', 'cod_material_articulo', 'cod_articulo_largo', 'unidad', 'aprob_disenio', 'aprob_produccion', 'denom_articulo_largo'];


    public function coloresPorArticulo()
    {
        return $this->hasMany(\App\Models\ColoresPorArticulo::class, 'cod_articulo', 'cod_articulo');
    }
    public function familia()
    {
        return $this->belongsTo(\App\Models\FamiliasProducto::class, 'cod_familia_producto', 'id');
        }
 public function linea()
{
    return $this->belongsTo(\App\Models\LineasProducto::class, 'cod_linea', 'cod_linea');
}


    public function ruta()
    {
        return $this->belongsTo(\App\Models\RutasProduccion::class, 'cod_ruta');
    }

    public function rango()
    {
        return $this->belongsTo(\App\Models\RangoTalle::class, 'cod_rango');
    }

    public function horma()
    {
        return $this->belongsTo(\App\Models\Horma::class, 'cod_horma');
    }

    public function marca()
    {
        return $this->belongsTo(\App\Models\Marca::class, 'cod_marca');
    }

    public function rubroIva()
    {
        return $this->belongsTo(\App\Models\RubrosIva::class, 'cod_rubro_iva');
    }


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
  'cod_ruta' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_linea' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_marca' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_rango' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_cliente' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fabricante' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vigente' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'S\')',
    'primary' => false,
  ),
  'forma_comercializacion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
   'rubro' => 
  array (
    'type' => 'nvarchar',
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
  'cod_tipo' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
    'cod_horma' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'categoria' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
   'cod_material' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'naturaleza' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'calidad' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_costo' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_material_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_articulo_largo' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'unidad' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'aprob_disenio' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'aprob_produccion' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_articulo_largo' => 
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
