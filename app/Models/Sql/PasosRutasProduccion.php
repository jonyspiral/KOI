<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class PasosRutasProduccion extends Model
{
    protected $table = 'Pasos_rutas_produccion';
    protected $primaryKey = 'id';
    public $timestamps = false;
    public $incrementing = false;
    public static array $primaryKeySql = ['cod_ruta', 'cod_paso', 'sub_paso', 'cod_seccion'];
    protected $connection = 'sqlsrv_koi';
    protected $fillable = ['cod_ruta', 'cod_paso', 'sub_paso', 'cod_seccion'];

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
  'cod_paso' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'sub_paso' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'cod_seccion' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'secciones_subordinadas' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_seccion_subordinada' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'ejecucion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'anulado' => 
  array (
    'type' => 'nvarchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_baja' => 
  array (
    'type' => 'smalldatetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'duracion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'demora' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'punto_programacion' => 
  array (
    'type' => 'int',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'capacidad_produccion_hora' => 
  array (
    'type' => 'real',
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
  'imprimir_orden_f2' => 
  array (
    'type' => 'char',
    'nullable' => true,
    'default' => '(\'N\')',
    'primary' => false,
  ),
  'ordenamiento_hoja_tarea' => 
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
  'fechaUltimaMod' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
);
    }
}
