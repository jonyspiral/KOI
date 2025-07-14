<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColoresPorArticulo extends Model
{
    protected $table = 'colores_por_articulo';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_articulo', 'cod_color_articulo'];
    protected $fillable = ['cod_articulo', 'cod_color_articulo', 'id','id_tipo_producto_stock'];

    public function tipo_producto_stock()
    {
        return $this->belongsTo(TipoProductoStock::class, 'id_tipo_producto_stock', 'id_tipo_producto_stock');
    }
    // app/Models/ColoresPorArticulo.php

public function articulo()
{
    return $this->belongsTo(\App\Models\Articulo::class, 'cod_articulo', 'cod_articulo');
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
  'cod_color_articulo' => 
  array (
    'type' => 'varchar',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'denom_color' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_variante' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'corte' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vigente' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_de_baja' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_color_abreviada' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_masa' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'disenio' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_minorista_usd' => 
  array (
    'type' => 'numeric',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_mayorista_usd' => 
  array (
    'type' => 'numeric',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_distrib' => 
  array (
    'type' => 'numeric',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_distrib_minorista' => 
  array (
    'type' => 'numeric',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_actualiz_precio' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'muestra_moneda' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'muestra_vip' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'muestra_porcentaje_vip' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_material' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_en_cliente' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denominacion_cliente' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'codigo_de_barras_cliente' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_en_proveedor' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_recargado' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_compuesto_articulo' => 
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
    'default' => '(\'S\')',
    'primary' => false,
  ),
  'aprob_produccion' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'S\')',
    'primary' => false,
  ),
  'fotografia1' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia2' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia3' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia4' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia5' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'id_tipo_producto_stock' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'catalogo' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'stock_temp_ecommerce' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'stock_temp' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'prod_trim_temp' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'catalogo_orden_pagina' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'descuento_color_articulo' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_base' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color_base' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_minimo' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color_cliente' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'importacion_propia' => 
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
  'validacion_stock' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_validacion_stock' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'usuario_valida' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia6' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia7' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fotografia8' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'zoom_lado_interno' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'zoom_puntera' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'zoom_caña' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'zoom_talon' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'texto_lado_interno' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'texto_puntera' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'texto_caña' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'texto_talon' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'texto_varios' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'comercializacion_libre' => 
  array (
    'type' => 'char',
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
  'fechaUltimaMod' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'en_produccion_temp' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'categoria_usuario' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_mp_critico_1' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_mp_critico_2' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_mp_critico_3' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color_mp_critico_1' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color_mp_critico_2' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_color_mp_critico_3' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'utiliza_cb_cliente' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'ecommerce_existe' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'ecommerce_fecha_ultima_sinc' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_nombre' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_info' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_forsale' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'ecommerce_condition' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => '(\'traditional\')',
    'primary' => false,
  ),
  'ecommerce_exclusive' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'ecommerce_featured' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'ecommerce_price1' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => '(0)',
    'primary' => false,
  ),
  'ecommerce_price2' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => '(0)',
    'primary' => false,
  ),
  'ecommerce_price3' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => '(\'Undefined\')',
    'primary' => false,
  ),
  'ecommerce_image1' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_cod_category' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'seleccion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'clasificacion_comercial' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'referencia_web_mayorista' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'mlibre_precio' => 
  array (
    'type' => 'numeric',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecomm_especific_price_reduction' => 
  array (
    'type' => 'decimal',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecomm_especific_price_from' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecomm_especific_price_to' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecomm_especific_price_identifier' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_reference' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_name' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ecommerce_description' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ml_description' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ml_name' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ml_reference' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'composition' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_articulo_cod_color_articulo' => 
    array (
      'columns' => 
      array (
        0 => 'cod_articulo',
        1 => 'cod_color_articulo',
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
