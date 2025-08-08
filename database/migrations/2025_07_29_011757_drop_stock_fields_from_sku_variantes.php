<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sku_variantes', function (Blueprint $table) {
            $table->dropColumn([
                'stock',
                'stock_ecommerce',
                'stock_2da',
                'stock_fulfillment',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('sku_variantes', function (Blueprint $table) {
            $table->integer('stock')->nullable();
            $table->integer('stock_ecommerce')->nullable();
            $table->integer('stock_2da')->nullable();
            $table->integer('stock_fulfillment')->nullable();
        });
    }
};
