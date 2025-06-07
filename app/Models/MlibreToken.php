<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlibreToken extends Model
{
    protected $table = 'mlibre_tokens';

    protected $fillable = [
        'user_id',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    public $timestamps = false;
}
