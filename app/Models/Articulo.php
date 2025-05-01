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
                                protected $fillable = ['cod_articulo', 'cod_ruta', 'cod_linea', 'cod_marca', 'cod_rango', 'denom_articulo', 'fabricante', 'vigente', 'forma_comercializacion', 'cod_prov', 'cod_horma', 'unidad', 'cod_rubro_iva', 'cod_familia_producto', 'denom_articulo_largo', 'updated_at', 'sync_status', 'id'];

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
  'cod_variante' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_matris' => 
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
  'denom_articulo_abreviada' => 
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
  'material_predomina' => 
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
  'fecha_de_baja' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'forma_comercializacion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_ultima_modificacion' => 
  array (
    'type' => 'smalldatetime',
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
  'diseño' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_lista' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_lista_mayor' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_distribuidor' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_precio' => 
  array (
    'type' => 'smalldatetime',
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
  'fecha_lanzamiento' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'tamaño' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'packaging' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_tamanio' => 
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
  'cod_art_en_proveed' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'colores_elenco' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_tempo' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_usuarios' => 
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
  'precio_recargado' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_mat_predom' => 
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
  'trazable' => 
  array (
    'type' => 'nvarchar',
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
  'tipo_proceso' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_variante_proceso' => 
  array (
    'type' => 'varchar',
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
  'cod_empaque' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'combinable' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'cod_articulo_combinacion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_variante_combinacion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'corte_combinacion' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'temporada' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'mercado' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'estacion' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'linea_verano' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'linea_invierno' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'linea_indistinta' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'target' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'plantilla' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fidelidad' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_linea_combina' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'articulo_de_origen' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'madurez' => 
  array (
    'type' => 'char',
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
  'reventa' => 
  array (
    'type' => 'char',
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
  'fecha_nacimiento' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_matris1' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_1' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_2' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_3' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_4' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_5' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_6' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'vend_7' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'contribucion_marginal' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'dificultad_produccion' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_mano_obra' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'precio_lista_aumento' => 
  array (
    'type' => 'real',
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
  'cod_rubro_iva' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => '(1)',
    'primary' => false,
  ),
  'utiliza_codigo_barra_cliente' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_familia_producto' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ml_denominacion' => 
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
