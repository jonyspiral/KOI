<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeccionesProduccion extends Model
{
    protected $table = 'secciones_produccion';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public static $sincronizable = true;
    public static array $primaryKeySql = ['cod_seccion'];
                                    protected $fillable = ['cod_seccion', 'ejecucion', 'denom_seccion', 'denom_corta', 'unid_med_cap_prod', 'interrumpible', 'anulado', 'color', 'impresion_stickers', 'jerarquia_seccion', 'tiene_subordinadas', 'subordinada_de_seccion', 'id'];

    public static function fieldsMeta()
    {
        return array (
  'cod_seccion' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'ejecucion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_seccion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'denom_corta' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'unid_med_cap_prod' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'interrumpible' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cap_prod_hora_hombre' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cap_prod_turno_maq' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cap_prod_turno_pers' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cap_prod_diaria_seccion' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'hombres_planta_cant' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'hombres_temp' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cuello_de_botella' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'limite_cap_hora_cuello' => 
  array (
    'type' => 'real',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ubicacion_geog' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_responsable_seccion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_resp_sustituto' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'turnos' => 
  array (
    'type' => 'int',
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
  'horas' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'hora_inicio' => 
  array (
    'type' => 'int',
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
  'color' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'impresion_stickers' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'jerarquia_seccion' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'tiene_subordinadas' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'subordinada_de_seccion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'muestra_materiales' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ingresa_al_stock' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'imprime_tarea_tipo' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'semielaborada' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'imprime_seccion' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fase' => 
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
  'fechaBaja' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_almacen_default' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'indices' => 
  array (
    'idx_unico_cod_seccion' => 
    array (
      'columns' => 
      array (
        0 => 'cod_seccion',
      ),
      'unique' => true,
    ),
  ),
);
    }
}
