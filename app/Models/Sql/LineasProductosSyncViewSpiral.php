<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class LineasProductos extends Model
{
    protected $table = 'lineas_productos';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_linea', 'denom_linea', 'origen', 'lanzamiento_inicial', 'estado_de_linea', 'fecha_de_baja', 'anulado', 'material', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'cod_linea_nro', 'fechaAlta', 'titulo_catalogo', 'titulo_ecommerce'];
}
