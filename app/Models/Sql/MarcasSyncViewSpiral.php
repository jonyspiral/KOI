<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class MarcasSyncViewSpiral extends Model
{
    protected $table = 'Marcas_syncViewSpiral';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_marca', 'cod_cliente', 'denom_marca', 'anulado', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'cod_prov', 'logo', 'fechaAlta', 'fechaBaja'];
}
