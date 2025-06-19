<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMarketplacePricesToSkuVariantesTable extends Migration
{
    public function up()
    {
        Schema::table('sku_variantes', function (Blueprint $table) {
            $table->decimal('ml_price', 10, 2)->nullable()->after('precio');
            $table->decimal('eshop_price', 10, 2)->nullable()->after('ml_price');
            $table->decimal('segunda_price', 10, 2)->nullable()->after('eshop_price');
        });
    }

    public function down()
    {
        Schema::table('sku_variantes', function (Blueprint $table) {
            $table->dropColumn(['ml_price', 'eshop_price', 'segunda_price']);
        });
    }
}
