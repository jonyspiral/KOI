<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horma extends Model
{
    protected $table = 'hormas';
    public $timestamps = false;
    protected $fillable = ['cod_horma', 'denom_horma', 'talles_desde', 'talles_hasta', 'punto', 'observaciones', 'created_at', 'updated_at', 'sync_status'];
}
