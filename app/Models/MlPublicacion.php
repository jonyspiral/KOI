<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlPublicacion extends Model
{
    protected $table = 'ml_publicaciones';

protected $fillable = [
        'ml_id',
        'ml_name',
        'ml_reference',
        'ml_description',
        'precio',
        'stock',
        'status',
        'category_id',
        'logistic_type',
        'family_id',
        'family_name',
        'raw_json',
    ];

    protected $casts = [
        'raw_json' => 'array',
    ];

    public function variantes()
    {
        return $this->hasMany(MlVariante::class, 'ml_id', 'ml_id');
    }
}

