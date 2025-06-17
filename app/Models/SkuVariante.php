<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\StockSkuService;


class SkuVariante extends Model
{
    protected $table = 'sku_variantes';

    public $incrementing = false;
    public $timestamps = false;

    protected $primaryKey = 'var_sku';
    protected $keyType = 'string';

    public static $sincronizable = false;

    protected $fillable = [
        'id',
        'sku',
        'var_sku',
        'ml_name',
        'cod_articulo',
        'cod_color_articulo',
        'familia',
        'color',
        'talle',
        'precio',
        'stock',
        'stock_ecommerce',
        'stock_2da',
        'stock_fulfillment',
        'id_tipo_producto_stock',
        'cod_linea',
        'sync_status',
        'sync_log',
        'created_at',
        'updated_at',
    ];

    public static function fieldsMeta()
    {
        return [
            'cod_articulo'           => ['type' => 'varchar', 'primary' => false],
            'cod_color_articulo'     => ['type' => 'varchar', 'primary' => false],
            'sku'                    => ['type' => 'varchar', 'primary' => false],
            'var_sku'                => ['type' => 'varchar', 'primary' => true],
            'ml_name'                => ['type' => 'varchar', 'primary' => false],
            'color'                  => ['type' => 'varchar', 'primary' => false],
            'talle'                  => ['type' => 'varchar', 'primary' => false],
            'precio'                 => ['type' => 'float', 'primary' => false],
            'stock'                  => ['type' => 'int', 'primary' => false],
            'stock_ecommerce'        => ['type' => 'int', 'primary' => false],
            'stock_2da'              => ['type' => 'int', 'primary' => false],
            'stock_fulfillment'      => ['type' => 'int', 'primary' => false],
            'id_tipo_producto_stock' => ['type' => 'varchar', 'primary' => false],
            'cod_linea'              => ['type' => 'varchar', 'primary' => false],
            'sync_status'            => ['type' => 'varchar', 'primary' => false],
            'sync_log'               => ['type' => 'text', 'primary' => false],
            'created_at'             => ['type' => 'timestamp', 'primary' => false],
            'updated_at'             => ['type' => 'timestamp', 'primary' => false],
        ];
    }



    public function tipoProductoStock()
{
    return $this->belongsTo(\App\Models\TipoProductoStock::class, 'id_tipo_producto_stock', 'id_tipo_producto_stock');
}

public function lineaProducto()
{
    return $this->belongsTo(\App\Models\LineasProducto::class, 'cod_linea', 'cod_linea');
}
public function getTipoProductoAttribute()
{
    return $this->tipoProductoStock?->denom_tipo_producto;
}


public function getStockAttribute()
    {
        return StockSkuService::obtenerStockSKU(
            $this->cod_articulo,
            $this->cod_color_articulo,
            $this->talle,
            ['01', '14']
        );
    }

    /**
     * Devuelve el stock de 2da selección (almacén 02).
     */
    public function getStock2daAttribute()
    {
        return StockSkuService::obtenerStockSKU(
            $this->cod_articulo,
            $this->cod_color_articulo,
            $this->talle,
            ['02']
        );
    }
    /**
     * Devuelve el stock de FULL si tenés almacén definido como 20 o similar.
     * Podés comentar o ajustar según el uso real.
     */
    public function getStockFullAttribute()
    {
        return StockSkuService::obtenerStockSKU(
            $this->cod_articulo,
            $this->cod_color_articulo,
            $this->talle,
            ['20'] // solo si usás almacén 20 para FULL
        );
    }

}
