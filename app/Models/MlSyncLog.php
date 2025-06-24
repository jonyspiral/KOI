<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlSyncLog extends Model
{
    protected $fillable = [
        'ml_variante_id',
        'campo',
        'exito',
        'mensaje',
    ];

    public function variante()
    {
        return $this->belongsTo(MlVariante::class, 'ml_variante_id');
    }
}
