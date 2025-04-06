<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operadore extends Model
{
    protected $table = 'Operadores';
    public $timestamps = false;
    protected $fillable = ['cod_operador', 'anulado', 'tipo_operador', 'cod_personal', 'cod_proveedor', 'fecha_baja', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'porc_comision_vtas', 'subordinado_a', 'tiene_subordinados', 'tiene_comision', 'comision_variable', 'porc_comis_fija', 'equipo', 'tipo_comision', 'posicion_lista_precios', 'PALABRA_clave', 'muestra_en_liquidacion'];
}
