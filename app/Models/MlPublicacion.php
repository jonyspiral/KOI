<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlPublicacion extends Model
{
    protected $table = 'ml_publicaciones';

    protected $fillable = [
        'ml_id',
        'ml_reference',
        'ml_name',
        'ml_description',
        'mlibre_precio',
        'mlibre_stock',
        'status',
        'raw_json',
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];
    public function variantes()
{
    return $this->hasMany(MlVariante::class, 'ml_publicacion_id');
}

}
