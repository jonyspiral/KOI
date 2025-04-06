<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarcasSyncViewSpiral extends Model
{
    protected $table = 'Marcas_syncViewSpiral';
    public $timestamps = false;
    protected $fillable = ['cod_marca', 'cod_cliente', 'denom_marca', 'anulado', 'fecha_ultima_modificacion', 'autor_ultima_modificacion', 'cod_prov', 'logo', 'fechaAlta', 'fechaBaja', 'created_at', 'updated_at', 'sync_status'];
}
