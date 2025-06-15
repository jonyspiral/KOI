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
        'category_id',
        'logistic_type',
        'family_id',
        'family_name',
        'raw_json',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];

    public function variantes()
    {
        return $this->hasMany(MlVariante::class, 'ml_id', 'ml_id');
    }
    public function mlVariantes()
    {
        return $this->hasMany(MlVariante::class, 'ml_id', 'ml_id');
    }
     /* Devuelve si esta publicación usa logística FULL.
     */
    public function isFull()
    {
        return $this->logistic_type === 'fulfillment';
    }

}

