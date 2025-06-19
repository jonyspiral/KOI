<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // ✅ Tabla explícita
    protected $primaryKey = 'id'; // Clave primaria lógica
    

    public $timestamps = true; // O false si no usás created_at / updated_at

        protected $fillable = ['id', 'name', 'nombre_completo', 'email', 'aplicacion_default', 'es_admin', 'password'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Meta campos para ABM Creator
    public static function fieldsMeta(): array
    {
        return [
            'id' => ['type' => 'key', 'primary' => true],
            'name' => ['type' => 'text', 'label' => 'Nombre'],
            'nombre_completo' => ['type' => 'text', 'label' => 'Nombre completo'],
            'email' => ['type' => 'email', 'label' => 'Correo electrónico'],
            'aplicacion_default' => ['type' => 'text', 'label' => 'Aplicación por defecto'],
            'es_admin' => ['type' => 'bool', 'label' => 'Es administrador'],
            'password' => ['type' => 'password', 'label' => 'Contraseña', 'hideInIndex' => true],
        ];
    }
}
