<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposProductoLineaToSkuVariantes extends Migration
{
    public function up()
    {
        Schema::table('sku_variantes', function (Blueprint $table) {
            $table->string('id_tipo_producto_stock')->nullable()->after('stock_fulfillment');
            $table->string('cod_linea')->nullable()->after('id_tipo_producto_stock');
        });
    }

    public function down()
    {
        Schema::table('sku_variantes', function (Blueprint $table) {
            $table->dropColumn(['id_tipo_producto_stock', 'cod_linea']);
        });
    }
}
