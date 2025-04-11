<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RutasProduccion extends Model
{
    protected $table = 'Rutas_produccion';
    public $timestamps = false;
                                                                                                                                                                                                                                                                                                                                                    protected $fillable = ['cod_ruta', 'denom_ruta', 'anulado', 'id'];

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
  'denom_ruta' => 
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
  'fecha_baja' => 
  array (
    'type' => 'datetime',
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
  'fechaAlta' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
);
}
public function pasos()
{
    return $this->hasMany(PasosRutasProduccion::class, 'cod_ruta', 'cod_ruta');
}


}