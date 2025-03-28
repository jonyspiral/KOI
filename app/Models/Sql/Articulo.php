<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table = 'articulos';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_articulo', 'cod_variante', 'cod_matris', 'corte', 'cod_ruta', 'cod_linea', 'cod_marca', 'cod_rango', 'cod_cliente', 'denom_articulo_abreviada', 'denom_articulo', 'material_predomina', 'origen', 'fabricante', 'vigente', 'fecha_de_baja', 'forma_comercializacion', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'diseño', 'precio_lista', 'precio_lista_mayor', 'precio_distribuidor', 'fecha_precio', 'rubro', 'fecha_lanzamiento', 'tamaño', 'packaging', 'cod_tamanio', 'cod_prov', 'cod_art_en_proveed', 'colores_elenco', 'cod_tempo', 'cod_usuarios', 'cod_tipo', 'precio_recargado', 'cod_mat_predom', 'cod_horma', 'categoria', 'trazable', 'cod_material', 'cod_color', 'naturaleza', 'calidad', 'precio_costo', 'cod_material_articulo', 'tipo_proceso', 'cod_variante_proceso', 'cod_compuesto_articulo', 'cod_empaque', 'combinable', 'cod_articulo_combinacion', 'cod_variante_combinacion', 'corte_combinacion', 'temporada', 'mercado', 'estacion', 'linea_verano', 'linea_invierno', 'linea_indistinta', 'target', 'plantilla', 'fidelidad', 'cod_linea_combina', 'articulo_de_origen', 'madurez', 'cod_articulo_largo', 'unidad', 'reventa', 'aprob_disenio', 'aprob_produccion', 'fecha_nacimiento', 'cod_matris1', 'vend_1', 'vend_2', 'vend_3', 'vend_4', 'vend_5', 'vend_6', 'vend_7', 'contribucion_marginal', 'dificultad_produccion', 'precio_mano_obra', 'precio_lista_aumento', 'fechaAlta', 'cod_rubro_iva', 'utiliza_codigo_barra_cliente', 'cod_familia_producto', 'ml_denominacion', 'ecommerce_reference', 'ecommerce_name', 'denom_articulo_largo'];
}
