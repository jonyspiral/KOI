<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkuVariante extends Model
{
    protected $table = 'view_sku_variantes';

    public $incrementing = false;
    public $timestamps = false;

    // Esta vista no tiene clave primaria real, pero Laravel necesita una
    protected $primaryKey = 'var_sku';
    protected $keyType = 'string';

    public static $sincronizable = false;

    protected $fillable = [
        'cod_articulo',
        'cod_color_articulo',
        'sku',
        'var_sku',
        'ml_name',
        'color',
        'talle',
        'precio',
        'stock',
        'created_at',
        'updated_at',
    ];

    public static function fieldsMeta()
    {
        return [
            'cod_articulo' => ['type' => 'varchar', 'primary' => false],
            'cod_color_articulo' => ['type' => 'varchar', 'primary' => false],
            'sku' => ['type' => 'varchar', 'primary' => false],
            'var_sku' => ['type' => 'varchar', 'primary' => true],
            'ml_name' => ['type' => 'varchar', 'primary' => false],
            'color' => ['type' => 'varchar', 'primary' => false],
            'talle' => ['type' => 'varchar', 'primary' => false],
            'precio' => ['type' => 'float', 'primary' => false],
            'stock' => ['type' => 'int', 'primary' => false],
            'created_at' => ['type' => 'timestamp', 'primary' => false],
            'updated_at' => ['type' => 'timestamp', 'primary' => false],
        ];
    }
}
