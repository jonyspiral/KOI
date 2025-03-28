<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class SeccionesProduccion extends Model
{
    protected $table = 'secciones_produccion';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_seccion', 'ejecucion', 'denom_seccion', 'denom_corta', 'unid_med_cap_prod', 'interrumpible', 'cap_prod_hora_hombre', 'cap_prod_turno_maq', 'cap_prod_turno_pers', 'cap_prod_diaria_seccion', 'hombres_planta_cant', 'hombres_temp', 'cuello_de_botella', 'limite_cap_hora_cuello', 'ubicacion_geog', 'cod_responsable_seccion', 'cod_resp_sustituto', 'turnos', 'anulado', 'horas', 'hora_inicio', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'color', 'impresion_stickers', 'jerarquia_seccion', 'tiene_subordinadas', 'subordinada_de_seccion', 'muestra_materiales', 'ingresa_al_stock', 'imprime_tarea_tipo', 'semielaborada', 'imprime_seccion', 'fase', 'fechaAlta', 'fechaBaja', 'cod_almacen_default'];
}
