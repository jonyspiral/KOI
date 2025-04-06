<?php

namespace App\Models\Sql;

use Illuminate\Database\Eloquent\Model;

class AcreditarDebitarChequeC extends Model
{
    protected $table = 'acreditar_debitar_cheque_c';
    protected $connection = 'sqlsrv_koi';
    public $timestamps = false;
    protected $fillable = ['cod_acreditar_debitar_cheque', 'empresa', 'tipo', 'fecha', 'observaciones', 'cod_usuario', 'fecha_documento', 'fecha_alta'];

    public static function fieldsMeta()
    {
        return array (
  'cod_acreditar_debitar_cheque' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'empresa' => 
  array (
    'type' => 'int',
    'nullable' => false,
    'default' => NULL,
    'primary' => true,
  ),
  'tipo' => 
  array (
    'type' => 'char',
    'nullable' => false,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'observaciones' => 
  array (
    'type' => 'text',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'cod_usuario' => 
  array (
    'type' => 'varchar',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_documento' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
  'fecha_alta' => 
  array (
    'type' => 'datetime',
    'nullable' => true,
    'default' => NULL,
    'primary' => false,
  ),
);
    }
}